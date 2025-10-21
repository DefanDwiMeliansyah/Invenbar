<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perbaikan_pemeliharaans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_perbaikan', 50)->unique();
            
            $table->foreignId('barang_id')
                ->constrained('barangs')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            
            $table->enum('jenis', ['Perbaikan', 'Pemeliharaan Rutin']);
            $table->enum('prioritas', ['Rendah', 'Sedang', 'Tinggi', 'Urgent'])->default('Sedang');
            $table->date('tanggal_pengajuan');
            $table->text('keluhan');
            
            $table->enum('status', [
                'Diajukan',
                'Disetujui',
                'Dalam Perbaikan',
                'Selesai',
                'Dibatalkan'
            ])->default('Diajukan');
            
            // Data Perbaikan
            $table->string('teknisi', 150)->nullable();
            $table->decimal('biaya_perbaikan', 12, 2)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('hasil_perbaikan')->nullable();
            $table->enum('kondisi_akhir', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->nullable();
            
            // User Relations
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            
            $table->datetime('approved_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perbaikan_pemeliharaans');
    }
};