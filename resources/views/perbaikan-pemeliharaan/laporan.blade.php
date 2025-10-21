<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        .header h2 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .filter-info {
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
        }

        .filter-info table {
            width: 100%;
        }

        .filter-info td {
            padding: 2px 5px;
            font-size: 10px;
        }

        .filter-info td:first-child {
            width: 120px;
            font-weight: bold;
        }

        .statistics {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
        }

        .stat-box .label {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-box .value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data-table thead {
            background-color: #333;
            color: white;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: left;
            font-size: 9px;
        }

        table.data-table th {
            font-weight: bold;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-danger { background-color: #dc3545; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
        .badge-primary { background-color: #007bff; color: white; }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>{{ $title }}</h2>
        <p>Dicetak pada: {{ $date }}</p>
    </div>

    <!-- Filter Information -->
    @if(array_filter($filters))
        <div class="filter-info">
            <strong>Filter yang Diterapkan:</strong>
            <table>
                @if($filters['tanggal_dari'])
                    <tr>
                        <td>Periode</td>
                        <td>: {{ \Carbon\Carbon::parse($filters['tanggal_dari'])->format('d/m/Y') }} 
                            s/d {{ \Carbon\Carbon::parse($filters['tanggal_sampai'])->format('d/m/Y') }}</td>
                    </tr>
                @endif
                @if($filters['status'])
                    <tr>
                        <td>Status</td>
                        <td>: {{ $filters['status'] }}</td>
                    </tr>
                @endif
                @if($filters['jenis'])
                    <tr>
                        <td>Jenis</td>
                        <td>: {{ $filters['jenis'] }}</td>
                    </tr>
                @endif
                @if($filters['prioritas'])
                    <tr>
                        <td>Prioritas</td>
                        <td>: {{ $filters['prioritas'] }}</td>
                    </tr>
                @endif
                @if($filters['lokasi'])
                    <tr>
                        <td>Lokasi</td>
                        <td>: {{ $filters['lokasi'] }}</td>
                    </tr>
                @endif
            </table>
        </div>
    @endif

    <!-- Statistics -->
    <div class="statistics">
        <div class="stat-box">
            <div class="label">Total Perbaikan</div>
            <div class="value">{{ $statistics['total_perbaikan'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Diajukan</div>
            <div class="value">{{ $statistics['total_diajukan'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Selesai</div>
            <div class="value">{{ $statistics['total_selesai'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Total Biaya</div>
            <div class="value">Rp {{ number_format($statistics['total_biaya'], 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Data Table -->
    @if($perbaikans->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 3%">No</th>
                    <th style="width: 10%">Kode</th>
                    <th style="width: 15%">Barang</th>
                    <th style="width: 10%">Lokasi</th>
                    <th style="width: 8%">Jenis</th>
                    <th style="width: 7%">Prioritas</th>
                    <th style="width: 8%">Tgl Pengajuan</th>
                    <th style="width: 8%">Status</th>
                    <th style="width: 12%">Teknisi</th>
                    <th style="width: 10%">Biaya</th>
                    <th style="width: 9%">Durasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perbaikans as $perbaikan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $perbaikan->kode_perbaikan }}</td>
                        <td>
                            {{ $perbaikan->barang->nama_barang }}<br>
                            <small style="color: #666;">{{ $perbaikan->barang->kode_barang }}</small>
                        </td>
                        <td>{{ $perbaikan->barang->lokasi->nama_lokasi }}</td>
                        <td>
                            @if($perbaikan->jenis === 'Perbaikan')
                                <span class="badge badge-danger">Perbaikan</span>
                            @else
                                <span class="badge badge-primary">Pemeliharaan</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = match($perbaikan->prioritas) {
                                    'Urgent' => 'badge-danger',
                                    'Tinggi' => 'badge-warning',
                                    'Sedang' => 'badge-info',
                                    'Rendah' => 'badge-secondary',
                                    default => 'badge-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $perbaikan->prioritas }}</span>
                        </td>
                        <td>{{ $perbaikan->tanggal_pengajuan->format('d/m/Y') }}</td>
                        <td>
                            @php
                                $statusBadge = match($perbaikan->status) {
                                    'Diajukan' => 'badge-warning',
                                    'Disetujui' => 'badge-info',
                                    'Dalam Perbaikan' => 'badge-primary',
                                    'Selesai' => 'badge-success',
                                    'Dibatalkan' => 'badge-danger',
                                    default => 'badge-secondary',
                                };
                            @endphp
                            <span class="badge {{ $statusBadge }}">{{ $perbaikan->status }}</span>
                        </td>
                        <td>{{ $perbaikan->teknisi ?? '-' }}</td>
                        <td style="text-align: right;">
                            @if($perbaikan->biaya_perbaikan)
                                Rp {{ number_format($perbaikan->biaya_perbaikan, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($perbaikan->getDurasiPerbaikan())
                                {{ $perbaikan->getDurasiPerbaikan() }} hari
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            Tidak ada data perbaikan yang sesuai dengan filter yang dipilih.
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>