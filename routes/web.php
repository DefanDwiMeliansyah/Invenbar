<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LokasiController;

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
});

require __DIR__ . '/auth.php';