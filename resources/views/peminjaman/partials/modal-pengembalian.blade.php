<!-- Modal Bootstrap Native -->
<div class="modal fade" id="returnModal{{ $peminjaman->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('peminjaman.return', $peminjaman) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Pengembalian Barang</h5>
                        <p class="mb-0 mt-2"><strong>Kode:</strong> {{ $peminjaman->kode_peminjaman }}</p>
                        <p class="mb-0"><strong>Peminjam:</strong> {{ $peminjaman->nama_peminjam }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @if($peminjaman->isLate())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <strong>PERHATIAN: Pengembalian Terlambat {{ $peminjaman->getDaysLate() }} Hari!</strong>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px" class="text-center">
                                        <input type="checkbox" class="form-check-input" id="checkAll{{ $peminjaman->id }}">
                                    </th>
                                    <th style="width: 120px">Kode</th>
                                    <th>Nama Barang</th>
                                    <th style="width: 100px">Tipe</th>
                                    <th style="width: 180px">Status</th>
                                    <th style="width: 150px">Jumlah</th>
                                    <th style="width: 180px">Kondisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $returnableDetails = $peminjaman->details->where('dapat_dikembalikan', true)->where('status_detail', '!=', 'Dikembalikan');
                                @endphp

                                @forelse($returnableDetails as $detail)
                                @php
                                $barang = $detail->barang;
                                $sisaPinjam = $detail->getRemainingQuantity();
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox"
                                            class="form-check-input item-check-{{ $peminjaman->id }}"
                                            name="detail_ids[]"
                                            value="{{ $detail->id }}"
                                            data-detail="{{ $detail->id }}"
                                            data-modal="{{ $peminjaman->id }}">
                                    </td>
                                    <td><small>{{ $barang->kode_barang }}</small></td>
                                    <td>{{ $barang->nama_barang }}</td>
                                    <td><span class="badge bg-secondary">{{ $barang->mode_input }}</span></td>
                                    <td>
                                        @if($barang->mode_input === 'Per Unit')
                                        <span class="badge bg-primary">Dipinjam: 1</span><br>
                                        <small class="text-muted">Kembali: 0</small>
                                        @else
                                        <small>Dipinjam: {{ $detail->jumlah }}</small><br>
                                        <small>Kembali: {{ $detail->jumlah_dikembalikan }}</small><br>
                                        <strong class="text-primary">Sisa: {{ $sisaPinjam }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if($barang->mode_input === 'Per Unit')
                                        <input type="hidden" name="jumlah_kembali[{{ $detail->id }}]" value="1">
                                        <input type="number" class="form-control form-control-sm" value="1" readonly disabled>
                                        @else
                                        <input type="number"
                                            class="form-control form-control-sm jumlah-{{ $peminjaman->id }}-{{ $detail->id }}"
                                            name="jumlah_kembali[{{ $detail->id }}]"
                                            min="1"
                                            max="{{ $sisaPinjam }}"
                                            value="{{ $sisaPinjam }}"
                                            disabled>
                                        <small class="text-muted">Max: {{ $sisaPinjam }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm kondisi-{{ $peminjaman->id }}-{{ $detail->id }}"
                                            name="kondisi_akhir[{{ $detail->id }}]"
                                            disabled>
                                            <option value="">Pilih...</option>
                                            <option value="Baik" selected>Baik</option>
                                            <option value="Rusak Ringan">Rusak Ringan</option>
                                            <option value="Rusak Berat">Rusak Berat</option>
                                        </select>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Semua barang sudah dikembalikan.
                                    </td>
                                </tr>
                                @endforelse

                                @if($peminjaman->details->where('dapat_dikembalikan', false)->count() > 0)
                                <tr>
                                    <td colspan="7" class="bg-light">
                                        <div class="alert alert-info mb-0">
                                            <i class="bi bi-info-circle"></i>
                                            <strong>Barang Consumable (Tidak Perlu Dikembalikan):</strong>
                                            <ul class="mb-0 mt-2">
                                                @foreach($peminjaman->details->where('dapat_dikembalikan', false) as $detail)
                                                <li>{{ $detail->barang->nama_barang }} ({{ $detail->jumlah }} {{ $detail->barang->satuan }})</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($returnableDetails->count() > 0)
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSubmit{{ $peminjaman->id }}" disabled>
                        <i class="bi bi-check-circle"></i> Simpan Pengembalian
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const modalId = {{ $peminjaman->id }};
    const modalElement = document.getElementById('returnModal' + modalId);
    
    if (!modalElement) {
        console.error('Modal not found:', modalId);
        return;
    }
    
    modalElement.addEventListener('shown.bs.modal', function() {
        console.log('Modal opened:', modalId);
        
        const checkAll = document.getElementById('checkAll' + modalId);
        const checkboxes = document.querySelectorAll('.item-check-' + modalId);
        const btnSubmit = document.getElementById('btnSubmit' + modalId);
        
        console.log('Elements found:', {
            checkAll: !!checkAll,
            checkboxes: checkboxes.length,
            btnSubmit: !!btnSubmit
        });
        
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                console.log('Check All clicked:', this.checked);
                checkboxes.forEach(function(cb) {
                    cb.checked = checkAll.checked;
                    toggleInputs(cb);
                });
                updateButton();
            });
        }
        
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                console.log('Checkbox changed:', this.value, 'Checked:', this.checked);
                toggleInputs(this);
                updateButton();
            });
        });
        
        function toggleInputs(checkbox) {
            const detailId = checkbox.getAttribute('data-detail');
            const jumlahInput = document.querySelector('.jumlah-' + modalId + '-' + detailId);
            const kondisiSelect = document.querySelector('.kondisi-' + modalId + '-' + detailId);
            
            console.log('Toggle inputs for detail:', detailId, 'Checked:', checkbox.checked);
            
            if (checkbox.checked) {
                if (jumlahInput) {
                    jumlahInput.disabled = false;
                    console.log('Jumlah input enabled');
                }
                if (kondisiSelect) {
                    kondisiSelect.disabled = false;
                    console.log('Kondisi select enabled');
                }
            } else {
                if (jumlahInput) jumlahInput.disabled = true;
                if (kondisiSelect) kondisiSelect.disabled = true;
            }
        }
        
        function updateButton() {
            const anyChecked = Array.from(checkboxes).some(function(cb) {
                return cb.checked;
            });
            
            console.log('Update button, any checked:', anyChecked);
            
            if (btnSubmit) {
                btnSubmit.disabled = !anyChecked;
                console.log('Button disabled:', btnSubmit.disabled);
            }
        }
    });
})();
</script>