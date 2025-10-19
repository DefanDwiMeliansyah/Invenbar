<x-modal name="modalPilihBarang" maxWidth="xl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="modal-title">Pilih Barang untuk Dipinjam</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="mb-3">
        <input type="text" class="form-control" id="searchBarang" placeholder="🔍 Cari nama barang...">
    </div>

    <div id="barangListContainer">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat data barang...</p>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnTambahkanBarang">
            <i class="bi bi-check-circle"></i> Tambahkan Barang
        </button>
    </div>
</x-modal>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let selectedBarang = [];
        let availableBarang = [];

        const modalElement = document.getElementById('modalPilihBarang');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        const lokasiSelect = document.getElementById('lokasi_id');
        const searchInput = document.getElementById('searchBarang');
        const containerList = document.getElementById('barangListContainer');
        const containerSelected = document.getElementById('selectedBarangContainer');
        const btnSubmit = document.getElementById('btnSubmit');

        // Load barang when modal is shown
        modalElement.addEventListener('show.bs.modal', function() {
            loadAvailableBarang();
        });

        // Load available barang via AJAX
        function loadAvailableBarang() {
            const lokasiId = lokasiSelect?.value;

            if (!lokasiId) {
                containerList.innerHTML = `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Pilih lokasi terlebih dahulu
                </div>
            `;
                return;
            }

            containerList.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Memuat data barang...</p>
            </div>
        `;

            fetch(`/peminjaman/barang-tersedia?lokasi_id=${lokasiId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    availableBarang = [];

                    // Flatten grouped data
                    for (const [kategori, barangs] of Object.entries(data.data)) {
                        barangs.forEach(barang => {
                            availableBarang.push({
                                ...barang,
                                kategori_nama: kategori
                            });
                        });
                    }

                    renderBarangList(availableBarang);
                })
                .catch(error => {
                    console.error('Error:', error);
                    containerList.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle"></i> Gagal memuat data barang
                    </div>
                `;
                });
        }

        // Render barang list
        function renderBarangList(barangs) {
            if (barangs.length === 0) {
                containerList.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tidak ada barang tersedia di lokasi ini
                </div>
            `;
                return;
            }

            // Group by kategori
            const grouped = barangs.reduce((acc, barang) => {
                const kategori = barang.kategori_nama || 'Tanpa Kategori';
                if (!acc[kategori]) acc[kategori] = [];
                acc[kategori].push(barang);
                return acc;
            }, {});

            let html = '';

            for (const [kategori, items] of Object.entries(grouped)) {
                html += `
                <div class="mb-4">
                    <h6 class="bg-light p-2 rounded"><i class="bi bi-folder"></i> ${kategori}</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%"></th>
                                    <th style="width: 15%">Kode</th>
                                    <th style="width: 35%">Nama Barang</th>
                                    <th style="width: 12%">Tipe</th>
                                    <th style="width: 13%">Status/Stok</th>
                                    <th style="width: 15%">Jumlah</th>
                                    <th style="width: 10%">Info</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

                items.forEach(barang => {
                    const isPerUnit = barang.mode_input === 'Per Unit';
                    const isConsumable = !barang.dapat_dikembalikan;
                    const statusBadge = isPerUnit ?
                        `<span class="badge bg-success">Tersedia</span>` :
                        `<span class="badge bg-info">${barang.jumlah} ${barang.satuan}</span>`;

                    html += `
                    <tr data-barang-id="${barang.id}">
                        <td class="text-center">
                            <input type="checkbox" 
                                   class="form-check-input barang-checkbox" 
                                   data-barang='${JSON.stringify(barang)}'
                                   data-mode="${barang.mode_input}">
                        </td>
                        <td><small>${barang.kode_barang}</small></td>
                        <td>
                            ${barang.nama_barang}
                            ${isConsumable ? '<span class="badge bg-danger ms-1">🔴</span>' : ''}
                        </td>
                        <td><span class="badge bg-secondary">${barang.mode_input}</span></td>
                        <td>${statusBadge}</td>
                        <td>
                            ${isPerUnit ? `
                                <span class="text-muted">-</span>
                            ` : `
                                <input type="number" 
                                       class="form-control form-control-sm jumlah-masal" 
                                       min="1" 
                                       max="${barang.jumlah}" 
                                       value="0"
                                       data-barang='${JSON.stringify(barang)}'
                                       data-barang-id="${barang.id}"
                                       data-max="${barang.jumlah}">
                                <small class="text-muted stok-info-${barang.id}">Max: ${barang.jumlah}</small>
                                <small class="text-danger error-msg-${barang.id}" style="display: none;"></small>
                            `}
                        </td>
                        <td>
                            ${isConsumable ? '<small class="text-danger">Tidak kembali</small>' : ''}
                        </td>
                    </tr>
                `;
                });

                html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
            }

            if (barangs.some(b => !b.dapat_dikembalikan)) {
                html = `
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle"></i>
                    <strong>Informasi:</strong>
                    Barang bertanda 🔴 adalah barang habis pakai dan tidak perlu dikembalikan.
                </div>
            ` + html;
            }

            containerList.innerHTML = html;

            // Setup event listeners for checkboxes and inputs
            setupBarangInteractions();
        }

        // Setup interactions for barang selection
        function setupBarangInteractions() {
            // Handle checkbox changes for Masal items
            document.querySelectorAll('.barang-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const barangData = JSON.parse(this.dataset.barang);

                    if (barangData.mode_input === 'Masal') {
                        const jumlahInput = document.querySelector(`.jumlah-masal[data-barang-id="${barangData.id}"]`);

                        if (this.checked) {
                            // Auto set to 1 when checked
                            if (jumlahInput) {
                                jumlahInput.value = 1;
                                validateJumlah(jumlahInput);
                            }
                        } else {
                            // Reset to 0 when unchecked
                            if (jumlahInput) {
                                jumlahInput.value = 0;
                                clearError(barangData.id);
                            }
                        }
                    }
                });
            });

            // Handle jumlah input changes for Masal items with validation
            document.querySelectorAll('.jumlah-masal').forEach(input => {
                input.addEventListener('input', function() {
                    const barangId = parseInt(this.dataset.barangId);
                    const checkbox = document.querySelector(`.barang-checkbox[data-barang*='"id":${barangId}']`);
                    const value = parseInt(this.value) || 0;

                    // Auto-check if value > 0, uncheck if 0
                    if (checkbox) {
                        checkbox.checked = value > 0;
                    }

                    // Validate jumlah
                    validateJumlah(this);
                });

                // Prevent non-numeric input
                input.addEventListener('keypress', function(e) {
                    if (e.key < '0' || e.key > '9') {
                        e.preventDefault();
                    }
                });
            });
        }

        // Validate jumlah input
        function validateJumlah(input) {
            const barangId = parseInt(input.dataset.barangId);
            const max = parseInt(input.dataset.max);
            const value = parseInt(input.value) || 0;
            const errorMsg = document.querySelector(`.error-msg-${barangId}`);
            const stokInfo = document.querySelector(`.stok-info-${barangId}`);

            if (value > max) {
                // Show error
                input.classList.add('is-invalid');
                if (errorMsg) {
                    errorMsg.textContent = `⚠️ Melebihi stok! Maksimal: ${max}`;
                    errorMsg.style.display = 'block';
                }
                if (stokInfo) {
                    stokInfo.style.display = 'none';
                }

                // Auto correct to max
                setTimeout(() => {
                    input.value = max;
                    clearError(barangId);
                }, 1500);
            } else if (value < 0) {
                input.value = 0;
                clearError(barangId);
            } else {
                clearError(barangId);
            }
        }

        // Clear error message
        function clearError(barangId) {
            const input = document.querySelector(`.jumlah-masal[data-barang-id="${barangId}"]`);
            const errorMsg = document.querySelector(`.error-msg-${barangId}`);
            const stokInfo = document.querySelector(`.stok-info-${barangId}`);

            if (input) {
                input.classList.remove('is-invalid');
            }
            if (errorMsg) {
                errorMsg.style.display = 'none';
            }
            if (stokInfo) {
                stokInfo.style.display = 'block';
            }
        }

        // Search functionality
        searchInput.addEventListener('input', function(e) {
            const keyword = e.target.value.toLowerCase();
            const filtered = availableBarang.filter(barang =>
                barang.nama_barang.toLowerCase().includes(keyword) ||
                barang.kode_barang.toLowerCase().includes(keyword)
            );
            renderBarangList(filtered);
        });

        // Tambahkan barang button
        document.getElementById('btnTambahkanBarang').addEventListener('click', function() {
            selectedBarang = [];

            // Collect Per Unit items (checked)
            document.querySelectorAll('.barang-checkbox:checked').forEach(checkbox => {
                const barang = JSON.parse(checkbox.dataset.barang);

                if (barang.mode_input === 'Per Unit') {
                    selectedBarang.push({
                        ...barang,
                        jumlah_pinjam: 1
                    });
                }
            });

            // Collect Masal items with quantity > 0 and validate
            let hasError = false;
            document.querySelectorAll('.jumlah-masal').forEach(input => {
                const jumlah = parseInt(input.value) || 0;
                const max = parseInt(input.dataset.max);

                if (jumlah > max) {
                    hasError = true;
                    alert(`Jumlah barang melebihi stok tersedia! Maksimal: ${max}`);
                    input.focus();
                    return;
                }

                if (jumlah > 0) {
                    const barang = JSON.parse(input.dataset.barang);
                    selectedBarang.push({
                        ...barang,
                        jumlah_pinjam: jumlah
                    });
                }
            });

            if (hasError) {
                return;
            }

            if (selectedBarang.length === 0) {
                alert('Pilih minimal 1 barang!');
                return;
            }

            renderSelectedBarang();
            modal.hide();
        });

        // Render selected barang
        function renderSelectedBarang() {
            if (selectedBarang.length === 0) {
                containerSelected.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Belum ada barang dipilih. Klik tombol "Pilih Barang" untuk menambahkan.
                </div>
            `;
                btnSubmit.disabled = true;
                return;
            }

            let html = `
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 15%">Kode Barang</th>
                            <th style="width: 35%">Nama Barang</th>
                            <th style="width: 12%">Tipe</th>
                            <th style="width: 15%">Jumlah</th>
                            <th style="width: 10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

            selectedBarang.forEach((barang, index) => {
                html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><small>${barang.kode_barang}</small></td>
                    <td>
                        ${barang.nama_barang}
                        ${!barang.dapat_dikembalikan ? '<span class="badge bg-danger ms-1">Consumable</span>' : ''}
                        <input type="hidden" name="barang_ids[]" value="${barang.id}">
                        <input type="hidden" name="jumlah_pinjam[]" value="${barang.jumlah_pinjam}">
                    </td>
                    <td><span class="badge bg-secondary">${barang.mode_input}</span></td>
                    <td>${barang.jumlah_pinjam} ${barang.satuan}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeBarang(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            });

            html += `
                    </tbody>
                </table>
            </div>
        `;

            containerSelected.innerHTML = html;
            btnSubmit.disabled = false;
        }

        // Remove barang function (global scope)
        window.removeBarang = function(index) {
            selectedBarang.splice(index, 1);
            renderSelectedBarang();
        };

        // Trigger load when lokasi changes
        lokasiSelect?.addEventListener('change', function() {
            selectedBarang = [];
            renderSelectedBarang();
        });
    });
</script>