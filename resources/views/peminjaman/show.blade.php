<x-main-layout title-page="Detail Peminjaman">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <x-notif-alert />

                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h4 class="mb-1">{{ $peminjaman->kode_peminjaman }}</h4>
                            <span class="badge {{ $peminjaman->getStatusBadgeClass() }} fs-6">
                                {{ $peminjaman->status }}
                            </span>
                            @if($peminjaman->isLate())
                                <span class="badge bg-danger fs-6 ms-1">
                                    Terlambat {{ $peminjaman->getDaysLate() }} hari
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <hr>

                    <h5 class="mb-3">Informasi Peminjaman</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Nama Peminjam</th>
                                    <td>{{ $peminjaman->nama_peminjam }}</td>
                                </tr>
                                <tr>
                                    <th>Nomor Telepon</th>
                                    <td>{{ $peminjaman->nomor_telepon }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $peminjaman->email }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Tanggal Pinjam</th>
                                    <td>{{ $peminjaman->tanggal_pinjam->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Batas Pengembalian</th>
                                    <td>
                                        {{ $peminjaman->tanggal_batas_pengembalian->format('d F Y') }}
                                        @if($peminjaman->isLate())
                                            <span class="badge bg-danger">Lewat Batas</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lokasi</th>
                                    <td>{{ $peminjaman->lokasi->nama_lokasi }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td>{{ $peminjaman->user->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($peminjaman->isLate() && $peminjaman->status === 'Dipinjam')
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <strong>PERHATIAN:</strong> Peminjaman ini terlambat {{ $peminjaman->getDaysLate() }} hari!
                            Barang masal yang dikembalikan akan berstatus <strong>"Sekali Pinjam"</strong> dan tidak kembali ke stok.
                        </div>
                    @endif

                    <hr>

                    <h5 class="mb-3">Detail Barang</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 15%">Kode</th>
                                    <th style="width: 20%">Nama Barang</th>
                                    <th style="width: 10%">Kategori</th>
                                    <th style="width: 15%">Jumlah</th>
                                    <th style="width: 12%">Kondisi</th>
                                    <th style="width: 10%">Status</th>
                                    <th style="width: 18%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($peminjaman->details as $detail)
                                    @php
                                        $barang = $detail->barang;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $barang->kode_barang }}</td>
                                        <td>
                                            {{ $barang->nama_barang }}
                                            @if(!$detail->dapat_dikembalikan)
                                                <span class="badge bg-danger">Consumable</span>
                                            @endif
                                        </td>
                                        <td>{{ $barang->kategori->nama_kategori }}</td>
                                        <td>
                                            @if($barang->mode_input === 'Per Unit')
                                                1 {{ $barang->satuan }}
                                            @else
                                                <div>
                                                    <small>Dipinjam: {{ $detail->jumlah }} {{ $barang->satuan }}</small><br>
                                                    <small>Dikembalikan: {{ $detail->jumlah_dikembalikan }} {{ $barang->satuan }}</small><br>
                                                    @if($detail->dapat_dikembalikan && $detail->status_detail === 'Dipinjam')
                                                        <strong class="text-primary">Sisa: {{ $detail->getRemainingQuantity() }} {{ $barang->satuan }}</strong>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                <strong>Awal:</strong> 
                                                <span class="badge bg-secondary">{{ $detail->kondisi_awal }}</span>
                                            </small><br>
                                            @if($detail->kondisi_akhir)
                                                <small>
                                                    <strong>Akhir:</strong> 
                                                    <span class="badge bg-secondary">{{ $detail->kondisi_akhir }}</span>
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $detail->getStatusBadgeClass() }}">
                                                {{ $detail->status_detail }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($detail->keterangan)
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-exclamation-triangle"></i> {{ $detail->keterangan }}
                                                </span>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($peminjaman->status === 'Dipinjam')
                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#returnModalShow">
                                <i class="bi bi-box-arrow-in-left"></i> Proses Pengembalian
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Ringkasan</h6>
                    <hr>
                    <div class="mb-3">
                        <small class="text-muted">Total Barang Dipinjam</small>
                        <h4 class="mb-0">{{ $peminjaman->details->count() }} Item</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Barang yang Harus Dikembalikan</small>
                        <h4 class="mb-0">
                            {{ $peminjaman->details->where('dapat_dikembalikan', true)->count() }} Item
                        </h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Barang Sudah Dikembalikan</small>
                        <h4 class="mb-0 text-success">
                            {{ $peminjaman->details->where('status_detail', 'Dikembalikan')->count() }} Item
                        </h4>
                    </div>
                    @if($peminjaman->details->where('dapat_dikembalikan', false)->count() > 0)
                        <div class="mb-3">
                            <small class="text-muted">Barang Consumable (Tidak Kembali)</small>
                            <h4 class="mb-0 text-secondary">
                                {{ $peminjaman->details->where('dapat_dikembalikan', false)->count() }} Item
                            </h4>
                        </div>
                    @endif
                    @if($peminjaman->details->whereNotNull('keterangan')->count() > 0)
                        <div>
                            <small class="text-muted">Barang Sekali Pinjam</small>
                            <h4 class="mb-0 text-warning">
                                {{ $peminjaman->details->whereNotNull('keterangan')->count() }} Item
                            </h4>
                        </div>
                    @endif
                </div>
            </div>

            @if($peminjaman->isLate())
                <div class="card mt-3 border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i> Keterlambatan
                        </h6>
                        <hr>
                        <p class="mb-2">
                            Peminjaman ini sudah melewati batas pengembalian 
                            <strong>{{ $peminjaman->getDaysLate() }} hari</strong>.
                        </p>
                        @if($peminjaman->status === 'Dipinjam')
                            <div class="alert alert-warning mb-0 mt-2">
                                <small>
                                    <i class="bi bi-info-circle"></i>
                                    Barang <strong>masal</strong> yang dikembalikan akan berstatus 
                                    <strong>"Sekali Pinjam"</strong> dan tidak kembali ke stok.
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($peminjaman->status === 'Dipinjam')
        @include('peminjaman.partials.modal-pengembalian-show', ['peminjaman' => $peminjaman])
    @endif
</x-main-layout>