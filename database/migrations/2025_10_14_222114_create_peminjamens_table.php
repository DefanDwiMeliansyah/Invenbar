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
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_peminjaman', 50)->unique();
            $table->string('nama_peminjam', 150);
            $table->string('nomor_telepon', 20);
            $table->string('email', 150);
            $table->date('tanggal_pinjam');
            $table->date('tanggal_batas_pengembalian');
            
            $table->foreignId('lokasi_id')
                ->constrained('lokasis')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            
            $table->enum('status', ['Dipinjam', 'Dikembalikan'])->default('Dipinjam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};