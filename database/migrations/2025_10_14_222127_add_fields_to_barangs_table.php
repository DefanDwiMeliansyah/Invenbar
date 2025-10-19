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
        Schema::table('barangs', function (Blueprint $table) {
            $table->enum('mode_input', ['Masal', 'Per Unit'])->default('Masal')->after('lokasi_id');
            $table->enum('status', [
                'Tersedia',
                'Dipinjam',
                'Rusak',
                'Hilang',
                'Tidak Dapat Dipinjam',
                'Diperbaiki',
                'Perawatan',
                'Habis'
            ])->default('Tersedia')->after('kondisi');
            $table->boolean('dapat_dikembalikan')->default(true)->after('mode_input');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['mode_input', 'status', 'dapat_dikembalikan']);
        });
    }
};