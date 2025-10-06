@csrf
<div x-data="{ modeInput: '{{ old('mode_input', $barang->mode_input ?? 'Masal') }}' }">
    <div class="row mb-3">
        <div class="col-md-6">
            @php
            $modeOptions = [['mode' => 'Masal'], ['mode' => 'Per Unit']];
            @endphp
            
            <label class="form-label">Mode Input</label>
            <select 
                name="mode_input" 
                class="form-select @error('mode_input') is-invalid @enderror"
                x-model="modeInput"
                @if(isset($update)) disabled @endif
            >
                <option value="">Pilih Mode Input</option>
                @foreach($modeOptions as $option)
                    <option value="{{ $option['mode'] }}" 
                        {{ old('mode_input', $barang->mode_input ?? '') == $option['mode'] ? 'selected' : '' }}>
                        {{ $option['mode'] }}
                    </option>
                @endforeach
            </select>
            @error('mode_input')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if(isset($update))
                <input type="hidden" name="mode_input" value="{{ $barang->mode_input }}">
            @endif
        </div>

        <div class="col-md-6">
            <x-form-input label="Kode Barang" name="kode_barang" :value="$barang->kode_barang" />
            <small class="text-muted" x-show="modeInput === 'Per Unit'">
                Contoh: PJTR01 (akan generate PJTR01, PJTR02, PJTR03, dst sesuai jumlah)
            </small>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <x-form-input label="Nama Barang" name="nama_barang" :value="$barang->nama_barang" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <x-form-select label="Kategori" name="kategori_id" :value="$barang->kategori_id"
                :option-data="$kategori" option-label="nama_kategori" option-value="id" />
        </div>

        <div class="col-md-6">
            <x-form-select label="Lokasi" name="lokasi_id" :value="$barang->lokasi_id"
                :option-data="$lokasi" option-label="nama_lokasi" option-value="id" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <x-form-input label="Jumlah" name="jumlah" :value="$barang->jumlah" type="number" />
            <small class="text-muted" x-show="modeInput === 'Per Unit'">
                Jumlah unit yang akan dibuat (setiap unit akan memiliki jumlah = 1)
            </small>
            <small class="text-muted" x-show="modeInput === 'Masal'">
                Total jumlah barang
            </small>
        </div>

        <div class="col-md-6">
            <x-form-input label="Satuan" name="satuan" :value="$barang->satuan" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            @php
            $kondisi = [['kondisi' => 'Baik'], ['kondisi' => 'Rusak Ringan'],
            ['kondisi' => 'Rusak Berat']];
            @endphp

            <x-form-select label="Kondisi" name="kondisi" :value="$barang->kondisi" :option-data="$kondisi"
                option-label="kondisi" option-value="kondisi" />
        </div>

        <div class="col-md-6">
            @php
            $statusOptions = [
                ['status' => 'Tersedia'],
                ['status' => 'Dipinjam'],
                ['status' => 'Rusak'],
                ['status' => 'Hilang'],
                ['status' => 'Tidak Dapat Dipinjam'],
                ['status' => 'Diperbaiki'],
                ['status' => 'Perawatan']
            ];
            @endphp

            <x-form-select label="Status" name="status" :value="$barang->status ?? 'Tersedia'" :option-data="$statusOptions"
                option-label="status" option-value="status" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            @php
            $tanggal = $barang->tanggal_pengadaan ?
            date('Y-m-d', strtotime($barang->tanggal_pengadaan)) : null;
            @endphp
            <x-form-input label="Tanggal Pengadaan" name="tanggal_pengadaan" type="date" :value="$tanggal" />
        </div>

        <div class="col-md-6">
            <x-form-input label="Gambar Barang" name="gambar" type="file" />
        </div>
    </div>

    <div class="mt-4">
        <x-primary-button>
            {{ isset($update) ? __('Update') : __('Simpan') }}
        </x-primary-button>

        <x-tombol-kembali :href="route('barang.index')" />
    </div>
</div>