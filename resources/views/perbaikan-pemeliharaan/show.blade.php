<x-main-layout title-page="Detail Perbaikan & Pemeliharaan">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <x-notif-alert />

                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h4 class="mb-1">{{ $perbaikanPemeliharaan->kode_perbaikan }}</h4>
                            <div class="mt-2">
                                <span class="badge {{ $perbaikanPemeliharaan->getStatusBadgeClass() }} fs-6">
                                    {{ $perbaikanPemeliharaan->status }}
                                </span>
                                <span class="badge {{ $perbaikanPemeliharaan->getPrioritasBadgeClass() }} fs-6 ms-1">
                                    {{ $perbaikanPemeliharaan->prioritas }}
                                </span>
                                <span class="badge {{ $perbaikanPemeliharaan->getJenisBadgeClass() }} fs-6 ms-1">
                                    {{ $perbaikanPemeliharaan->jenis }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('perbaikan-pemeliharaan.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <hr>

                    <h5 class="mb-3">Informasi Barang</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Kode Barang</th>
                                    <td>{{ $perbaikanPemeliharaan->barang->kode_barang }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Barang</th>
                                    <td>{{ $perbaikanPemeliharaan->barang->nama_barang }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>{{ $perbaikanPemeliharaan->barang->kategori->nama_kategori }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Lokasi</th>
                                    <td>{{ $perbaikanPemeliharaan->barang->lokasi->nama_lokasi }}</td>
                                </tr>
                                <tr>
                                    <th>Status Barang</th>
                                    <td>
                                        <span class="badge {{ $perbaikanPemeliharaan->barang->getStatusBadgeClass() }}">
                                            {{ $perbaikanPemeliharaan->barang->status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kondisi Barang</th>
                                    <td>{{ $perbaikanPemeliharaan->barang->kondisi }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3">Detail Pengajuan</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Tanggal Pengajuan</th>
                                    <td>{{ $perbaikanPemeliharaan->tanggal_pengajuan->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Diajukan Oleh</th>
                                    <td>{{ $perbaikanPemeliharaan->user->name }}</td>
                                </tr>
                                @if($perbaikanPemeliharaan->approved_by)
                                    <tr>
                                        <th>Disetujui Oleh</th>
                                        <td>{{ $perbaikanPemeliharaan->approvedBy->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Approval</th>
                                        <td>{{ $perbaikanPemeliharaan->approved_at->format('d F Y H:i') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Keluhan</th>
                                    <td>{{ $perbaikanPemeliharaan->keluhan }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($perbaikanPemeliharaan->status !== 'Diajukan')
                        <hr>
                        <h5 class="mb-3">Detail Perbaikan</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    @if($perbaikanPemeliharaan->teknisi)
                                        <tr>
                                            <th width="40%">Teknisi</th>
                                            <td>{{ $perbaikanPemeliharaan->teknisi }}</td>
                                        </tr>
                                    @endif
                                    @if($perbaikanPemeliharaan->tanggal_mulai)
                                        <tr>
                                            <th>Tanggal Mulai</th>
                                            <td>{{ $perbaikanPemeliharaan->tanggal_mulai->format('d F Y') }}</td>
                                        </tr>
                                    @endif
                                    @if($perbaikanPemeliharaan->tanggal_selesai)
                                        <tr>
                                            <th>Tanggal Selesai</th>
                                            <td>{{ $perbaikanPemeliharaan->tanggal_selesai->format('d F Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Durasi</th>
                                            <td>{{ $perbaikanPemeliharaan->getDurasiPerbaikan() }} hari</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    @if($perbaikanPemeliharaan->biaya_perbaikan)
                                        <tr>
                                            <th width="40%">Biaya</th>
                                            <td>Rp {{ number_format($perbaikanPemeliharaan->biaya_perbaikan, 0, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                    @if($perbaikanPemeliharaan->kondisi_akhir)
                                        <tr>
                                            <th>Kondisi Akhir</th>
                                            <td>{{ $perbaikanPemeliharaan->kondisi_akhir }}</td>
                                        </tr>
                                    @endif
                                    @if($perbaikanPemeliharaan->hasil_perbaikan)
                                        <tr>
                                            <th>Hasil Perbaikan</th>
                                            <td>{{ $perbaikanPemeliharaan->hasil_perbaikan }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <!-- Action Buttons -->
                    <div class="mt-4">
                        @can('manage perbaikan-pemeliharaan')
                            @if($perbaikanPemeliharaan->canBeApproved() && auth()->user()->isAdmin())
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                    <i class="bi bi-check-circle"></i> Setujui Perbaikan
                                </button>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="bi bi-x-circle"></i> Batalkan
                                </button>
                            @endif

                            @if($perbaikanPemeliharaan->status === 'Disetujui')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#processModal">
                                    <i class="bi bi-wrench"></i> Mulai Perbaikan
                                </button>
                            @endif

                            @if($perbaikanPemeliharaan->status === 'Dalam Perbaikan')
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeModal">
                                    <i class="bi bi-check-circle"></i> Selesaikan Perbaikan
                                </button>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Timeline</h6>
                    <hr>
                    
                    <div class="timeline">
                        <div class="timeline-item mb-3">
                            <i class="bi bi-circle-fill text-success"></i>
                            <div class="ms-3">
                                <strong>Diajukan</strong>
                                <p class="mb-0 text-muted small">
                                    {{ $perbaikanPemeliharaan->tanggal_pengajuan->format('d M Y') }}<br>
                                    oleh {{ $perbaikanPemeliharaan->user->name }}
                                </p>
                            </div>
                        </div>

                        @if($perbaikanPemeliharaan->approved_at)
                            <div class="timeline-item mb-3">
                                <i class="bi bi-circle-fill text-success"></i>
                                <div class="ms-3">
                                    <strong>Disetujui</strong>
                                    <p class="mb-0 text-muted small">
                                        {{ $perbaikanPemeliharaan->approved_at->format('d M Y H:i') }}<br>
                                        oleh {{ $perbaikanPemeliharaan->approvedBy->name }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($perbaikanPemeliharaan->tanggal_mulai)
                            <div class="timeline-item mb-3">
                                <i class="bi bi-circle-fill {{ $perbaikanPemeliharaan->status === 'Dalam Perbaikan' || $perbaikanPemeliharaan->status === 'Selesai' ? 'text-success' : 'text-secondary' }}"></i>
                                <div class="ms-3">
                                    <strong>Mulai Perbaikan</strong>
                                    <p class="mb-0 text-muted small">
                                        {{ $perbaikanPemeliharaan->tanggal_mulai->format('d M Y') }}<br>
                                        Teknisi: {{ $perbaikanPemeliharaan->teknisi }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($perbaikanPemeliharaan->tanggal_selesai)
                            <div class="timeline-item mb-3">
                                <i class="bi bi-circle-fill text-success"></i>
                                <div class="ms-3">
                                    <strong>Selesai</strong>
                                    <p class="mb-0 text-muted small">
                                        {{ $perbaikanPemeliharaan->tanggal_selesai->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($perbaikanPemeliharaan->status === 'Dibatalkan')
                            <div class="timeline-item mb-3">
                                <i class="bi bi-circle-fill text-danger"></i>
                                <div class="ms-3">
                                    <strong>Dibatalkan</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('perbaikan-pemeliharaan.partials.modal-approval')
</x-main-layout>