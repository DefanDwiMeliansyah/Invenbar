<div class="row">
    <div class="col">
        <x-tombol-tambah label="Tambah Barang" href="{{ route('peminjaman.create') }}" />
        <x-tombol-cetak label="Cetak Laporan Barang" href="{{ route('peminjaman.laporan-form') }}" />
    </div>
    
    <div class="col">
        <x-form-search placeholder="Cari nama/kode peminjaman..." />
    </div>
</div>