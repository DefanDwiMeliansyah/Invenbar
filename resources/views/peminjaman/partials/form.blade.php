<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <x-form-input
                name="nama_peminjam"
                label="Nama Peminjam *"
                :value="old('nama_peminjam', $peminjaman->nama_peminjam ?? '')" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <x-form-input
                name="nomor_telepon"
                label="Nomor Telepon *"
                :value="old('nomor_telepon', $peminjaman->nomor_telepon ?? '')" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <x-form-input
                type="email"
                name="email"
                label="Email *"
                :value="old('email', $peminjaman->email ?? '')" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <x-form-select
                name="lokasi_id"
                label="Lokasi *"
                :optionData="$lokasi"
                optionValue="id"
                optionLabel="nama_lokasi"
                :value="old('lokasi_id', $peminjaman->lokasi_id ?? auth()->user()->lokasi_id)"
                :disabled="auth()->user()->isPetugas()" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <x-form-input
                type="date"
                name="tanggal_pinjam"
                label="Tanggal Pinjam *"
                :value="old('tanggal_pinjam', $peminjaman->tanggal_pinjam ?? now()->format('Y-m-d'))" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <x-form-input
                type="date"
                name="tanggal_batas_pengembalian"
                label="Batas Pengembalian *"
                :value="old('tanggal_batas_pengembalian', $peminjaman->tanggal_batas_pengembalian ?? '')" />
        </div>
    </div>
</div>

<hr class="my-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Barang yang Dipinjam</h5>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPilihBarang">
        <i class="bi bi-plus-circle"></i> Pilih Barang
    </button>
</div>

<div id="selectedBarangContainer">
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Belum ada barang dipilih. Klik tombol "Pilih Barang" untuk menambahkan.
    </div>
</div>

<hr class="my-4">

<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle"></i> Batal
    </a>
    <x-primary-button id="btnSubmit" disabled>
        <i class="bi bi-save"></i> Simpan Peminjaman
    </x-primary-button>
</div>