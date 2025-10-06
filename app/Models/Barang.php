<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
            default => 'bg-secondary',
        };
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