<x-table-list>
    <x-slot name="header">
        <tr>
            <th style="width: 5%">No</th>
            <th style="width: 12%">Kode Peminjaman</th>
            <th style="width: 15%">Peminjam</th>
            <th style="width: 10%">Kontak</th>
            <th style="width: 10%">Tgl Pinjam</th>
            <th style="width: 10%">Batas Kembali</th>
            <th style="width: 12%">Lokasi</th>
            <th style="width: 10%">Status</th>
            <th style="width: 16%">Aksi</th>
        </tr>
    </x-slot>

    @forelse ($peminjamans as $peminjaman)
        <tr>
            <td>{{ $peminjamans->firstItem() + $loop->index }}</td>
            <td>
                <strong>{{ $peminjaman->kode_peminjaman }}</strong>
            </td>
            <td>
                {{ $peminjaman->nama_peminjam }}<br>
                <small class="text-muted">{{ $peminjaman->email }}</small>
            </td>
            <td>{{ $peminjaman->nomor_telepon }}</td>
            <td>{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
            <td>{{ $peminjaman->tanggal_batas_pengembalian->format('d/m/Y') }}</td>
            <td>{{ $peminjaman->lokasi->nama_lokasi }}</td>
            <td>
                <span class="badge {{ $peminjaman->getStatusBadgeClass() }}">
                    {{ $peminjaman->status }}
                </span>
                @if($peminjaman->isLate())
                    <br>
                    <span class="badge bg-danger mt-1">
                        Terlambat {{ $peminjaman->getDaysLate() }} hari
                    </span>
                @endif
            </td>
            <td>
                <div role="group">
                    <x-tombol-aksi type="show" href="{{ route('peminjaman.show', $peminjaman) }}" />
                    
                    @if($peminjaman->status === 'Dipinjam')
                        <button type="button" class="btn btn-sm btn-success" 
                                data-bs-toggle="modal" 
                                data-bs-target="#returnModal{{ $peminjaman->id }}">
                            <i class="bi bi-box-arrow-in-left"></i>
                        </button>
                    @endif

                    @can('delete peminjaman')
                        <x-tombol-aksi type="delete" href="{{ route('peminjaman.destroy', $peminjaman) }}" />
                    @endcan
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="text-center py-4 text-muted">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-2">Tidak ada data peminjaman</p>
            </td>
        </tr>
    @endforelse
</x-table-list>

@foreach($peminjamans as $peminjaman)
    @if($peminjaman->status === 'Dipinjam')
        @include('peminjaman.partials.modal-pengembalian', ['peminjaman' => $peminjaman])
    @endif
@endforeach