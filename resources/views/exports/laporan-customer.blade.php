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
    @include('exports.partials.header')

    <h1>Laporan Customer</h1>
    <div class="sub-title">
        Periode: {{ $periodeLabel ?? '-' }}<br>
        Rentang tanggal: {{ isset($periodeStart) ? \Carbon\Carbon::parse($periodeStart)->translatedFormat('d F Y') : '-' }}
        &mdash;
        {{ isset($periodeEnd) ? \Carbon\Carbon::parse($periodeEnd)->translatedFormat('d F Y') : '-' }}
    </div>

    <div class="section">
        <h3>📊 Statistik Umum</h3>
        <table>
            <tr>
                <th>Total Pelanggan</th>
                <td class="highlight">{{ $totalCustomers }}</td>
            </tr>
            <tr>
                <th>Total Pelanggan Baru</th>
                <td>
                    {{ $newCustomerCount }}
                    <div class="text-muted">Pelanggan yang bergabung antara {{ isset($periodeStart) ? \Carbon\Carbon::parse($periodeStart)->format('d M Y') : '-' }} dan {{ isset($periodeEnd) ? \Carbon\Carbon::parse($periodeEnd)->format('d M Y') : '-' }}</div>
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

    @if(($customers ?? collect())->count() > 0)
    <div class="section">
        <h3>🆕 Daftar Customer Baru</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Customer</th>
                    <th>Tanggal Bergabung</th>
                    <th>Kota</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->created_at ? \Carbon\Carbon::parse($customer->created_at)->translatedFormat('d F Y') : '-' }}</td>
                        <td>{{ $customer->city ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(($repeatCustomers ?? collect())->count() > 0)
    <div class="section">
        <h3>🔁 Pelanggan Repeat Order</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Customer</th>
                    <th>Jumlah Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($repeatCustomers as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['order_count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(($singlePurchaseCustomers ?? collect())->count() > 0)
    <div class="section">
        <h3>🛒 Pelanggan Satu Kali Transaksi</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Customer</th>
                </tr>
            </thead>
            <tbody>
                @foreach($singlePurchaseCustomers as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

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

    <div class="text-muted" style="margin-top: 50px;">Laporan dibuat pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</div>
</body>
</html>
