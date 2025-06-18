<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rata-Rata Pesanan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 30px;
        }

        h1 {
            text-align: center;
            font-size: 22px;
            color: teal;
            margin-bottom: 10px;
        }

        p.sub {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #e0f7f7;
        }

        .highlight {
            background-color: #f1fdfc;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <h1>Laporan Pesanan Rata-Rata</h1>
    <p class="sub">Menampilkan jumlah rata-rata uang yang dibelanjakan oleh setiap customer</p>

    <table>
        <thead>
            <tr>
                <th>ID Customer</th>
                <th>Jumlah Transaksi</th>
                <th>Total Belanja</th>
                <th>Rata-Rata Belanja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rataRataPerCustomer as $c)
                <tr>
                    <td>{{ $c['customer_id'] }}</td>
                    <td>{{ $c['jumlah_transaksi'] }}</td>
                    <td>Rp {{ number_format($c['total_belanja'], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($c['rata_rata_belanja'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>📊 Rata-Rata Global Semua Customer:</h3>
    <p class="highlight">Rp {{ number_format($rataRataGlobal, 0, ',', '.') }}</p>

    <div class="footer">Laporan dibuat pada {{ now()->format('d F Y H:i') }}</div>
</body>
</html>
