<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Retur</title>
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

        .text-center {
            text-align: center;
        }

        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-title {
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
        }

        .summary-item {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <h1>Laporan Barang Retur</h1>

    <div class="summary-box">
        <div class="summary-title">Ringkasan Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'N/A' }} - {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'N/A' }}</div>
        <div class="summary-item">Total Retur: <strong>{{ $totalReturns }}</strong></div>
        <div class="summary-item">Customer Terlibat: <strong>{{ $totalCustomers }}</strong></div>
        <div class="summary-item">Rata-rata retur per customer: <strong>{{ $totalCustomers > 0 ? round($totalReturns / $totalCustomers, 2) : 0 }}</strong></div>
    </div>

    <h3>Analisis Berdasarkan Status Retur</h3>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-right">Jumlah</th>
                <th class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statusStats as $status => $count)
                <tr>
                    <td>{{ ucfirst($status) }}</td>
                    <td class="text-right">{{ $count }}</td>
                    <td class="text-right">{{ $totalReturns > 0 ? round(($count / $totalReturns) * 100, 1) : 0 }}%</td>
                </tr>
            @empty
                <tr><td colspan="3">Tidak ada data retur.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($productStats->count() > 0)
    <h3>Produk Terpal Paling Sering Diretur</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Variant</th>
                <th class="text-right">Jumlah Retur</th>
                <th class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productStats as $product)
                <tr>
                    <td>{{ $product['product_name'] }}</td>
                    <td>{{ $product['variant_name'] }}</td>
                    <td class="text-right">{{ $product['return_count'] }}</td>
                    <td class="text-right">{{ $totalReturns > 0 ? round(($product['return_count'] / $totalReturns) * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($colorStats->count() > 0)
    <h3>Warna Terpal Paling Sering Diretur</h3>
    <table>
        <thead>
            <tr>
                <th>Warna</th>
                <th class="text-right">Jumlah Retur</th>
                <th class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($colorStats as $color)
                <tr>
                    <td>{{ ucfirst($color['color']) }}</td>
                    <td class="text-right">{{ $color['return_count'] }}</td>
                    <td class="text-right">{{ $totalReturns > 0 ? round(($color['return_count'] / $totalReturns) * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($customerStats->count() > 0)
    <h3>Customer dengan Retur Terbanyak</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Customer</th>
                <th class="text-right">Jumlah Retur</th>
                <th class="text-right">Persentase</th>
                <th>Retur Terakhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customerStats as $customer)
                <tr>
                    <td>{{ $customer['customer_name'] }}</td>
                    <td class="text-right">{{ $customer['return_count'] }}</td>
                    <td class="text-right">{{ $totalReturns > 0 ? round(($customer['return_count'] / $totalReturns) * 100, 1) : 0 }}%</td>
                    <td>{{ \Carbon\Carbon::parse($customer['latest_return'])->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($monthlyStats->count() > 0)
    <h3>Trend Retur Bulanan (6 Bulan Terakhir)</h3>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-right">Jumlah Retur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyStats as $month => $count)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</td>
                    <td class="text-right">{{ $count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h3>Detail Data Retur</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Customer</th>
                <th>Invoice</th>
                <th>Produk</th>
                <th>Status</th>
                <th>Deskripsi</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $index => $return)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $return->customer ? $return->customer->name : 'N/A' }}</td>
                    <td>{{ $return->invoice ? $return->invoice->code : 'N/A' }}</td>
                    <td>{{ $return->product_info ?? 'N/A' }}</td>
                    <td class="text-center">{{ ucfirst($return->status) }}</td>
                    <td>{{ Str::limit($return->description, 30) }}</td>
                    <td>{{ \Carbon\Carbon::parse($return->created_at)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Tidak ada data retur.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-title">Kesimpulan dan Rekomendasi:</div>
        <div class="summary-item">• Produk terpal yang paling sering diretur: <strong>{{ $topReturnedProduct ?? 'N/A' }}</strong></div>
        <div class="summary-item">• Warna terpal yang paling sering diretur: <strong>{{ $topReturnedColor ?? 'N/A' }}</strong></div>
        <div class="summary-item">• Customer dengan retur terbanyak: <strong>{{ $topReturnedCustomer ?? 'N/A' }}</strong></div>
        <div class="summary-item">• Rekomendasi: Perlu evaluasi kualitas produk dan proses QC untuk mengurangi retur</div>
    </div>

    <p style="margin-top: 50px; font-size: 10px; color: #888;">Laporan dibuat pada {{ now()->format('d F Y H:i') }}</p>
</body>
</html>