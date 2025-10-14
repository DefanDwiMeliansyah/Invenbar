<table>
    <thead>
        <th>No</th>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th>Kategori</th>
        <th>Sumber</th>
        <th>Lokasi</th>
        <th>Jumlah</th>
        <th>Kondisi</th>
        <th>Status</th>
        <th>Tgl. Pengadaan</th>
    </thead>
    <tbody>
        @php
            $displayedPrefixes = [];
            $no = 1;
        @endphp

        @forelse ($barangs as $index => $barang)
            @php
                $prefix = $barang->getKodePrefix();
                $isPerUnit = $barang->mode_input === 'Per Unit';
                $isFirstOfGroup = $isPerUnit && !in_array($prefix, $displayedPrefixes);
                $shouldDisplay = !$isPerUnit || $isFirstOfGroup;
                
                if ($isFirstOfGroup) {
                    $displayedPrefixes[] = $prefix;
                }

                // Hitung total unit untuk grup
                $relatedUnits = [];
                if ($isPerUnit && isset($groupedBarangs[$prefix])) {
                    $relatedUnits = collect($groupedBarangs[$prefix])->sortBy('kode_barang')->values();
                }
            @endphp

            @if($shouldDisplay)
                @if($isPerUnit && count($relatedUnits) > 1)
                    {{-- Baris Header Grup Per Unit --}}
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td rowspan="{{ count($relatedUnits) + 1 }}" style="vertical-align: top;">{{ $no++ }}</td>
                        <td colspan="9">
                            <strong>{{ $barang->nama_barang }}</strong> 
                            ({{ $barang->kategori->nama_kategori }} - {{ $barang->lokasi->nama_lokasi }})
                            - Total: {{ count($relatedUnits) }} {{ $barang->satuan }}
                        </td>
                    </tr>
                    
                    {{-- Detail Setiap Unit --}}
                    @foreach($relatedUnits as $unit)
                    <tr>
                        <td style="padding-left: 15px;">{{ $unit->kode_barang }}</td>
                        <td>{{ $unit->nama_barang }}</td>
                        <td>{{ $unit->kategori->nama_kategori }}</td>
                        <td>{{ $barang->sumber }}</td>
                        <td>{{ $unit->lokasi->nama_lokasi }}</td>
                        <td>{{ $unit->jumlah }} {{ $unit->satuan }}</td>
                        <td>{{ $unit->kondisi }}</td>
                        <td>{{ $unit->status ?? 'Tersedia' }}</td>
                        <td>{{ date('d-m-Y', strtotime($unit->tanggal_pengadaan)) }}</td>
                    </tr>
                    @endforeach
                @else
                    {{-- Barang Masal atau Per Unit Tunggal --}}
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $barang->kode_barang }}</td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>{{ $barang->kategori->nama_kategori }}</td>
                        <td>{{ $barang->sumber }}</td>
                        <td>{{ $barang->lokasi->nama_lokasi }}</td>
                        <td>{{ $barang->jumlah }} {{ $barang->satuan }}</td>
                        <td>{{ $barang->kondisi }}</td>
                        <td>{{ $barang->status ?? 'Tersedia' }}</td>
                        <td>{{ date('d-m-Y', strtotime($barang->tanggal_pengadaan)) }}</td>
                    </tr>
                @endif
            @endif
        @empty
        <tr>
            <td colspan="9" style="text-align: center;">Tidak ada data.</td>
        </tr>
        @endforelse
    </tbody>
</table>