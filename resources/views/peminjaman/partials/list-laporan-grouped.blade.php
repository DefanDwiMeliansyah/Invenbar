@php
    $globalIndex = 1;
@endphp

@foreach($groupedData as $groupName => $peminjamans)
    <div style="margin-bottom: 25px;">
        <h3 style="background-color: #007bff; color: white; padding: 8px; margin: 0 0 10px 0; font-size: 12px;">
            {{ $groupBy === 'status' ? 'Status: ' : ($groupBy === 'lokasi' ? 'Lokasi: ' : 'Periode: ') }}
            {{ $groupName }}
            ({{ $peminjamans->count() }} peminjaman)
        </h3>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Peminjam</th>
                    <th>Kontak</th>
                    <th>Tgl Pinjam</th>
                    <th>Batas Kembali</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Detail Barang</th>
                </tr>
            </thead>
            <tbody>
                @foreach($peminjamans as $peminjaman)
                    <tr>
                        <td>{{ $globalIndex++ }}</td>
                        <td><strong>{{ $peminjaman->kode_peminjaman }}</strong></td>
                        <td>
                            {{ $peminjaman->nama_peminjam }}<br>
                            <small>{{ $peminjaman->email }}</small>
                        </td>
                        <td>{{ $peminjaman->nomor_telepon }}</td>
                        <td>{{ $peminjaman->tanggal_pinjam->format('d-m-Y') }}</td>
                        <td>
                            {{ $peminjaman->tanggal_batas_pengembalian->format('d-m-Y') }}
                            @if($peminjaman->isLate())
                                <br><span class="text-late">Terlambat {{ $peminjaman->getDaysLate() }} hari</span>
                            @endif
                        </td>
                        <td>{{ $peminjaman->lokasi->nama_lokasi }}</td>
                        <td>
                            <span class="badge {{ $peminjaman->status === 'Dipinjam' ? 'badge-warning' : 'badge-success' }}">
                                {{ $peminjaman->status }}
                            </span>
                        </td>
                        <td>
                            @if($peminjaman->details->count() > 0)
                                <table style="width: 100%; border: none; margin: 0;">
                                    @foreach($peminjaman->details as $detail)
                                        <tr style="border: none;">
                                            <td style="border: none; padding: 2px 0; width: 15%;">
                                                <small>{{ $detail->barang->kode_barang }}</small>
                                            </td>
                                            <td style="border: none; padding: 2px 0; width: 45%;">
                                                <small>
                                                    {{ $detail->barang->nama_barang }}
                                                    @if(!$detail->dapat_dikembalikan)
                                                        <span class="consumable-mark">(Consumable)</span>
                                                    @endif
                                                </small>
                                            </td>
                                            <td style="border: none; padding: 2px 0; width: 20%; text-align: center;">
                                                <small>
                                                    @if($detail->barang->mode_input === 'Per Unit')
                                                        1 {{ $detail->barang->satuan }}
                                                    @else
                                                        {{ $detail->jumlah }} {{ $detail->barang->satuan }}
                                                    @endif
                                                </small>
                                            </td>
                                            <td style="border: none; padding: 2px 0; width: 20%; text-align: center;">
                                                <small>
                                                    <span class="badge {{ $detail->status_detail === 'Dikembalikan' ? 'badge-success' : 'badge-warning' }}">
                                                        {{ $detail->status_detail }}
                                                    </span>
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            @else
                                <small>-</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
                
                <!-- Summary row for each group -->
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="9" style="text-align: right; padding: 6px;">
                        <strong>Subtotal Grup:</strong> 
                        {{ $peminjamans->count() }} peminjaman | 
                        {{ $peminjamans->sum(fn($p) => $p->details->count()) }} barang |
                        Terlambat: <span class="text-late">{{ $peminjamans->filter(fn($p) => $p->isLate())->count() }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endforeach

<div style="margin-top: 20px; font-size: 10px;">
    <strong>Keterangan:</strong>
    <ul style="margin: 5px 0; padding-left: 20px;">
        <li><span class="consumable-mark">(Consumable)</span> = Barang habis pakai yang tidak perlu dikembalikan</li>
        <li><span class="text-late">Terlambat</span> = Peminjaman yang melewati batas waktu pengembalian</li>
    </ul>
</div>