<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PerbaikanPemeliharaan extends Model
{
    protected $guarded = ['id'];

    protected $table = 'perbaikan_pemeliharaans';

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at' => 'datetime',
        'biaya_perbaikan' => 'decimal:2',
    ];

    /**
     * Generate kode perbaikan otomatis
     */
    public static function generateKode(): string
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = 'PBK-' . $date . '-';
        
        $lastPerbaikan = self::where('kode_perbaikan', 'like', $prefix . '%')
            ->orderBy('kode_perbaikan', 'desc')
            ->first();
        
        if ($lastPerbaikan) {
            $lastNumber = (int) substr($lastPerbaikan->kode_perbaikan, -3);
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
            'Diajukan' => 'bg-warning text-dark',
            'Disetujui' => 'bg-info',
            'Dalam Perbaikan' => 'bg-primary',
            'Selesai' => 'bg-success',
            'Dibatalkan' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get badge class for prioritas
     */
    public function getPrioritasBadgeClass(): string
    {
        return match($this->prioritas) {
            'Urgent' => 'bg-danger',
            'Tinggi' => 'bg-warning text-dark',
            'Sedang' => 'bg-info',
            'Rendah' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Get badge class for jenis
     */
    public function getJenisBadgeClass(): string
    {
        return match($this->jenis) {
            'Perbaikan' => 'bg-danger',
            'Pemeliharaan Rutin' => 'bg-primary',
            default => 'bg-secondary',
        };
    }

    /**
     * Check if can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'Diajukan';
    }

    /**
     * Check if can be processed
     */
    public function canBeProcessed(): bool
    {
        return in_array($this->status, ['Disetujui', 'Dalam Perbaikan']);
    }

    /**
     * Check if can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['Diajukan', 'Disetujui']);
    }

    /**
     * Get durasi perbaikan (days)
     */
    public function getDurasiPerbaikan(): ?int
    {
        if (!$this->tanggal_mulai || !$this->tanggal_selesai) {
            return null;
        }
        
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    /**
     * Get barang relation
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    /**
     * Get user that created the perbaikan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get user that approved the perbaikan
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}