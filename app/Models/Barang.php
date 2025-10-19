<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pengadaan' => 'date',
    ];

    /**
     * Get badge class for status
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'Tersedia' => 'bg-success',
            'Dipinjam' => 'bg-primary',
            'Rusak' => 'bg-danger',
            'Hilang' => 'bg-dark',
            'Tidak Dapat Dipinjam' => 'bg-secondary',
            'Diperbaiki' => 'bg-warning text-dark',
            'Perawatan' => 'bg-info',
            'Habis' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Check if barang is available for borrowing
     */
    public function isAvailableForBorrowing(): bool
    {
        if ($this->mode_input === 'Per Unit') {
            return $this->status === 'Tersedia';
        }
        
        // Masal
        return $this->jumlah > 0 && $this->status !== 'Habis';
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function lokasi(): BelongsTo
    {
         return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    /**
     * Get peminjaman details
     */
    public function peminjamanDetails(): HasMany
    {
        return $this->hasMany(PeminjamanDetail::class, 'barang_id');
    }

    /**
     * Extract numeric suffix from kode_barang
     */
    public function getKodePrefix(): string
    {
        return preg_replace('/\d+$/', '', $this->kode_barang);
    }

    /**
     * Extract numeric part from kode_barang
     */
    public function getKodeNumber(): int
    {
        preg_match('/(\d+)$/', $this->kode_barang, $matches);
        return isset($matches[1]) ? (int)$matches[1] : 0;
    }
}