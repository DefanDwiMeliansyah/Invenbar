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
            $table->enum('status', [
                'Tersedia',
                'Dipinjam',
                'Rusak',
                'Hilang',
                'Tidak Dapat Dipinjam',
                'Diperbaiki',
                'Perawatan'
            ])->default('Tersedia')->after('mode_input');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};