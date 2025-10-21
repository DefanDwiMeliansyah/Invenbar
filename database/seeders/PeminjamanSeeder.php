<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert data peminjaman - Oktober 2025
        $peminjamans = [
            [
                'id' => 1,
                'kode_peminjaman' => 'PJM-2025-001',
                'nama_peminjam' => 'Ahmad Fauzi',
                'nomor_telepon' => '081234567890',
                'email' => 'ahmad.fauzi@student.sch.id',
                'tanggal_pinjam' => '2025-10-15',
                'tanggal_batas_pengembalian' => '2025-10-20',
                'lokasi_id' => 1, // LAB PPLG-1
                'user_id' => 2, // Guru PPLG 1
                'status' => 'Dikembalikan',
                'created_at' => Carbon::parse('2025-10-15'),
                'updated_at' => Carbon::parse('2025-10-19'),
            ],
            [
                'id' => 2,
                'kode_peminjaman' => 'PJM-2025-002',
                'nama_peminjam' => 'Siti Nurhaliza',
                'nomor_telepon' => '081298765432',
                'email' => 'siti.nurhaliza@student.sch.id',
                'tanggal_pinjam' => '2025-10-18',
                'tanggal_batas_pengembalian' => '2025-10-25',
                'lokasi_id' => 2, // LAB PPLG-2
                'user_id' => 3, // Guru PPLG 2
                'status' => 'Dipinjam',
                'created_at' => Carbon::parse('2025-10-18'),
                'updated_at' => Carbon::parse('2025-10-18'),
            ],
            [
                'id' => 3,
                'kode_peminjaman' => 'PJM-2025-003',
                'nama_peminjam' => 'Budi Santoso',
                'nomor_telepon' => '082134567891',
                'email' => 'budi.santoso@student.sch.id',
                'tanggal_pinjam' => '2025-10-20',
                'tanggal_batas_pengembalian' => '2025-10-27',
                'lokasi_id' => 3, // LAB PPLG-3
                'user_id' => 4, // Guru PPLG 3
                'status' => 'Dipinjam',
                'created_at' => Carbon::parse('2025-10-20'),
                'updated_at' => Carbon::parse('2025-10-20'),
            ],
            [
                'id' => 4,
                'kode_peminjaman' => 'PJM-2025-004',
                'nama_peminjam' => 'Dewi Lestari',
                'nomor_telepon' => '085612345678',
                'email' => 'dewi.lestari@teacher.sch.id',
                'tanggal_pinjam' => '2025-10-13',
                'tanggal_batas_pengembalian' => '2025-10-18',
                'lokasi_id' => 4, // Ruang UKS
                'user_id' => 5, // Pembina PMR
                'status' => 'Dikembalikan',
                'created_at' => Carbon::parse('2025-10-13'),
                'updated_at' => Carbon::parse('2025-10-17'),
            ],
            [
                'id' => 5,
                'kode_peminjaman' => 'PJM-2025-005',
                'nama_peminjam' => 'Rizki Ramadhan',
                'nomor_telepon' => '087765432109',
                'email' => 'rizki.ramadhan@student.sch.id',
                'tanggal_pinjam' => '2025-10-22',
                'tanggal_batas_pengembalian' => '2025-10-29',
                'lokasi_id' => 1, // LAB PPLG-1
                'user_id' => 2, // Guru PPLG 1
                'status' => 'Dipinjam',
                'created_at' => Carbon::parse('2025-10-22'),
                'updated_at' => Carbon::parse('2025-10-22'),
            ],
        ];

        DB::table('peminjamans')->insert($peminjamans);

        // Insert detail peminjaman untuk setiap peminjaman
        $details = [
            // ============================================================
            // Peminjaman 1: Ahmad Fauzi - LAB PPLG-1 - DIKEMBALIKAN
            // ============================================================
            [
                'peminjaman_id' => 1,
                'barang_id' => 1, // PC Desktop HP EliteDesk (PPLG1-PC-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 1,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => 'Baik',
                'status_detail' => 'Selesai',
                'created_at' => Carbon::parse('2025-10-15'),
                'updated_at' => Carbon::parse('2025-10-19'),
            ],
            [
                'peminjaman_id' => 1,
                'barang_id' => 6, // Monitor LG 24 Inch (PPLG1-MON-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 1,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => 'Baik',
                'status_detail' => 'Selesai',
                'created_at' => Carbon::parse('2025-10-15'),
                'updated_at' => Carbon::parse('2025-10-19'),
            ],

            // ============================================================
            // Peminjaman 2: Siti Nurhaliza - LAB PPLG-2 - DIPINJAM
            // ============================================================
            [
                'peminjaman_id' => 2,
                'barang_id' => 23, // Laptop Asus VivoBook (PPLG2-LP-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 0,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => null,
                'status_detail' => 'Dipinjam',
                'created_at' => Carbon::parse('2025-10-18'),
                'updated_at' => Carbon::parse('2025-10-18'),
            ],
            [
                'peminjaman_id' => 2,
                'barang_id' => 33, // Router TP-Link Archer C6 (PPLG2-RTR-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 0,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => null,
                'status_detail' => 'Dipinjam',
                'created_at' => Carbon::parse('2025-10-18'),
                'updated_at' => Carbon::parse('2025-10-18'),
            ],

            // ============================================================
            // Peminjaman 3: Budi Santoso - LAB PPLG-3 - DIPINJAM
            // ============================================================
            [
                'peminjaman_id' => 3,
                'barang_id' => 51, // PC Gaming Custom Ryzen 5 (PPLG3-PC-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 0,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => null,
                'status_detail' => 'Dipinjam',
                'created_at' => Carbon::parse('2025-10-20'),
                'updated_at' => Carbon::parse('2025-10-20'),
            ],
            [
                'peminjaman_id' => 3,
                'barang_id' => 59, // Graphics Card GTX 1650 (PPLG3-GPU-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 0,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => null,
                'status_detail' => 'Dipinjam',
                'created_at' => Carbon::parse('2025-10-20'),
                'updated_at' => Carbon::parse('2025-10-20'),
            ],
            [
                'peminjaman_id' => 3,
                'barang_id' => 71, // Camera DSLR Canon EOS 1500D (PPLG3-CAM-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 0,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => null,
                'status_detail' => 'Dipinjam',
                'created_at' => Carbon::parse('2025-10-20'),
                'updated_at' => Carbon::parse('2025-10-20'),
            ],

            // ============================================================
            // Peminjaman 4: Dewi Lestari - RUANG UKS - DIKEMBALIKAN
            // ============================================================
            [
                'peminjaman_id' => 4,
                'barang_id' => 80, // Tensimeter Digital Omron (UKS-TNS-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 1,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => 'Baik',
                'status_detail' => 'Selesai',
                'created_at' => Carbon::parse('2025-10-13'),
                'updated_at' => Carbon::parse('2025-10-17'),
            ],
            [
                'peminjaman_id' => 4,
                'barang_id' => 83, // Thermometer Digital (UKS-TRM-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 1,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => 'Baik',
                'status_detail' => 'Selesai',
                'created_at' => Carbon::parse('2025-10-13'),
                'updated_at' => Carbon::parse('2025-10-17'),
            ],
            [
                'peminjaman_id' => 4,
                'barang_id' => 90, // Brankar Lipat (UKS-BRK-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 1,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => 'Baik',
                'status_detail' => 'Selesai',
                'created_at' => Carbon::parse('2025-10-13'),
                'updated_at' => Carbon::parse('2025-10-17'),
            ],

            // ============================================================
            // Peminjaman 5: Rizki Ramadhan - LAB PPLG-1 - DIPINJAM
            // ============================================================
            [
                'peminjaman_id' => 5,
                'barang_id' => 11, // Keyboard Logitech K120 (PPLG1-KBD-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 0,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => null,
                'status_detail' => 'Dipinjam',
                'created_at' => Carbon::parse('2025-10-22'),
                'updated_at' => Carbon::parse('2025-10-22'),
            ],
            [
                'peminjaman_id' => 5,
                'barang_id' => 16, // Mouse Logitech M90 (PPLG1-MOU-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 0,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => null,
                'status_detail' => 'Dipinjam',
                'created_at' => Carbon::parse('2025-10-22'),
                'updated_at' => Carbon::parse('2025-10-22'),
            ],
            [
                'peminjaman_id' => 5,
                'barang_id' => 21, // Projector Epson EB-X49 (PPLG1-PRJ-01)
                'jumlah' => 1,
                'dapat_dikembalikan' => true,
                'jumlah_dikembalikan' => 0,
                'kondisi_awal' => 'Baik',
                'kondisi_akhir' => null,
                'status_detail' => 'Dipinjam',
                'created_at' => Carbon::parse('2025-10-22'),
                'updated_at' => Carbon::parse('2025-10-22'),
            ],
        ];

        DB::table('peminjaman_details')->insert($details);

        $this->command->info('✅ 5 Peminjaman (Oktober 2025) dengan detail berhasil di-seed!');
        $this->command->info('   ├── PJM-2025-001: Ahmad Fauzi (LAB PPLG-1) - 2 item - Dikembalikan');
        $this->command->info('   ├── PJM-2025-002: Siti Nurhaliza (LAB PPLG-2) - 2 item - Dipinjam');
        $this->command->info('   ├── PJM-2025-003: Budi Santoso (LAB PPLG-3) - 3 item - Dipinjam');
        $this->command->info('   ├── PJM-2025-004: Dewi Lestari (Ruang UKS) - 3 item - Dikembalikan');
        $this->command->info('   └── PJM-2025-005: Rizki Ramadhan (LAB PPLG-1) - 3 item - Dipinjam');
    }
}