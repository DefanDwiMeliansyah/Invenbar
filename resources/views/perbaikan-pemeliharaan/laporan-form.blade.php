<x-main-layout title-page="{{ __('Cetak Laporan Perbaikan dan Pemeliharaan') }}">
    <div class="card">
        <div class="card-body">
            <x-notif-alert />

            <form action="{{ route('perbaikan-pemeliharaan.cetak-laporan') }}" method="GET" target="_blank">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <x-form-input
                                type="date"
                                name="tanggal_dari"
                                label="Tanggal Dari"
                                :value="old('tanggal_dari')" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <x-form-input
                                type="date"
                                name="tanggal_sampai"
                                label="Tanggal Sampai"
                                :value="old('tanggal_sampai')" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="Diajukan">Diajukan</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Dalam Perbaikan">Dalam Perbaikan</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jenis" class="form-label">Jenis</label>
                            <select name="jenis" id="jenis" class="form-select">
                                <option value="">Semua Jenis</option>
                                <option value="Perbaikan">Perbaikan</option>
                                <option value="Pemeliharaan Rutin">Pemeliharaan Rutin</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="prioritas" class="form-label">Prioritas</label>
                            <select name="prioritas" id="prioritas" class="form-select">
                                <option value="">Semua Prioritas</option>
                                <option value="Urgent">Urgent</option>
                                <option value="Tinggi">Tinggi</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Rendah">Rendah</option>
                            </select>
                        </div>
                    </div>

                    @if(auth()->user()->isAdmin())
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-form-select
                                    name="lokasi_id"
                                    label="Lokasi"
                                    :optionData="$lokasi"
                                    optionValue="id"
                                    optionLabel="nama_lokasi"
                                    :value="old('lokasi_id')" />
                            </div>
                        </div>
                    @endif
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('perbaikan-pemeliharaan.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-printer"></i> Cetak Laporan Perbaikan & Pemeliharaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-main-layout>