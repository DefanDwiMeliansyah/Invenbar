<?php

namespace App\Http\Controllers;

use App\Models\PerbaikanPemeliharaan;
use App\Models\Barang;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Barryvdh\DomPDF\Facade\Pdf;

class PerbaikanPemeliharaanController extends Controller implements HasMiddleware
{
    /**
     * Get query with lokasi filter applied
     */
    protected function getFilteredQuery()
    {
        $query = PerbaikanPemeliharaan::query();
        $user = Auth::user();

        if ($user && $user->isPetugas() && $user->lokasi_id) {
            $query->whereHas('barang', function ($q) use ($user) {
                $q->where('lokasi_id', $user->lokasi_id);
            });
        }

        return $query;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $filterStatus = $request->status;
        $filterPrioritas = $request->prioritas;
        $filterJenis = $request->jenis;

        $perbaikans = $this->getFilteredQuery()
            ->with(['barang.lokasi', 'user'])
            ->when($search, function ($query, $search) {
                $query->where('kode_perbaikan', 'like', '%' . $search . '%')
                    ->orWhereHas('barang', function ($q) use ($search) {
                        $q->where('nama_barang', 'like', '%' . $search . '%')
                          ->orWhere('kode_barang', 'like', '%' . $search . '%');
                    });
            })
            ->when($filterStatus, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filterPrioritas, function ($query, $prioritas) {
                $query->where('prioritas', $prioritas);
            })
            ->when($filterJenis, function ($query, $jenis) {
                $query->where('jenis', $jenis);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('perbaikan-pemeliharaan.index', compact('perbaikans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        // Get available barang for perbaikan
        $query = Barang::with(['kategori', 'lokasi']);

        if ($user->isPetugas() && $user->lokasi_id) {
            $query->where('lokasi_id', $user->lokasi_id);
        }

        $barangs = $query->orderBy('nama_barang')->get();

        $perbaikan = new PerbaikanPemeliharaan();

        return view('perbaikan-pemeliharaan.create', compact('perbaikan', 'barangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jenis' => 'required|in:Perbaikan,Pemeliharaan Rutin',
            'prioritas' => 'required|in:Rendah,Sedang,Tinggi,Urgent',
            'tanggal_pengajuan' => 'required|date',
            'keluhan' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $barang = Barang::findOrFail($validated['barang_id']);

            // Validasi lokasi untuk petugas
            if ($user->isPetugas() && $barang->lokasi_id != $user->lokasi_id) {
                throw new \Exception('Anda hanya dapat mengajukan perbaikan untuk barang di lokasi Anda.');
            }

            // Validasi barang tidak sedang dipinjam
            if ($barang->status === 'Dipinjam') {
                throw new \Exception('Barang sedang dipinjam. Tidak dapat diajukan perbaikan.');
            }

            // Validasi barang tidak sedang dalam perbaikan/perawatan
            if (in_array($barang->status, ['Diperbaiki', 'Perawatan'])) {
                throw new \Exception('Barang sedang dalam perbaikan/perawatan.');
            }

            // Create perbaikan
            $perbaikan = PerbaikanPemeliharaan::create([
                'kode_perbaikan' => PerbaikanPemeliharaan::generateKode(),
                'barang_id' => $validated['barang_id'],
                'jenis' => $validated['jenis'],
                'prioritas' => $validated['prioritas'],
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'keluhan' => $validated['keluhan'],
                'status' => 'Diajukan',
                'user_id' => $user->id,
            ]);

            // Update status barang
            $newStatus = $validated['jenis'] === 'Perbaikan' ? 'Diperbaiki' : 'Perawatan';
            $barang->update(['status' => $newStatus]);

            DB::commit();

            return redirect()->route('perbaikan-pemeliharaan.index')
                ->with('success', 'Pengajuan perbaikan berhasil dibuat dengan kode: ' . $perbaikan->kode_perbaikan);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PerbaikanPemeliharaan $perbaikanPemeliharaan)
    {
        $user = Auth::user();

        // Petugas hanya bisa lihat perbaikan di lokasinya
        if ($user->isPetugas() && $perbaikanPemeliharaan->barang->lokasi_id != $user->lokasi_id) {
            abort(403, 'Anda tidak memiliki akses ke data perbaikan ini.');
        }

        $perbaikanPemeliharaan->load(['barang.lokasi', 'user', 'approvedBy']);

        return view('perbaikan-pemeliharaan.show', compact('perbaikanPemeliharaan'));
    }

    /**
     * Approve perbaikan (Admin only)
     */
    public function approve(Request $request, PerbaikanPemeliharaan $perbaikanPemeliharaan)
    {
        if (!$perbaikanPemeliharaan->canBeApproved()) {
            return back()->withErrors(['error' => 'Perbaikan ini tidak dapat disetujui.']);
        }

        DB::beginTransaction();

        try {
            $perbaikanPemeliharaan->update([
                'status' => 'Disetujui',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('perbaikan-pemeliharaan.show', $perbaikanPemeliharaan)
                ->with('success', 'Perbaikan berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyetujui perbaikan: ' . $e->getMessage()]);
        }
    }

    /**
     * Process perbaikan (update to Dalam Perbaikan)
     */
    public function process(Request $request, PerbaikanPemeliharaan $perbaikanPemeliharaan)
    {
        $validated = $request->validate([
            'teknisi' => 'required|string|max:150',
            'tanggal_mulai' => 'required|date',
        ]);

        if (!$perbaikanPemeliharaan->canBeProcessed()) {
            return back()->withErrors(['error' => 'Perbaikan ini tidak dapat diproses.']);
        }

        DB::beginTransaction();

        try {
            $perbaikanPemeliharaan->update([
                'status' => 'Dalam Perbaikan',
                'teknisi' => $validated['teknisi'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
            ]);

            DB::commit();

            return redirect()->route('perbaikan-pemeliharaan.show', $perbaikanPemeliharaan)
                ->with('success', 'Perbaikan berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses perbaikan: ' . $e->getMessage()]);
        }
    }

    /**
     * Complete perbaikan
     */
    public function complete(Request $request, PerbaikanPemeliharaan $perbaikanPemeliharaan)
    {
        $validated = $request->validate([
            'tanggal_selesai' => 'required|date|after_or_equal:' . $perbaikanPemeliharaan->tanggal_mulai,
            'hasil_perbaikan' => 'required|string|max:1000',
            'kondisi_akhir' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'biaya_perbaikan' => 'nullable|numeric|min:0',
        ]);

        if ($perbaikanPemeliharaan->status !== 'Dalam Perbaikan') {
            return back()->withErrors(['error' => 'Perbaikan ini tidak dapat diselesaikan.']);
        }

        DB::beginTransaction();

        try {
            $perbaikanPemeliharaan->update([
                'status' => 'Selesai',
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'hasil_perbaikan' => $validated['hasil_perbaikan'],
                'kondisi_akhir' => $validated['kondisi_akhir'],
                'biaya_perbaikan' => $validated['biaya_perbaikan'],
            ]);

            // Update status barang
            $barang = $perbaikanPemeliharaan->barang;
            $newStatus = match ($validated['kondisi_akhir']) {
                'Baik' => 'Tersedia',
                'Rusak Ringan' => 'Rusak',
                'Rusak Berat' => 'Rusak',
                default => 'Tersedia',
            };

            $barang->update([
                'status' => $newStatus,
                'kondisi' => $validated['kondisi_akhir'],
            ]);

            DB::commit();

            return redirect()->route('perbaikan-pemeliharaan.show', $perbaikanPemeliharaan)
                ->with('success', 'Perbaikan berhasil diselesaikan. Status barang telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyelesaikan perbaikan: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel perbaikan
     */
    public function cancel(Request $request, PerbaikanPemeliharaan $perbaikanPemeliharaan)
    {
        $validated = $request->validate([
            'alasan_pembatalan' => 'required|string|max:500',
        ]);

        if (!$perbaikanPemeliharaan->canBeCancelled()) {
            return back()->withErrors(['error' => 'Perbaikan ini tidak dapat dibatalkan.']);
        }

        DB::beginTransaction();

        try {
            $perbaikanPemeliharaan->update([
                'status' => 'Dibatalkan',
                'hasil_perbaikan' => 'DIBATALKAN: ' . $validated['alasan_pembatalan'],
            ]);

            // Kembalikan status barang
            $barang = $perbaikanPemeliharaan->barang;
            $barang->update(['status' => 'Tersedia']);

            DB::commit();

            return redirect()->route('perbaikan-pemeliharaan.index')
                ->with('success', 'Perbaikan berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membatalkan perbaikan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PerbaikanPemeliharaan $perbaikanPemeliharaan)
    {
        $user = Auth::user();

        // Petugas hanya bisa hapus perbaikan di lokasinya
        if ($user->isPetugas() && $perbaikanPemeliharaan->barang->lokasi_id != $user->lokasi_id) {
            abort(403, 'Anda tidak memiliki akses ke data perbaikan ini.');
        }

        // Tidak bisa hapus jika sudah disetujui atau selesai
        if (in_array($perbaikanPemeliharaan->status, ['Selesai', 'Dalam Perbaikan'])) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus perbaikan yang sudah diproses atau selesai.']);
        }

        DB::beginTransaction();

        try {
            // Kembalikan status barang jika masih Diajukan/Disetujui
            if (in_array($perbaikanPemeliharaan->status, ['Diajukan', 'Disetujui', 'Dibatalkan'])) {
                $barang = $perbaikanPemeliharaan->barang;
                $barang->update(['status' => 'Tersedia']);
            }

            $perbaikanPemeliharaan->delete();

            DB::commit();

            return redirect()->route('perbaikan-pemeliharaan.index')
                ->with('success', 'Data perbaikan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus perbaikan: ' . $e->getMessage()]);
        }
    }

    /**
     * Show laporan form
     */
    public function laporanForm()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $lokasi = Lokasi::all();
        } else {
            $lokasi = Lokasi::where('id', $user->lokasi_id)->get();
        }

        return view('perbaikan-pemeliharaan.laporan-form', compact('lokasi'));
    }

    /**
     * Generate laporan PDF
     */
    public function cetakLaporan(Request $request)
    {
        $request->validate([
            'tanggal_dari' => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
            'status' => 'nullable|in:Diajukan,Disetujui,Dalam Perbaikan,Selesai,Dibatalkan',
            'jenis' => 'nullable|in:Perbaikan,Pemeliharaan Rutin',
            'prioritas' => 'nullable|in:Rendah,Sedang,Tinggi,Urgent',
            'lokasi_id' => 'nullable|exists:lokasis,id',
        ]);

        $query = $this->getFilteredQuery()->with(['barang.lokasi', 'user', 'approvedBy']);

        // Apply filters
        if ($request->tanggal_dari) {
            $query->whereDate('tanggal_pengajuan', '>=', $request->tanggal_dari);
        }

        if ($request->tanggal_sampai) {
            $query->whereDate('tanggal_pengajuan', '<=', $request->tanggal_sampai);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->jenis) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->prioritas) {
            $query->where('prioritas', $request->prioritas);
        }

        if ($request->lokasi_id) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('lokasi_id', $request->lokasi_id);
            });
        }

        $perbaikans = $query->orderBy('tanggal_pengajuan', 'desc')->get();

        // Calculate statistics
        $totalPerbaikan = $perbaikans->count();
        $totalDiajukan = $perbaikans->where('status', 'Diajukan')->count();
        $totalSelesai = $perbaikans->where('status', 'Selesai')->count();
        $totalBiaya = $perbaikans->where('status', 'Selesai')->sum('biaya_perbaikan');

        $data = [
            'title' => 'Laporan Perbaikan & Pemeliharaan',
            'date' => date('d F Y'),
            'filters' => [
                'tanggal_dari' => $request->tanggal_dari,
                'tanggal_sampai' => $request->tanggal_sampai,
                'status' => $request->status,
                'jenis' => $request->jenis,
                'prioritas' => $request->prioritas,
                'lokasi' => $request->lokasi_id ? Lokasi::find($request->lokasi_id)->nama_lokasi : null,
            ],
            'perbaikans' => $perbaikans,
            'statistics' => [
                'total_perbaikan' => $totalPerbaikan,
                'total_diajukan' => $totalDiajukan,
                'total_selesai' => $totalSelesai,
                'total_biaya' => $totalBiaya,
            ],
        ];

        $pdf = Pdf::loadView('perbaikan-pemeliharaan.laporan', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-perbaikan-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Middleware configuration
     */
    public static function middleware()
    {
        return [
            new Middleware('permission:manage perbaikan-pemeliharaan', except: ['destroy']),
            new Middleware('permission:delete perbaikan-pemeliharaan', only: ['destroy']),
        ];
    }
}