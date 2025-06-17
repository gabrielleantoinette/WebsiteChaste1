<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Terpal</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 30px;
        }

        h1 {
            text-align: center;
            color: teal;
            font-size: 20px;
            margin-bottom: 30px;
        }

        h3 {
            color: #555;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #e0f7f7;
            color: #333;
            padding: 8px;
            text-align: left;
        }

        td {
            padding: 8px;
            border: 1px solid #ccc;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Laporan Penjualan Terpal</h1>

    <h3>Terpal Paling Laku</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th class="text-right">Jumlah Terjual</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $p)
                <tr>
                    <td>{{ $p['product'] }}</td>
                    <td class="text-right">{{ $p['jumlah_terjual'] }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Tidak ada data penjualan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>Warna Terfavorit</h3>
    <table>
        <thead>
            <tr>
                <th>Warna</th>
                <th class="text-right">Jumlah Terjual</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topColors as $c)
                <tr>
                    <td>{{ ucfirst($c['warna']) }}</td>
                    <td class="text-right">{{ $c['jumlah_terjual'] }}</td>
                </tr>
            @empty
                <tr><td colspan="2">Tidak ada data warna terjual.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p style="margin-top: 50px; font-size: 10px; color: #888;">Laporan dibuat pada {{ now()->format('d F Y') }}</p>
</body>
</html>
