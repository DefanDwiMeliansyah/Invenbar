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

class BarangController extends Controller implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $barangs = Barang::with(['kategori', 'lokasi'])
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
        $lokasi = Lokasi::all();

        $barang = new Barang();

        return view('barang.create', compact('barang', 'kategori', 'lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mode_input' => 'required|in:Masal,Per Unit',
            'kode_barang' => 'required|string|max:50',
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:20',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|in:Tersedia,Dipinjam,Rusak,Hilang,Tidak Dapat Dipinjam,Diperbaiki,Perawatan',
            'tanggal_pengadaan' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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
        $barang->load(['kategori', 'lokasi']);

        return view('barang.show', compact('barang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        $kategori = Kategori::all();
        $lokasi = Lokasi::all();

        return view('barang.edit', compact('barang', 'kategori', 'lokasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'mode_input' => 'required|in:Masal,Per Unit',
            'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status' => 'required|in:Tersedia,Dipinjam,Rusak,Hilang,Tidak Dapat Dipinjam,Diperbaiki,Perawatan',
            'tanggal_pengadaan' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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
        if ($barang->gambar) {
            Storage::disk('gambar-barang')->delete($barang->gambar);
        }

        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil dihapus.');
    }

    /**
     * Remove all barang with same prefix (Per Unit group)
     */
    public function destroyGroup(Request $request, $prefix)
    {
        DB::beginTransaction();
        
        try {
            $barangs = Barang::where('mode_input', 'Per Unit')
                ->where('kode_barang', 'like', $prefix . '%')
                ->get();

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
        $barangs = Barang::with(['kategori', 'lokasi'])
            ->orderBy('kode_barang')
            ->get();

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
        ];
    }
}