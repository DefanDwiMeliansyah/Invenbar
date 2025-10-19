<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanDetail extends Model
{
    protected $guarded = ['id'];

    /**
     * Get badge class for status detail
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status_detail) {
            'Dipinjam' => 'bg-primary',
            'Dikembalikan' => 'bg-success',
            'Selesai' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Get remaining quantity to return
     */
    public function getRemainingQuantity(): int
    {
        return $this->jumlah - $this->jumlah_dikembalikan;
    }

    /**
     * Check if fully returned
     */
    public function isFullyReturned(): bool
    {
        return $this->jumlah_dikembalikan >= $this->jumlah;
    }

    /**
     * Get peminjaman that owns the detail
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    /**
     * Get barang
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}