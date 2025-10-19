<!-- Modal Bootstrap Native -->
<div class="modal fade" id="returnModalShow" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('peminjaman.return', $peminjaman) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <h5 class="modal-title">Pengembalian Barang</h5>
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
                                        <input type="checkbox" class="form-check-input" id="checkAllShow">
                                    </th>
                                    <th style="width: 120px">Kode</th>
                                    <th>Nama Barang</th>
                                    <th style="width: 100px">Tipe</th>
                                    <th style="width: 180px">Status</th>
                                    <th style="width: 150px">Jumla</th>
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
                                                   class="form-check-input item-check-show" 
                                                   name="detail_ids[]" 
                                                   value="{{ $detail->id }}"
                                                   data-detail="{{ $detail->id }}">
                                        </td>
                                        <td><small>{{ $barang->kode_barang }}</small></td>
                                        <td>{{ $barang->nama_barang }}</td>
                                        <td><span class="badge bg-secondary">{{ $barang->mode_input }}</span></td>
                                        <td>
                                            @if($barang->mode_input === 'Per Unit')
                                                <span class="badge bg-primary">Dipinjam</span>
                                            @else
                                                <small>Sisa: <strong class="text-primary">{{ $sisaPinjam }} {{ $barang->satuan }}</strong></small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($barang->mode_input === 'Per Unit')
                                                <input type="hidden" name="jumlah_kembali[{{ $detail->id }}]" value="1">
                                                <input type="number" class="form-control form-control-sm" value="1" readonly disabled>
                                            @else
                                                <input type="number" 
                                                       class="form-control form-control-sm jumlah-show-{{ $detail->id }}" 
                                                       name="jumlah_kembali[{{ $detail->id }}]"
                                                       min="1" 
                                                       max="{{ $sisaPinjam }}"
                                                       value="{{ $sisaPinjam }}"
                                                       disabled>
                                                <small class="text-muted">Max: {{ $sisaPinjam }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm kondisi-show-{{ $detail->id }}" 
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
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($returnableDetails->count() > 0)
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="btnSubmitShow" disabled>
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
    const modalElement = document.getElementById('returnModalShow');
    
    if (!modalElement) {
        console.error('Modal returnModalShow not found');
        return;
    }
    
    // Init saat modal dibuka
    modalElement.addEventListener('shown.bs.modal', function() {
        console.log('Modal Show opened');
        
        const checkAll = document.getElementById('checkAllShow');
        const checkboxes = document.querySelectorAll('.item-check-show');
        const btnSubmit = document.getElementById('btnSubmitShow');
        
        console.log('Elements:', {
            checkAll: !!checkAll,
            checkboxes: checkboxes.length,
            btnSubmit: !!btnSubmit
        });
        
        // Check All
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                console.log('Check All Show:', this.checked);
                checkboxes.forEach(function(cb) {
                    cb.checked = checkAll.checked;
                    toggleInputs(cb);
                });
                updateButton();
            });
        }
        
        // Individual checkboxes
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                console.log('Checkbox Show changed:', this.value);
                toggleInputs(this);
                updateButton();
            });
        });
        
        function toggleInputs(checkbox) {
            const detailId = checkbox.getAttribute('data-detail');
            const jumlahInput = document.querySelector('.jumlah-show-' + detailId);
            const kondisiSelect = document.querySelector('.kondisi-show-' + detailId);
            
            if (checkbox.checked) {
                if (jumlahInput) {
                    jumlahInput.disabled = false;
                    console.log('Jumlah enabled:', detailId);
                }
                if (kondisiSelect) {
                    kondisiSelect.disabled = false;
                    console.log('Kondisi enabled:', detailId);
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
            
            console.log('Button update:', anyChecked);
            
            if (btnSubmit) {
                btnSubmit.disabled = !anyChecked;
            }
        }
    });
})();
</script>