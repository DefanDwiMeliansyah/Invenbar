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
        Schema::create('peminjaman_details', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('peminjaman_id')
                ->constrained('peminjamans')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            $table->foreignId('barang_id')
                ->constrained('barangs')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            
            $table->integer('jumlah')->default(1);
            $table->boolean('dapat_dikembalikan')->default(true);
            $table->integer('jumlah_dikembalikan')->default(0);
            $table->enum('kondisi_awal', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('kondisi_akhir', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->nullable();
            $table->enum('status_detail', ['Dipinjam', 'Dikembalikan', 'Selesai'])->default('Dipinjam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_details');
    }
};