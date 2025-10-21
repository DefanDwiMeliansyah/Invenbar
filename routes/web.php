<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PeminjamanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Navigasi Aplikasi
    Route::resource('user', UserController::class);
    Route::resource('kategori', KategoriController::class);
    Route::resource('lokasi', LokasiController::class);

    // Route Barang - dengan middleware lokasi
    Route::middleware('check.lokasi')->group(function () {
        Route::get('/barang/laporan', [BarangController::class, 'cetaklaporan'])->name('barang.laporan');
        Route::delete('/barang/group/{prefix}', [BarangController::class, 'destroyGroup'])->name('barang.destroy-group');
        Route::resource('barang', BarangController::class);
    });

    // Route Peminjaman
    Route::middleware('check.lokasi')->group(function () {
        Route::get('/peminjaman/barang-tersedia', [PeminjamanController::class, 'getAvailableBarang'])
            ->name('peminjaman.barang-tersedia');
        Route::patch('/peminjaman/{peminjaman}/return', [PeminjamanController::class, 'return'])
            ->name('peminjaman.return');
        Route::resource('peminjaman', PeminjamanController::class);
        Route::get('peminjaman-laporan/form', [PeminjamanController::class, 'laporanForm'])
            ->name('peminjaman.laporan-form');
        Route::get('peminjaman-laporan/cetak', [PeminjamanController::class, 'cetakLaporan'])
            ->name('peminjaman.cetak-laporan');
    });

    // Route Perbaikan & Pemeliharaan
Route::middleware('check.lokasi')->group(function () {
    // Laporan
    Route::get('perbaikan-pemeliharaan-laporan/form', [App\Http\Controllers\PerbaikanPemeliharaanController::class, 'laporanForm'])
        ->name('perbaikan-pemeliharaan.laporan-form');
    Route::get('perbaikan-pemeliharaan-laporan/cetak', [App\Http\Controllers\PerbaikanPemeliharaanController::class, 'cetakLaporan'])
        ->name('perbaikan-pemeliharaan.cetak-laporan');
    
    // Workflow Actions
    Route::patch('perbaikan-pemeliharaan/{perbaikanPemeliharaan}/approve', [App\Http\Controllers\PerbaikanPemeliharaanController::class, 'approve'])
        ->name('perbaikan-pemeliharaan.approve');
    Route::patch('perbaikan-pemeliharaan/{perbaikanPemeliharaan}/process', [App\Http\Controllers\PerbaikanPemeliharaanController::class, 'process'])
        ->name('perbaikan-pemeliharaan.process');
    Route::patch('perbaikan-pemeliharaan/{perbaikanPemeliharaan}/complete', [App\Http\Controllers\PerbaikanPemeliharaanController::class, 'complete'])
        ->name('perbaikan-pemeliharaan.complete');
    Route::patch('perbaikan-pemeliharaan/{perbaikanPemeliharaan}/cancel', [App\Http\Controllers\PerbaikanPemeliharaanController::class, 'cancel'])
        ->name('perbaikan-pemeliharaan.cancel');
    
    // Resource Routes
    Route::resource('perbaikan-pemeliharaan', App\Http\Controllers\PerbaikanPemeliharaanController::class);
});
});

require __DIR__ . '/auth.php';
