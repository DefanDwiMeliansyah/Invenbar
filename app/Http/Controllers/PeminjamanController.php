<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use App\Models\Barang;
use App\Models\Lokasi;
use App\Models\Kategori;
use App\Http\Requests\StorePeminjamanRequest;
use App\Http\Requests\ReturnPeminjamanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Barryvdh\DomPDF\Facade\Pdf;

class PeminjamanController extends Controller implements HasMiddleware
{
    /**
     * Get query with lokasi filter applied
     */
    protected function getFilteredQuery()
    {
        $query = Peminjaman::query();
        $user = Auth::user();

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

        $peminjamans = $this->getFilteredQuery()
            ->with(['lokasi', 'user', 'details.barang'])
            ->when($search, function ($query, $search) {
                $query->where('kode_peminjaman', 'like', '%' . $search . '%')
                    ->orWhere('nama_peminjam', 'like', '%' . $search . '%')
                    ->orWhere('nomor_telepon', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('peminjaman.index', compact('peminjamans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $lokasi = Lokasi::all();
        } else {
            $lokasi = Lokasi::where('id', $user->lokasi_id)->get();
        }

        $peminjaman = new Peminjaman();

        return view('peminjaman.create', compact('peminjaman', 'lokasi'));
    }

    /**
     * Get available barang for borrowing (AJAX)
     */
    public function getAvailableBarang(Request $request)
    {
        $user = Auth::user();
        $lokasiId = $request->lokasi_id;

        $query = Barang::with(['kategori', 'lokasi']);

        // Filter by lokasi
        if ($user->isPetugas() && $user->lokasi_id) {
            $query->where('lokasi_id', $user->lokasi_id);
        } elseif ($lokasiId) {
            $query->where('lokasi_id', $lokasiId);
        }

        // Filter available for borrowing
        $query->where(function ($q) {
            $q->where(function ($sq) {
                // Per Unit: status Tersedia
                $sq->where('mode_input', 'Per Unit')
                    ->where('status', 'Tersedia');
            })->orWhere(function ($sq) {
                // Masal: jumlah > 0 dan status != Habis
                $sq->where('mode_input', 'Masal')
                    ->where('jumlah', '>', 0)
                    ->where('status', '!=', 'Habis');
            });
        });

        $barangs = $query->orderBy('mode_input', 'desc')
            ->orderBy('nama_barang')
            ->get();

        // Group by kategori
        $grouped = $barangs->groupBy('kategori.nama_kategori');

        return response()->json([
            'success' => true,
            'data' => $grouped
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePeminjamanRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Validasi lokasi untuk petugas
        if ($user->isPetugas() && $validated['lokasi_id'] != $user->lokasi_id) {
            return back()->withInput()->withErrors(['lokasi_id' => 'Anda hanya dapat membuat peminjaman di lokasi Anda.']);
        }

        DB::beginTransaction();

        try {
            // Create peminjaman
            $peminjaman = Peminjaman::create([
                'kode_peminjaman' => Peminjaman::generateKode(),
                'nama_peminjam' => $validated['nama_peminjam'],
                'nomor_telepon' => $validated['nomor_telepon'],
                'email' => $validated['email'],
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_batas_pengembalian' => $validated['tanggal_batas_pengembalian'],
                'lokasi_id' => $validated['lokasi_id'],
                'user_id' => $user->id,
                'status' => 'Dipinjam',
            ]);

            // Process each barang
            foreach ($validated['barang_ids'] as $index => $barangId) {
                $barang = Barang::findOrFail($barangId);
                $jumlahPinjam = $validated['jumlah_pinjam'][$index];

                // Validasi stok untuk barang masal
                if ($barang->mode_input === 'Masal' && $jumlahPinjam > $barang->jumlah) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi. Tersedia: {$barang->jumlah}, diminta: {$jumlahPinjam}");
                }

                // Validasi barang per unit sudah tersedia
                if ($barang->mode_input === 'Per Unit' && $barang->status !== 'Tersedia') {
                    throw new \Exception("Barang {$barang->nama_barang} tidak tersedia untuk dipinjam.");
                }

                // Create detail
                $statusDetail = $barang->dapat_dikembalikan ? 'Dipinjam' : 'Selesai';

                PeminjamanDetail::create([
                    'peminjaman_id' => $peminjaman->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $jumlahPinjam,
                    'dapat_dikembalikan' => $barang->dapat_dikembalikan,
                    'jumlah_dikembalikan' => 0,
                    'kondisi_awal' => $barang->kondisi,
                    'status_detail' => $statusDetail,
                ]);

                // Update barang
                if ($barang->mode_input === 'Per Unit') {
                    $barang->update(['status' => 'Dipinjam']);
                } else {
                    // Masal: kurangi stok
                    $newJumlah = $barang->jumlah - $jumlahPinjam;
                    $newStatus = $newJumlah <= 0 ? 'Habis' : $barang->status;

                    $barang->update([
                        'jumlah' => $newJumlah,
                        'status' => $newStatus,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('peminjaman.index')
                ->with('success', 'Peminjaman berhasil dibuat dengan kode: ' . $peminjaman->kode_peminjaman);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Peminjaman $peminjaman)
    {
        $user = Auth::user();

        if ($user->isPetugas() && $peminjaman->lokasi_id != $user->lokasi_id) {
            abort(403, 'Anda tidak memiliki akses ke peminjaman ini.');
        }

        $peminjaman->load(['lokasi', 'user', 'details.barang.kategori']);

        return view('peminjaman.show', compact('peminjaman'));
    }

    /**
     * Process return of borrowed items
     */
    public function return(ReturnPeminjamanRequest $request, Peminjaman $peminjaman)
    {
        $user = Auth::user();

        if ($user->isPetugas() && $peminjaman->lokasi_id != $user->lokasi_id) {
            abort(403, 'Anda tidak memiliki akses ke peminjaman ini.');
        }

        $validated = $request->validated();

        // Debug log
        \Log::info('Return Request Data:', $validated);

        DB::beginTransaction();

        try {
            // Check if detail_ids exist and not empty
            if (!isset($validated['detail_ids']) || empty($validated['detail_ids'])) {
                throw new \Exception('Tidak ada barang yang dipilih untuk dikembalikan.');
            }

            // Check if peminjaman is late
            $isLate = $peminjaman->isLate();
            $daysLate = $peminjaman->getDaysLate();

            foreach ($validated['detail_ids'] as $detailId) {
                $detail = PeminjamanDetail::findOrFail($detailId);
                $barang = $detail->barang;

                // Get jumlah and kondisi for this detail
                $jumlahKembali = isset($validated['jumlah_kembali'][$detailId])
                    ? (int)$validated['jumlah_kembali'][$detailId]
                    : 1;

                $kondisiAkhir = $validated['kondisi_akhir'][$detailId] ?? 'Baik';

                // Validasi jumlah yang dikembalikan
                $sisaPinjam = $detail->getRemainingQuantity();
                if ($jumlahKembali > $sisaPinjam) {
                    throw new \Exception("Jumlah pengembalian {$barang->nama_barang} melebihi sisa pinjaman. Sisa: {$sisaPinjam}");
                }

                // FITUR BARU: Cek jika barang Masal dan terlambat
                $isSekaliPinjam = false;
                if ($barang->mode_input === 'Masal' && $isLate) {
                    $isSekaliPinjam = true;
                    // Barang Masal yang terlambat dianggap "Sekali Pinjam" (tidak kembali ke stok)
                }

                // Update detail
                $newJumlahDikembalikan = $detail->jumlah_dikembalikan + $jumlahKembali;
                $isFullyReturned = $newJumlahDikembalikan >= $detail->jumlah;

                $detail->update([
                    'jumlah_dikembalikan' => $newJumlahDikembalikan,
                    'kondisi_akhir' => $kondisiAkhir,
                    'status_detail' => $isFullyReturned ? 'Dikembalikan' : 'Dipinjam',
                    'keterangan' => $isSekaliPinjam ? 'Sekali Pinjam (Terlambat ' . $daysLate . ' hari)' : null,
                ]);

                // Update barang
                if ($barang->mode_input === 'Per Unit') {
                    // Per Unit: update status berdasarkan kondisi akhir (normal)
                    $newStatus = match ($kondisiAkhir) {
                        'Baik' => 'Tersedia',
                        'Rusak Ringan' => 'Rusak',
                        'Rusak Berat' => 'Rusak',
                        default => 'Tersedia',
                    };

                    $barang->update([
                        'status' => $newStatus,
                        'kondisi' => $kondisiAkhir,
                    ]);
                } else {
                    // Masal: cek apakah "Sekali Pinjam" atau tidak
                    if ($isSekaliPinjam) {
                        // TIDAK menambah stok karena dianggap consumable/rusak/hilang
                        // Stok tetap seperti saat dipinjam (sudah dikurangi)
                        \Log::info("Barang Masal Sekali Pinjam: {$barang->nama_barang}, Jumlah: {$jumlahKembali}, Tidak kembali ke stok");

                        // Optional: Update status jika stok habis
                        if ($barang->jumlah <= 0) {
                            $barang->update(['status' => 'Habis']);
                        }
                    } else {
                        // Normal: tambah stok
                        $newJumlah = $barang->jumlah + $jumlahKembali;
                        $newStatus = $newJumlah > 0 ? 'Tersedia' : 'Habis';

                        $barang->update([
                            'jumlah' => $newJumlah,
                            'status' => $newStatus,
                            'kondisi' => $kondisiAkhir,
                        ]);
                    }
                }
            }

            // Check if all returnable items are returned
            if ($peminjaman->isAllReturned()) {
                $peminjaman->update(['status' => 'Dikembalikan']);
            }

            DB::commit();

            $message = $peminjaman->status === 'Dikembalikan'
                ? 'Semua barang telah dikembalikan. Peminjaman selesai.'
                : 'Barang berhasil dikembalikan sebagian.';

            // Tambah notifikasi jika ada barang "Sekali Pinjam"
            if ($isLate) {
                $message .= ' PERHATIAN: Barang masal yang terlambat tidak kembali ke stok (Sekali Pinjam).';
            }

            return redirect()->route('peminjaman.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Return Error:', ['message' => $e->getMessage()]);

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Destroy the specified resource
     */
    public function destroy(Peminjaman $peminjaman)
    {
        $user = Auth::user();

        if ($user->isPetugas() && $peminjaman->lokasi_id != $user->lokasi_id) {
            abort(403, 'Anda tidak memiliki akses ke peminjaman ini.');
        }

        // Tidak bisa hapus jika sudah ada pengembalian
        $hasReturned = $peminjaman->details()
            ->where('status_detail', 'Dikembalikan')
            ->exists();

        if ($hasReturned) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus peminjaman yang sudah ada pengembalian.']);
        }

        DB::beginTransaction();

        try {
            // Kembalikan status/stok barang
            foreach ($peminjaman->details as $detail) {
                $barang = $detail->barang;

                if ($barang->mode_input === 'Per Unit') {
                    $barang->update(['status' => 'Tersedia']);
                } else {
                    $newJumlah = $barang->jumlah + $detail->jumlah;
                    $newStatus = $newJumlah > 0 ? 'Tersedia' : $barang->status;

                    $barang->update([
                        'jumlah' => $newJumlah,
                        'status' => $newStatus,
                    ]);
                }
            }

            $peminjaman->delete();

            DB::commit();

            return redirect()->route('peminjaman.index')
                ->with('success', 'Data peminjaman berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus peminjaman: ' . $e->getMessage()]);
        }
    }

    /**
     * Show laporan filter form
     */
    public function laporanForm()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $lokasi = Lokasi::all();
        } else {
            $lokasi = Lokasi::where('id', $user->lokasi_id)->get();
        }

        return view('peminjaman.laporan-form', compact('lokasi'));
    }

    /**
     * Generate laporan PDF
     */
    public function cetakLaporan(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'tanggal_dari' => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
            'status' => 'nullable|in:Dipinjam,Dikembalikan',
            'lokasi_id' => 'nullable|exists:lokasis,id',
            'nama_peminjam' => 'nullable|string',
            'group_by' => 'nullable|in:none,status,lokasi,bulan',
        ]);

        $query = $this->getFilteredQuery()->with(['lokasi', 'user', 'details.barang.kategori']);

        // Apply filters
        if ($request->tanggal_dari) {
            $query->whereDate('tanggal_pinjam', '>=', $request->tanggal_dari);
        }

        if ($request->tanggal_sampai) {
            $query->whereDate('tanggal_pinjam', '<=', $request->tanggal_sampai);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->lokasi_id) {
            $query->where('lokasi_id', $request->lokasi_id);
        }

        if ($request->nama_peminjam) {
            $query->where('nama_peminjam', 'like', '%' . $request->nama_peminjam . '%');
        }

        $peminjamans = $query->orderBy('tanggal_pinjam', 'desc')->get();

        // Calculate statistics
        $totalPeminjaman = $peminjamans->count();
        $totalDipinjam = $peminjamans->where('status', 'Dipinjam')->count();
        $totalDikembalikan = $peminjamans->where('status', 'Dikembalikan')->count();
        $totalTerlambat = $peminjamans->filter(fn($p) => $p->isLate())->count();
        $totalBarang = $peminjamans->sum(fn($p) => $p->details->count());

        // Group data if requested
        $groupedData = null;
        if ($request->group_by && $request->group_by !== 'none') {
            $groupedData = $this->groupPeminjaman($peminjamans, $request->group_by);
        }

        $data = [
            'title' => 'Laporan Data Peminjaman Inventaris',
            'date' => date('d F Y'),
            'filters' => [
                'tanggal_dari' => $request->tanggal_dari,
                'tanggal_sampai' => $request->tanggal_sampai,
                'status' => $request->status,
                'lokasi' => $request->lokasi_id ? Lokasi::find($request->lokasi_id)->nama_lokasi : null,
                'nama_peminjam' => $request->nama_peminjam,
            ],
            'peminjamans' => $peminjamans,
            'groupedData' => $groupedData,
            'groupBy' => $request->group_by,
            'statistics' => [
                'total_peminjaman' => $totalPeminjaman,
                'total_dipinjam' => $totalDipinjam,
                'total_dikembalikan' => $totalDikembalikan,
                'total_terlambat' => $totalTerlambat,
                'total_barang' => $totalBarang,
            ],
        ];

        $pdf = Pdf::loadView('peminjaman.laporan', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-peminjaman-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Group peminjaman data
     */
    private function groupPeminjaman($peminjamans, $groupBy)
    {
        switch ($groupBy) {
            case 'status':
                return $peminjamans->groupBy('status');

            case 'lokasi':
                return $peminjamans->groupBy('lokasi.nama_lokasi');

            case 'bulan':
                return $peminjamans->groupBy(function ($item) {
                    return $item->tanggal_pinjam->format('F Y');
                });

            default:
                return null;
        }
    }

    /**
     * Middleware configuration
     */
    public static function middleware()
    {
        return [
            new Middleware('permission:manage peminjaman', except: ['destroy']),
            new Middleware('permission:delete peminjaman', only: ['destroy']),
        ];
    }
}
