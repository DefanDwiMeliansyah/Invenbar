<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Jumlah</th>
            <th>Kondisi</th>
            <th>Status</th>
            <th>&nbsp;</th>
        </tr>
    </x-slot>

    @php
    $displayedPrefixes = [];
    $currentNumber = 0;
    @endphp

    @forelse ($barangs as $index => $barang)
    @php
    $prefix = $barang->getKodePrefix();
    $isPerUnit = $barang->mode_input === 'Per Unit';
    $isFirstOfGroup = $isPerUnit && !in_array($prefix, $displayedPrefixes);

    $shouldDisplay = !$isPerUnit || $isFirstOfGroup;

    if ($shouldDisplay) {
    $currentNumber++;
    }

    if ($isFirstOfGroup) {
    $displayedPrefixes[] = $prefix;
    }

    // Ambil semua unit yang related
    $relatedUnits = [];
    if ($isPerUnit && isset($groupedBarangs[$prefix])) {
    $relatedUnits = collect($groupedBarangs[$prefix])->sortBy('kode_barang')->values()->all();
    // Pastikan $barang adalah yang pertama dalam grup
    if (count($relatedUnits) > 0) {
    $barang = $relatedUnits[0];
    }
    }
    @endphp

    @if($shouldDisplay)
    <tbody x-data="{ expanded: false }">
        <tr>
            <td>{{ $currentNumber }}</td>
            <td>
                @if($isPerUnit && count($relatedUnits) > 1)
                <button
                    @click="expanded = !expanded"
                    class="btn btn-sm btn-link p-0 me-2 text-decoration-none"
                    type="button"
                    :aria-expanded="expanded">
                    <span x-show="!expanded"><i class="bi bi-chevron-right"></i></span>
                    <span x-show="expanded" x-cloak><i class="bi bi-chevron-down"></i></span>
                </button>
                @endif
                {{ $barang->kode_barang }}
                @if($isPerUnit && count($relatedUnits) > 1)
                <small class="text-muted">({{ count($relatedUnits) }} unit)</small>
                @endif
            </td>
            <td>{{ $barang->nama_barang }}</td>
            <td>{{ $barang->kategori->nama_kategori }}</td>
            <td>{{ $barang->lokasi->nama_lokasi }}</td>
            <td>
                @if($isPerUnit && count($relatedUnits) > 1)
                {{ count($relatedUnits) }} {{ $barang->satuan }}
                @else
                {{ $barang->jumlah }} {{ $barang->satuan }}
                @endif
            </td>
            <td>
                @php
                $badgeClass = 'bg-success';
                if ($barang->kondisi == 'Rusak Ringan') {
                $badgeClass = 'bg-warning text-dark';
                }
                if ($barang->kondisi == 'Rusak Berat') {
                $badgeClass = 'bg-danger';
                }
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $barang->kondisi }}</span>
            </td>
            <td>
                <span class="badge {{ $barang->getStatusBadgeClass() }}">
                    {{ $barang->status ?? 'Tersedia' }}
                </span>
            </td>
            <td class="text-center">
                @if($isPerUnit && count($relatedUnits) > 1)
                <div class="d-flex justify-content-evenly">
                {{-- Tombol untuk Item Induk Per Unit --}}
                @can('manage barang')
                <x-tombol-aksi href="{{ route('barang.show', $barang->id) }}" type="show" />
                <x-tombol-aksi href="{{ route('barang.edit', $barang->id) }}" type="edit" />
                @endcan

                @can('delete barang')
                <x-tombol-aksi href="{{ route('barang.destroy', $barang->id) }}" type="delete" />
                @endcan
                </div>
                @can('delete barang')
                <form action="{{ route('barang.destroy-group', $prefix) }}"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua {{ count($relatedUnits) }} unit barang ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger p-1 mt-1" title="Hapus Semua Unit">
                        <i class="bi bi-trash"></i> Hapus Semua
                    </button>
                </form>
                @endcan
                @else
                @can('manage barang')
                <x-tombol-aksi href="{{ route('barang.show', $barang->id) }}" type="show" />
                <x-tombol-aksi href="{{ route('barang.edit', $barang->id) }}" type="edit" />
                @endcan

                @can('delete barang')
                <x-tombol-aksi href="{{ route('barang.destroy', $barang->id) }}" type="delete" />
                @endcan
                @endif
            </td>
        </tr>

        @if($isPerUnit && count($relatedUnits) > 1)
        @foreach($relatedUnits as $unitIndex => $unit)
        @if($unitIndex > 0)
        <tr x-show="expanded"
            x-transition.duration.200ms
            class="bg-light"
            style="display: none;"
            x-cloak>
            <td></td>
            <td class="ps-5">
                <span class="text-muted">└─</span> {{ $unit->kode_barang }}
            </td>
            <td>{{ $unit->nama_barang }}</td>
            <td>{{ $unit->kategori->nama_kategori }}</td>
            <td>{{ $unit->lokasi->nama_lokasi }}</td>
            <td>{{ $unit->jumlah }} {{ $unit->satuan }}</td>
            <td>
                @php
                $badgeClass = 'bg-success';
                if ($unit->kondisi == 'Rusak Ringan') {
                $badgeClass = 'bg-warning text-dark';
                }
                if ($unit->kondisi == 'Rusak Berat') {
                $badgeClass = 'bg-danger';
                }
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $unit->kondisi }}</span>
            </td>
            <td>
                <span class="badge {{ $unit->getStatusBadgeClass() }}">{{ $unit->status ?? 'Tersedia' }}</span>
            </td>
            <td class="text-end">
                @can('manage barang')
                <x-tombol-aksi href="{{ route('barang.show', $unit->id) }}" type="show" />
                <x-tombol-aksi href="{{ route('barang.edit', $unit->id) }}" type="edit" />
                @endcan

                @can('delete barang')
                <x-tombol-aksi href="{{ route('barang.destroy', $unit->id) }}" type="delete" />
                @endcan
            </td>
        </tr>
        @endif
        @endforeach
        @endif
    </tbody>
    @endif
    @empty
    <tr>
        <td colspan="9" class="text-center">
            <div class="alert alert-danger">
                Data barang belum tersedia.
            </div>
        </td>
    </tr>
    @endforelse
</x-table-list>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>