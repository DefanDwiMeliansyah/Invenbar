<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Query dasar untuk barang
        $barangQuery = Barang::query();

        // Filter lokasi untuk petugas
        if ($user->isPetugas() && $user->lokasi_id) {
            $barangQuery->where('lokasi_id', $user->lokasi_id);
        }

        // Hitung statistik
        $totalBarang = $barangQuery->sum('jumlah');
        $jumlahBarang = (clone $barangQuery)->count();
        $barangTersedia = (clone $barangQuery)->where('status', 'Tersedia')->sum('jumlah');
        $barangDipinjam = (clone $barangQuery)->where('status', 'Dipinjam')->sum('jumlah');
        $barangRusak = (clone $barangQuery)->whereIn('status', ['Rusak', 'Rusak Ringan', 'Rusak Berat'])->sum('jumlah');

        // Data untuk admin (dapat melihat semua)
        if ($user->isAdmin()) {
            $totalKategori = Kategori::count();
            $jumlahKategori = Kategori::count();
            $totalLokasi = Lokasi::count();
            $jumlahLokasi = Lokasi::count();
            $totalUser = User::count();
            $jumlahUser = User::count();
        } else {
            // Data untuk petugas (hanya kategori yang global)
            $totalKategori = Kategori::count();
            $jumlahKategori = Kategori::count();
            $totalLokasi = 1; // Hanya lokasi mereka
            $jumlahLokasi = 1;
            $totalUser = null; // Petugas tidak perlu lihat ini
            $jumlahUser = null;
        }

        // Barang terbaru (sesuai lokasi)
        $barangTerbaru = (clone $barangQuery)
            ->with(['kategori', 'lokasi'])
            ->latest()
            ->limit(5)
            ->get();

        // Barang per kategori (sesuai lokasi)
        $barangPerKategori = (clone $barangQuery)
            ->select('kategori_id', DB::raw('SUM(jumlah) as total'))
            ->with('kategori')
            ->groupBy('kategori_id')
            ->get();

        // Barang per lokasi (untuk admin saja)
        $barangPerLokasi = null;
        if ($user->isAdmin()) {
            $barangPerLokasi = Barang::select('lokasi_id', DB::raw('SUM(jumlah) as total'))
                ->with('lokasi')
                ->groupBy('lokasi_id')
                ->get();
        }

        // Statistik kondisi barang
        $kondisiBaik = (clone $barangQuery)->where('kondisi', 'Baik')->sum('jumlah');
        $kondisiRusakRingan = (clone $barangQuery)->where('kondisi', 'Rusak Ringan')->sum('jumlah');
        $kondisiRusakBerat = (clone $barangQuery)->where('kondisi', 'Rusak Berat')->sum('jumlah');

        return view('dashboard', compact(
            'totalBarang',
            'jumlahBarang',
            'jumlahKategori',
            'jumlahLokasi',
            'jumlahUser',
            'barangTersedia',
            'barangDipinjam',
            'barangRusak',
            'kondisiBaik',
            'kondisiRusakRingan',
            'kondisiRusakBerat',
            'totalKategori',
            'totalLokasi',
            'totalUser',
            'barangTerbaru',
            'barangPerKategori',
            'barangPerLokasi'
        ));
    }
}