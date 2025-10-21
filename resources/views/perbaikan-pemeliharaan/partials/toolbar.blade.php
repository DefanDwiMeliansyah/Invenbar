<form method="GET" action="{{ route('perbaikan-pemeliharaan.index') }}" id="filterForm">
    <div class="row mb-3">
        <div class="col-md-6">
            <x-tombol-tambah label="Ajukan Perbaikan" href="{{ route('perbaikan-pemeliharaan.create') }}" />
            <x-tombol-cetak label="Cetak Laporan Perbaikan & Pemeliharaan" href="{{ route('perbaikan-pemeliharaan.laporan-form') }}" />
        </div>
        
        <div class="col-md-6">
            <!-- Search Input -->
            <div class="input-group">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Cari kode/barang..." 
                       value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-3">
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Status</option>
                <option value="Diajukan" {{ request('status') === 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                <option value="Disetujui" {{ request('status') === 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="Dalam Perbaikan" {{ request('status') === 'Dalam Perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="Dibatalkan" {{ request('status') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="prioritas" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Prioritas</option>
                <option value="Urgent" {{ request('prioritas') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="Tinggi" {{ request('prioritas') === 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                <option value="Sedang" {{ request('prioritas') === 'Sedang' ? 'selected' : '' }}>Sedang</option>
                <option value="Rendah" {{ request('prioritas') === 'Rendah' ? 'selected' : '' }}>Rendah</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="jenis" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Jenis</option>
                <option value="Perbaikan" {{ request('jenis') === 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                <option value="Pemeliharaan Rutin" {{ request('jenis') === 'Pemeliharaan Rutin' ? 'selected' : '' }}>Pemeliharaan Rutin</option>
            </select>
        </div>
        <div class="col-md-3">
            @if(request()->hasAny(['status', 'prioritas', 'jenis', 'search']))
                <a href="{{ route('perbaikan-pemeliharaan.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-x-circle"></i> Reset Filter
                </a>
            @endif
        </div>
    </div>
</form>