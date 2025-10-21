<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <x-form-select
                name="barang_id"
                label="Pilih Barang *"
                :optionData="$barangs"
                optionValue="id"
                optionLabel="nama_barang"
                :value="old('barang_id', $perbaikan->barang_id ?? '')" />
            <small class="text-muted">
                <i class="bi bi-info-circle"></i> Hanya menampilkan barang yang tersedia
            </small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <x-form-select
                name="jenis"
                label="Jenis *"
                :optionData="[
                    ['value' => 'Perbaikan', 'label' => 'Perbaikan'],
                    ['value' => 'Pemeliharaan Rutin', 'label' => 'Pemeliharaan Rutin']
                ]"
                optionValue="value"
                optionLabel="label"
                :value="old('jenis', $perbaikan->jenis ?? '')" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <x-form-select
                name="prioritas"
                label="Prioritas *"
                :optionData="[
                    ['value' => 'Rendah', 'label' => 'Rendah'],
                    ['value' => 'Sedang', 'label' => 'Sedang'],
                    ['value' => 'Tinggi', 'label' => 'Tinggi'],
                    ['value' => 'Urgent', 'label' => 'Urgent']
                ]"
                optionValue="value"
                optionLabel="label"
                :value="old('prioritas', $perbaikan->prioritas ?? 'Sedang')" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <x-form-input
                type="date"
                name="tanggal_pengajuan"
                label="Tanggal Pengajuan *"
                :value="old('tanggal_pengajuan', $perbaikan->tanggal_pengajuan ?? now()->format('Y-m-d'))" />
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label for="keluhan" class="form-label">Keluhan / Keterangan *</label>
            <textarea 
                name="keluhan" 
                id="keluhan" 
                class="form-control @error('keluhan') is-invalid @enderror" 
                rows="4" 
                placeholder="Jelaskan kondisi barang atau kebutuhan pemeliharaan..."
            >{{ old('keluhan', $perbaikan->keluhan ?? '') }}</textarea>
            @error('keluhan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    <strong>Catatan:</strong> Setelah pengajuan dibuat, status barang akan otomatis berubah menjadi 
    <strong>"Diperbaiki"</strong> atau <strong>"Perawatan"</strong> dan tidak dapat dipinjam hingga proses selesai.
</div>

<hr class="my-4">

<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('perbaikan-pemeliharaan.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle"></i> Batal
    </a>
    <x-primary-button>
        <i class="bi bi-send"></i> Ajukan Perbaikan
    </x-primary-button>
</div>