<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Peminjaman extends Model
{
    protected $guarded = ['id'];

    protected $table = 'peminjamans';

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_batas_pengembalian' => 'date',
    ];

    /**
     * Generate kode peminjaman otomatis
     */
    public static function generateKode(): string
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = 'PJM-' . $date . '-';
        
        $lastPeminjaman = self::where('kode_peminjaman', 'like', $prefix . '%')
            ->orderBy('kode_peminjaman', 'desc')
            ->first();
        
        if ($lastPeminjaman) {
            $lastNumber = (int) substr($lastPeminjaman->kode_peminjaman, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get badge class for status
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'Dipinjam' => 'bg-primary',
            'Dikembalikan' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    /**
     * Check if peminjaman is late
     */
    public function isLate(): bool
    {
        if ($this->status === 'Dikembalikan') {
            return false;
        }
        
        return Carbon::now()->isAfter($this->tanggal_batas_pengembalian);
    }

    /**
     * Get days late
     */
    public function getDaysLate(): int
    {
        if (!$this->isLate()) {
            return 0;
        }
        
        return Carbon::now()->diffInDays($this->tanggal_batas_pengembalian);
    }

    /**
     * Check if all returnable items are returned
     */
    public function isAllReturned(): bool
    {
        return $this->details()
            ->where('dapat_dikembalikan', true)
            ->where('status_detail', '!=', 'Dikembalikan')
            ->count() === 0;
    }

    /**
     * Get lokasi that owns the peminjaman
     */
    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    /**
     * Get user that created the peminjaman
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get peminjaman details
     */
    public function details(): HasMany
    {
        return $this->hasMany(PeminjamanDetail::class, 'peminjaman_id');
    }
}