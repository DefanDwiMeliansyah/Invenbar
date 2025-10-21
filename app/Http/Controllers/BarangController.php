<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Lokasi;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller implements HasMiddleware
{
    /**
     * Get query with lokasi filter applied
     */
    protected function getFilteredQuery()
    {
        $query = Barang::query();
        $user = Auth::user();

        // Jika petugas, filter berdasarkan lokasi_id
        if ($user && $user->isPetugas() && $user->lokasi_id) {
            $query->where('lokasi_id', $user->lokasi_id);
        }

        return $query;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $barangs = $this->getFilteredQuery()
            ->with(['kategori', 'lokasi'])
            ->when($search, function ($query, $search) {
                $query->where('nama_barang', 'like', '%' . $search . '%')
                    ->orWhere('kode_barang', 'like', '%' . $search . '%');
            })
            ->latest()->paginate()->withQueryString();

        // Group barang per unit by kode_barang prefix
        $groupedBarangs = [];
        foreach ($barangs->items() as $barang) {
            if ($barang->mode_input === 'Per Unit') {
                $prefix = $barang->getKodePrefix();
                if (!isset($groupedBarangs[$prefix])) {
                    $groupedBarangs[$prefix] = [];
                }
                $groupedBarangs[$prefix][] = $barang;
            }
        }

        return view('barang.index', compact('barangs', 'groupedBarangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategori = Kategori::all();
        $user = Auth::user();

        // Admin bisa pilih semua lokasi, petugas hanya lokasinya sendiri
        if ($user->isAdmin()) {
            $lokasi = Lokasi::all();
        } else {
            $lokasi = Lokasi::where('id', $user->lokasi_id)->get();
        }

        $barang = new Barang();


        return view('barang.create', compact('barang', 'kategori', 'lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'mode_input' => 'required|in:Masal,Per Unit',
            'kode_barang' => 'required|string|max:50',
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:20',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'sumber' => 'required|in:Pemerintah,Swadaya,Donatur,Mitra',
            'status' => 'required|in:Tersedia,Dipinjam,Rusak,Hilang,Tidak Dapat Dipinjam,Diperbaiki,Perawatan',
            'tanggal_pengadaan' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Validasi lokasi untuk petugas
        if ($user->isPetugas() && $validated['lokasi_id'] != $user->lokasi_id) {
            return back()->withInput()->withErrors(['lokasi_id' => 'Anda hanya dapat menambahkan barang di lokasi Anda.']);
        }

        DB::beginTransaction();

        try {
            $gambarPath = null;
            if ($request->hasFile('gambar')) {
                $gambarPath = $request->file('gambar')->store(null, 'gambar-barang');
            }

            if ($validated['mode_input'] === 'Masal') {
                // Validasi kode barang unique untuk mode Masal
                if (Barang::where('kode_barang', $validated['kode_barang'])->exists()) {
                    throw new \Exception('Kode barang sudah ada.');
                }

                $validated['gambar'] = $gambarPath;
                $validated['status'] = $validated['status'] ?? 'Tersedia';
                Barang::create($validated);
            } else {
                // Mode Per Unit: generate multiple records
                $kodeBarang = $validated['kode_barang'];
                $jumlahUnit = $validated['jumlah'];

                preg_match('/^(.+?)(\d+)$/', $kodeBarang, $matches);

                if (count($matches) < 3) {
                    throw new \Exception('Format kode barang tidak valid. Gunakan format seperti PJTR01');
                }

                $prefix = $matches[1];
                $startNumber = (int)$matches[2];
                $digitLength = strlen($matches[2]);

                // Validasi semua kode yang akan di-generate
                for ($i = 0; $i < $jumlahUnit; $i++) {
                    $currentNumber = $startNumber + $i;
                    $currentKode = $prefix . str_pad($currentNumber, $digitLength, '0', STR_PAD_LEFT);

                    if (Barang::where('kode_barang', $currentKode)->exists()) {
                        throw new \Exception("Kode barang {$currentKode} sudah ada. Gunakan kode awal yang berbeda.");
                    }
                }

                // Create multiple records
                for ($i = 0; $i < $jumlahUnit; $i++) {
                    $currentNumber = $startNumber + $i;
                    $currentKode = $prefix . str_pad($currentNumber, $digitLength, '0', STR_PAD_LEFT);

                    Barang::create([
                        'mode_input' => 'Per Unit',
                        'kode_barang' => $currentKode,
                        'nama_barang' => $validated['nama_barang'],
                        'kategori_id' => $validated['kategori_id'],
                        'lokasi_id' => $validated['lokasi_id'],
                        'jumlah' => 1,
                        'satuan' => $validated['satuan'],
                        'kondisi' => 'Baik',
                        'sumber' => 'Pemerintah',
                        'status' => $validated['status'],
                        'tanggal_pengadaan' => $validated['tanggal_pengadaan'],
                        'gambar' => $gambarPath,
                    ]);
                }
            }

            DB::commit();

            $message = $validated['mode_input'] === 'Masal'
                ? 'Data barang berhasil ditambahkan.'
                : "Berhasil menambahkan {$validated['jumlah']} unit barang.";

            return redirect()->route('barang.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($gambarPath) {
                Storage::disk('gambar-barang')->delete($gambarPath);
            }

            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        $user = Auth::user();

        // Petugas hanya bisa lihat barang di lokasinya
        if ($user->isPetugas() && $barang->lokasi_id != $user->lokasi_id) {
            abort(403, 'Anda tidak memiliki akses ke barang ini.');
        }

        $barang->load(['kategori', 'lokasi']);

        return view('barang.show', compact('barang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        $user = Auth::user();

        // Petugas hanya bisa edit barang di lokasinya
        if ($user->isPetugas() && $barang->lokasi_id != $user->lokasi_id) {
            abort(403, 'Anda tidak memiliki akses ke barang ini.');
        }

        $kategori = Kategori::all();

        // Admin bisa pilih semua lokasi, petugas hanya lokasinya sendiri
        if ($user->isAdmin()) {
            $lokasi = Lokasi::all();
        } else {
            $lokasi = Lokasi::where('id', $user->lokasi_id)->get();
        }

        return view('barang.edit', compact('barang', 'kategori', 'lokasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
    {
        $user = Auth::user();

        // Petugas hanya bisa update barang di lokasinya
        if ($user->isPetugas() && $barang->lokasi_id != $user->lokasi_id) {
            abort(403, 'Anda tidak memiliki akses ke barang ini.');
        }

        $validated = $request->validate([
            'mode_input' => 'required|in:Masal,Per Unit',
            'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'sumber' => 'required|in:Pemerintah,Swadaya,Donatur,Mitra',
            'status' => 'required|in:Tersedia,Dipinjam,Rusak,Hilang,Tidak Dapat Dipinjam,Diperbaiki,Perawatan',
            'tanggal_pengadaan' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Validasi lokasi untuk petugas
        if ($user->isPetugas() && $validated['lokasi_id'] != $user->lokasi_id) {
            return back()->withInput()->withErrors(['lokasi_id' => 'Anda hanya dapat memindahkan barang ke lokasi Anda.']);
        }

        if ($request->hasFile('gambar')) {
            if ($barang->gambar) {
                Storage::disk('gambar-barang')->delete($barang->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store(null, 'gambar-barang');
        }

        $barang->update($validated);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        $user = Auth::user();

        // 🔹 Petugas hanya bisa hapus barang di lokasinya sendiri
        if ($user->isPetugas() && $barang->lokasi_id != $user->lokasi_id) {
            abort(403, 'Anda tidak memiliki akses ke barang ini.');
        }

        // 🔹 Cek apakah barang masih punya peminjaman aktif
        if ($barang->peminjamanDetails()->exists()) {
            return redirect()->back()->with('error', 'Barang ini masih memiliki data peminjaman dan tidak dapat dihapus.');
        }

        // 🔹 Cek apakah barang masih punya riwayat perbaikan/pemeliharaan
        if ($barang->perbaikanPemeliharaans()->exists()) {
            return redirect()->back()->with('error', 'Barang ini masih memiliki data perbaikan atau pemeliharaan dan tidak dapat dihapus.');
        }

        // 🔹 Hapus file gambar jika ada
        if ($barang->gambar) {
            Storage::disk('gambar-barang')->delete($barang->gambar);
        }

        // 🔹 Hapus data barang
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil dihapus.');
    }

    /**
     * Remove all barang with same prefix (Per Unit group)
     */
    public function destroyGroup(Request $request, $prefix)
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            $query = Barang::where('mode_input', 'Per Unit')
                ->where('kode_barang', 'like', $prefix . '%');

            // Filter lokasi untuk petugas
            if ($user->isPetugas()) {
                $query->where('lokasi_id', $user->lokasi_id);
            }

            $barangs = $query->get();

            if ($barangs->isEmpty()) {
                return redirect()->route('barang.index')
                    ->with('error', 'Data barang tidak ditemukan.');
            }

            $count = $barangs->count();

            foreach ($barangs as $barang) {
                if ($barang->gambar) {
                    Storage::disk('gambar-barang')->delete($barang->gambar);
                }
                $barang->delete();
            }

            DB::commit();

            return redirect()->route('barang.index')
                ->with('success', "Berhasil menghapus {$count} unit barang.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('barang.index')
                ->with('error', 'Gagal menghapus data barang: ' . $e->getMessage());
        }
    }

    public function cetaklaporan()
    {
        $user = Auth::user();

        $query = Barang::with(['kategori', 'lokasi'])
            ->orderBy('kode_barang');

        // Filter untuk petugas
        if ($user->isPetugas() && $user->lokasi_id) {
            $query->where('lokasi_id', $user->lokasi_id);
        }

        $barangs = $query->get();

        $groupedBarangs = [];
        foreach ($barangs as $barang) {
            if ($barang->mode_input === 'Per Unit') {
                $prefix = $barang->getKodePrefix();
                $groupedBarangs[$prefix][] = $barang;
            }
        }

        $data = [
            'title' => 'Laporan Data Barang Inventaris',
            'date' => date('d F Y'),
            'barangs' => $barangs,
            'groupedBarangs' => $groupedBarangs
        ];

        $pdf = Pdf::loadView('barang.laporan', $data);

        return $pdf->stream('laporan-inventaris-barang.pdf');
    }

    public static function middleware()
    {
        return [
            new Middleware('permission:manage barang', except: ['destroy']),
            new Middleware('permission:delete barang', only: ['destroy']),
            new Middleware('App\Http\Middleware\CheckLokasiAccess'),
        ];
    }
}
