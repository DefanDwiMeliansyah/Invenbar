<x-main-layout title-page="Laporan Peminjaman">
    <div class="card">
        <div class="card-body">
            <div class="mb-4">
                <h5 class="card-title">Filter Laporan Peminjaman</h5>
                <p class="text-muted">Pilih filter untuk menghasilkan laporan sesuai kebutuhan</p>
            </div>

            <x-notif-alert />

            <form action="{{ route('peminjaman.cetak-laporan') }}" method="GET" target="_blank">
                <div class="row">
                    <!-- Rentang Tanggal -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Pinjam Dari</label>
                            <input type="date" 
                                   name="tanggal_dari" 
                                   class="form-control"
                                   value="{{ old('tanggal_dari') }}">
                            <small class="text-muted">Kosongkan untuk semua tanggal</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Pinjam Sampai</label>
                            <input type="date" 
                                   name="tanggal_sampai" 
                                   class="form-control"
                                   value="{{ old('tanggal_sampai') }}">
                        </div>
                    </div>

                    <!-- Status Peminjaman -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status Peminjaman</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="Dipinjam" {{ old('status') == 'Dipinjam' ? 'selected' : '' }}>
                                    Dipinjam
                                </option>
                                <option value="Dikembalikan" {{ old('status') == 'Dikembalikan' ? 'selected' : '' }}>
                                    Dikembalikan
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Lokasi -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Lokasi</label>
                            <select name="lokasi_id" class="form-select">
                                <option value="">Semua Lokasi</option>
                                @foreach($lokasi as $lok)
                                    <option value="{{ $lok->id }}" {{ old('lokasi_id') == $lok->id ? 'selected' : '' }}>
                                        {{ $lok->nama_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Nama Peminjam -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama Peminjam</label>
                            <input type="text" 
                                   name="nama_peminjam" 
                                   class="form-control"
                                   placeholder="Cari nama peminjam..."
                                   value="{{ old('nama_peminjam') }}">
                            <small class="text-muted">Kosongkan untuk semua peminjam</small>
                        </div>
                    </div>

                    <!-- Kelompokkan Berdasarkan -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Kelompokkan Berdasarkan</label>
                            <select name="group_by" class="form-select">
                                <option value="none" {{ old('group_by') == 'none' ? 'selected' : '' }}>
                                    Tidak Dikelompokkan
                                </option>
                                <option value="status" {{ old('group_by') == 'status' ? 'selected' : '' }}>
                                    Status
                                </option>
                                <option value="lokasi" {{ old('group_by') == 'lokasi' ? 'selected' : '' }}>
                                    Lokasi
                                </option>
                                <option value="bulan" {{ old('group_by') == 'bulan' ? 'selected' : '' }}>
                                    Bulan
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        Laporan akan dibuka di tab baru dalam format PDF
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-file-earmark-pdf"></i> Generate Laporan PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card mt-3">
        <div class="card-body">
            <h6 class="card-title"><i class="bi bi-lightbulb"></i> Informasi</h6>
            <ul class="mb-0">
                <li>Laporan akan menampilkan data peminjaman beserta detail barang yang dipinjam</li>
                <li>Filter dapat dikombinasikan untuk menghasilkan laporan yang lebih spesifik</li>
                <li>Statistik ringkasan akan ditampilkan di bagian atas laporan</li>
                <li>Informasi keterlambatan akan ditandai dengan warna merah</li>
                <li>Barang consumable (tidak kembali) akan ditandai khusus</li>
            </ul>
        </div>
    </div>
</x-main-layout>