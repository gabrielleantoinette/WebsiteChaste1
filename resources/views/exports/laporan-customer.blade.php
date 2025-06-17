@php
    $rangeEnd = \Carbon\Carbon::now();
    $rangeStart = $rangeEnd->copy()->subDays(30);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Customer</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 30px;
            color: #333;
        }

        h1 {
            text-align: center;
            font-size: 22px;
            color: teal;
            margin-bottom: 5px;
        }

        .sub-title {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #e0f7f7;
        }

        .section {
            margin-bottom: 25px;
        }

        .section h3 {
            margin-bottom: 5px;
            color: #555;
        }

        .highlight {
            background-color: #f7fdfc;
            font-weight: bold;
        }

        .text-muted {
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <h1>Laporan Customer</h1>
    <div class="sub-title">Periode: {{ $rangeStart->format('d F Y') }} – {{ $rangeEnd->format('d F Y') }}</div>

    <div class="section">
        <h3>📊 Statistik Umum</h3>
        <table>
            <tr>
                <th>Total Pelanggan</th>
                <td class="highlight">{{ $totalCustomers }}</td>
            </tr>
            <tr>
                <th>Total Pelanggan Baru (30 hari)</th>
                <td>
                    {{ $newCustomerCount }}
                    <div class="text-muted">Pelanggan yang bergabung antara {{ $rangeStart->format('d M Y') }} dan {{ $rangeEnd->format('d M Y') }}</div>
                </td>
            </tr>
            <tr>
                <th>Persentase Pelanggan Baru</th>
                <td>{{ $newCustomerPercentage }}%</td>
            </tr>
            <tr>
                <th>Pelanggan Repeat Order</th>
                <td>{{ $repeatOrderCount }} pelanggan yang pernah bertransaksi lebih dari 1 kali</td>
            </tr>
            <tr>
                <th>Pelanggan 1 Kali Beli</th>
                <td>{{ $oneTimeCustomerCount }} pelanggan hanya 1 transaksi</td>
            </tr>
            <tr>
                <th>Rata-rata Umur Pelanggan</th>
                <td>{{ $avgAge ? $avgAge . ' tahun' : '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>📍 Distribusi Pelanggan Berdasarkan Kota</h3>
        <table>
            <thead>
                <tr><th>Kota</th><th>Jumlah Pelanggan</th></tr>
            </thead>
            <tbody>
                @forelse($locationStats as $city => $count)
                    <tr>
                        <td>{{ $city ?: '-' }}</td>
                        <td>{{ $count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">Tidak ada data kota.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="text-muted" style="margin-top: 50px;">Laporan dibuat pada {{ now()->format('d F Y H:i') }}</div>
</body>
</html>
