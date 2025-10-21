<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangs = [];

        // Helper function untuk membuat barang Per Unit
        $createPerUnitBarangs = function($kodePrefix, $namaBarang, $kategoriId, $lokasiId, $satuan, 
                                        $kondisi, $sumber, $status, $tanggalPengadaan, $gambar, $jumlahUnit) {
            $records = [];
            for ($i = 1; $i <= $jumlahUnit; $i++) {
                $kodeBarang = $kodePrefix . str_pad($i, 2, '0', STR_PAD_LEFT);
                $records[] = [
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => $namaBarang,
                    'kategori_id' => $kategoriId,
                    'lokasi_id' => $lokasiId,
                    'jumlah' => 1,
                    'satuan' => $satuan,
                    'kondisi' => $kondisi,
                    'sumber' => $sumber,
                    'mode_input' => 'Per Unit',
                    'status' => $status,
                    'dapat_dikembalikan' => true,
                    'tanggal_pengadaan' => $tanggalPengadaan,
                    'gambar' => $gambar,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            return $records;
        };

        // ========================================
        // LAB PPLG-1 (ID: 1) - 5 Jenis Barang
        // ========================================
        $barangs = array_merge($barangs, 
            $createPerUnitBarangs('PPLG1-PC', 'PC Desktop HP EliteDesk 800 G6', 1, 1, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-01-15', null, 5)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG1-MON', 'Monitor LG 24 Inch Full HD', 3, 1, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-01-15', null, 5)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG1-KBD', 'Keyboard Logitech K120', 3, 1, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-01-15', null, 5)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG1-MOU', 'Mouse Logitech M90', 3, 1, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-01-15', null, 5)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG1-PRJ', 'Projector Epson EB-X49', 5, 1, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-02-01', null, 2)
        );

        // ========================================
        // LAB PPLG-2 (ID: 2) - 5 Jenis Barang
        // ========================================
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG2-LP', 'Laptop Asus VivoBook 15', 1, 2, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-02-10', null, 10)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG2-RTR', 'Router TP-Link Archer C6', 2, 2, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-02-15', null, 3)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG2-SWH', 'Switch D-Link 24 Port', 2, 2, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-02-15', null, 2)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG2-HDD', 'External HDD Seagate 1TB', 6, 2, 'Unit', 'Baik', 'Swadaya', 'Tersedia', '2024-03-01', null, 8)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG2-WEB', 'Webcam Logitech C270', 5, 2, 'Unit', 'Baik', 'Swadaya', 'Tersedia', '2024-03-10', null, 5)
        );

        // ========================================
        // LAB PPLG-3 (ID: 3) - 5 Jenis Barang
        // ========================================
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG3-PC', 'PC Gaming Custom Ryzen 5', 1, 3, 'Unit', 'Baik', 'Swadaya', 'Tersedia', '2024-03-15', null, 8)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG3-GPU', 'Graphics Card GTX 1650', 1, 3, 'Unit', 'Baik', 'Swadaya', 'Tersedia', '2024-03-15', null, 8)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG3-MIC', 'Microphone USB Rode NT', 5, 3, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-04-01', null, 4)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG3-CAM', 'Camera DSLR Canon EOS 1500D', 5, 3, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-04-05', null, 3)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('PPLG3-TAB', 'Drawing Tablet Wacom Intuos', 3, 3, 'Unit', 'Baik', 'Swadaya', 'Tersedia', '2024-04-10', null, 6)
        );

        // ========================================
        // RUANG UKS (ID: 4) - 5 Jenis Barang
        // ========================================
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('UKS-TNS', 'Tensimeter Digital Omron', 7, 4, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-01-20', null, 3)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('UKS-TRM', 'Thermometer Digital', 7, 4, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-01-20', null, 5)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('UKS-STT', 'Stetoskop Littmann', 7, 4, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-01-25', null, 2)
        );
        
        $barangs = array_merge($barangs,
            $createPerUnitBarangs('UKS-BRK', 'Brankar Lipat', 9, 4, 'Unit', 'Baik', 'Pemerintah', 'Tersedia', '2024-02-01', null, 2)
        );
        
        $barangs[] = [
            'kode_barang' => 'UKS-OBT-001',
            'nama_barang' => 'Kotak P3K Lengkap',
            'kategori_id' => 8,
            'lokasi_id' => 4,
            'jumlah' => 10,
            'satuan' => 'Set',
            'kondisi' => 'Baik',
            'sumber' => 'Pemerintah',
            'mode_input' => 'Masal',
            'status' => 'Tersedia',
            'dapat_dikembalikan' => false,
            'tanggal_pengadaan' => '2024-02-05',
            'gambar' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert semua data
        DB::table('barangs')->insert($barangs);

        $totalBarang = count($barangs);
        $this->command->info('✅ ' . $totalBarang . ' Barang berhasil di-seed!');
        $this->command->info('   ├── LAB PPLG-1: 22 item (5 PC, 5 Monitor, 5 Keyboard, 5 Mouse, 2 Projector)');
        $this->command->info('   ├── LAB PPLG-2: 28 item (10 Laptop, 3 Router, 2 Switch, 8 HDD, 5 Webcam)');
        $this->command->info('   ├── LAB PPLG-3: 29 item (8 PC Gaming, 8 GPU, 4 Mic, 3 Camera, 6 Tablet)');
        $this->command->info('   └── Ruang UKS: 13 item (3 Tensimeter, 5 Thermometer, 2 Stetoskop, 2 Brankar, 1 P3K)');
    }
}