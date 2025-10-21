<!-- Modal Approve -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('perbaikan-pemeliharaan.approve', $perbaikanPemeliharaan) }}">
            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle text-success"></i>
                    Setujui Perbaikan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyetujui perbaikan ini?</p>
                <div class="alert alert-info">
                    <small>
                        <i class="bi bi-info-circle"></i>
                        Setelah disetujui, perbaikan dapat diproses oleh petugas.
                    </small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Ya, Setujui
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Process (Mulai Perbaikan) -->
<div class="modal fade" id="processModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('perbaikan-pemeliharaan.process', $perbaikanPemeliharaan) }}">
            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-wrench text-primary"></i>
                    Mulai Perbaikan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <x-form-input
                        name="teknisi"
                        label="Nama Teknisi *"
                        placeholder="Masukkan nama teknisi"
                        :value="old('teknisi')" />
                </div>

                <div class="mb-3">
                    <x-form-input
                        type="date"
                        name="tanggal_mulai"
                        label="Tanggal Mulai *"
                        :value="old('tanggal_mulai', now()->format('Y-m-d'))" />
                </div>

                <div class="alert alert-info">
                    <small>
                        <i class="bi bi-info-circle"></i>
                        Status perbaikan akan berubah menjadi "Dalam Perbaikan"
                    </small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-wrench"></i> Mulai Perbaikan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Complete (Selesaikan Perbaikan) -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="{{ route('perbaikan-pemeliharaan.complete', $perbaikanPemeliharaan) }}">
            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle text-success"></i>
                    Selesaikan Perbaikan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <x-form-input
                                type="date"
                                name="tanggal_selesai"
                                label="Tanggal Selesai *"
                                :value="old('tanggal_selesai', now()->format('Y-m-d'))" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <x-form-select
                                name="kondisi_akhir"
                                label="Kondisi Akhir *"
                                :optionData="[
                                    ['value' => 'Baik', 'label' => 'Baik'],
                                    ['value' => 'Rusak Ringan', 'label' => 'Rusak Ringan'],
                                    ['value' => 'Rusak Berat', 'label' => 'Rusak Berat']
                                ]"
                                optionValue="value"
                                optionLabel="label"
                                :value="old('kondisi_akhir')" />
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <x-form-input
                                type="number"
                                name="biaya_perbaikan"
                                label="Biaya Perbaikan (Rp)"
                                placeholder="0"
                                step="0.01"
                                :value="old('biaya_perbaikan')" />
                            <small class="text-muted">Opsional - kosongkan jika tidak ada biaya</small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="hasil_perbaikan" class="form-label">Hasil Perbaikan *</label>
                            <textarea 
                                name="hasil_perbaikan" 
                                id="hasil_perbaikan" 
                                class="form-control @error('hasil_perbaikan') is-invalid @enderror" 
                                rows="4" 
                                placeholder="Jelaskan hasil perbaikan, tindakan yang dilakukan, dll..."
                            >{{ old('hasil_perbaikan') }}</textarea>
                            @error('hasil_perbaikan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="alert alert-success">
                    <small>
                        <i class="bi bi-info-circle"></i>
                        <strong>Status barang akan otomatis diperbarui:</strong><br>
                        - Kondisi "Baik" → Status "Tersedia"<br>
                        - Kondisi "Rusak Ringan/Berat" → Status "Rusak"
                    </small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Selesaikan Perbaikan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Cancel -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('perbaikan-pemeliharaan.cancel', $perbaikanPemeliharaan) }}">
            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle text-danger"></i>
                    Batalkan Perbaikan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="alasan_pembatalan" class="form-label">Alasan Pembatalan *</label>
                    <textarea 
                        name="alasan_pembatalan" 
                        id="alasan_pembatalan" 
                        class="form-control @error('alasan_pembatalan') is-invalid @enderror" 
                        rows="3" 
                        placeholder="Jelaskan alasan pembatalan..."
                        required
                    >{{ old('alasan_pembatalan') }}</textarea>
                    @error('alasan_pembatalan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-warning">
                    <small>
                        <i class="bi bi-exclamation-triangle"></i>
                        Status barang akan dikembalikan menjadi "Tersedia"
                    </small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>