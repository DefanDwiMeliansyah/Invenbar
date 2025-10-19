<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    @include('peminjaman.partials.style-laporan')
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Tanggal Cetak: {{ $date }}</p>
        
        @if($filters['tanggal_dari'] || $filters['tanggal_sampai'] || $filters['status'] || $filters['lokasi'] || $filters['nama_peminjam'])
            <div class="filter-info">
                <strong>Filter:</strong>
                @if($filters['tanggal_dari'])
                    Dari: {{ date('d-m-Y', strtotime($filters['tanggal_dari'])) }}
                @endif
                @if($filters['tanggal_sampai'])
                    s/d {{ date('d-m-Y', strtotime($filters['tanggal_sampai'])) }}
                @endif
                @if($filters['status'])
                    | Status: {{ $filters['status'] }}
                @endif
                @if($filters['lokasi'])
                    | Lokasi: {{ $filters['lokasi'] }}
                @endif
                @if($filters['nama_peminjam'])
                    | Peminjam: {{ $filters['nama_peminjam'] }}
                @endif
            </div>
        @endif
    </div>

    <!-- Statistik Ringkasan -->
    <div class="statistics">
        <table class="stats-table">
            <tr>
                <td>
                    <strong>Total Peminjaman:</strong> {{ $statistics['total_peminjaman'] }}
                </td>
                <td>
                    <strong>Dipinjam:</strong> {{ $statistics['total_dipinjam'] }}
                </td>
                <td>
                    <strong>Dikembalikan:</strong> {{ $statistics['total_dikembalikan'] }}
                </td>
                <td>
                    <strong>Terlambat:</strong> <span class="text-danger">{{ $statistics['total_terlambat'] }}</span>
                </td>
                <td>
                    <strong>Total Barang:</strong> {{ $statistics['total_barang'] }}
                </td>
            </tr>
        </table>
    </div>

    @if($groupBy && $groupBy !== 'none' && $groupedData)
        @include('peminjaman.partials.list-laporan-grouped')
    @else
        @include('peminjaman.partials.list-laporan')
    @endif
</body>
</html>