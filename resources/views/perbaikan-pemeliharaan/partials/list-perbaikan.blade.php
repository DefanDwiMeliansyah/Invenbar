<x-table-list>
    <x-slot name="header">
        <tr>
            <th style="width: 5%">No</th>
            <th style="width: 12%">Kode</th>
            <th style="width: 15%">Barang</th>
            <th style="width: 10%">Lokasi</th>
            <th style="width: 10%">Jenis</th>
            <th style="width: 8%">Prioritas</th>
            <th style="width: 10%">Tgl Pengajuan</th>
            <th style="width: 10%">Status</th>
            <th style="width: 10%">Teknisi</th>
            <th style="width: 10%">Aksi</th>
        </tr>
    </x-slot>

    @forelse ($perbaikans as $perbaikan)
        <tr>
            <td>{{ $perbaikans->firstItem() + $loop->index }}</td>
            <td>
                <strong>{{ $perbaikan->kode_perbaikan }}</strong>
            </td>
            <td>
                {{ $perbaikan->barang->nama_barang }}<br>
                <small class="text-muted">{{ $perbaikan->barang->kode_barang }}</small>
            </td>
            <td>{{ $perbaikan->barang->lokasi->nama_lokasi }}</td>
            <td>
                <span class="badge {{ $perbaikan->getJenisBadgeClass() }}">
                    {{ $perbaikan->jenis }}
                </span>
            </td>
            <td>
                <span class="badge {{ $perbaikan->getPrioritasBadgeClass() }}">
                    {{ $perbaikan->prioritas }}
                </span>
            </td>
            <td>{{ $perbaikan->tanggal_pengajuan->format('d/m/Y') }}</td>
            <td>
                <span class="badge {{ $perbaikan->getStatusBadgeClass() }}">
                    {{ $perbaikan->status }}
                </span>
            </td>
            <td>
                @if($perbaikan->teknisi)
                    <small>{{ $perbaikan->teknisi }}</small>
                @else
                    <small class="text-muted">-</small>
                @endif
            </td>
            <td>
                <div role="group">
                    <x-tombol-aksi type="show" href="{{ route('perbaikan-pemeliharaan.show', $perbaikan) }}" />
                    
                    @can('delete perbaikan-pemeliharaan')
                        @if(!in_array($perbaikan->status, ['Selesai', 'Dalam Perbaikan']))
                            <x-tombol-aksi type="delete" href="{{ route('perbaikan-pemeliharaan.destroy', $perbaikan) }}" />
                        @endif
                    @endcan
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="10" class="text-center py-4 text-muted">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-2">Tidak ada data perbaikan</p>
            </td>
        </tr>
    @endforelse
</x-table-list>