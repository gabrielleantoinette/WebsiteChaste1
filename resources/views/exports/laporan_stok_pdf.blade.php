<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok {{ $judulPeriode }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .summary {
            margin-bottom: 20px;
        }
        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-cell {
            display: table-cell;
            width: 25%;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: top;
        }
        .summary-cell h3 {
            margin: 0 0 5px 0;
            font-size: 10px;
            color: #666;
        }
        .summary-cell .value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .status-low {
            color: #d32f2f;
            font-weight: bold;
        }
        .status-medium {
            color: #f57c00;
            font-weight: bold;
        }
        .status-good {
            color: #388e3c;
            font-weight: bold;
        }
        .variant-item {
            margin: 2px 0;
            font-size: 9px;
        }
        .page-break {
            page-break-before: always;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN STOK {{ strtoupper($judulPeriode) }}</h1>
        <p>PT. Chaste Gemilang Mandiri</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
        <p>Periode: {{ $judulPeriode }}</p>
    </div>

    {{-- Ringkasan --}}
    <div class="summary">
        <div class="section-title">RINGKASAN STOK</div>
        <div class="summary-grid">
            <div class="summary-cell">
                <h3>Total Stok Saat Ini</h3>
                <div class="value">{{ $stokSaatIni->sum('stok') }}</div>
            </div>
            <div class="summary-cell">
                <h3>Stok Masuk</h3>
                <div class="value status-good">{{ $stokMasuk->sum(function($item) { return collect($item['items'])->sum('qty'); }) }}</div>
            </div>
            <div class="summary-cell">
                <h3>Stok Keluar</h3>
                <div class="value status-low">{{ $stokKeluar->sum(function($item) { return collect($item['items'])->sum('qty'); }) }}</div>
            </div>
            <div class="summary-cell">
                <h3>Sisa Stok</h3>
                <div class="value">{{ $stokSaatIni->sum('stok') + $stokMasuk->sum(function($item) { return collect($item['items'])->sum('qty'); }) - $stokKeluar->sum(function($item) { return collect($item['items'])->sum('qty'); }) }}</div>
            </div>
        </div>
    </div>

    {{-- Stok Saat Ini --}}
    <div class="page-break"></div>
    <div class="section-title">STOK SAAT INI</div>
    <table>
        <thead>
            <tr>
                <th>Nama Produk/Material</th>
                <th>Kategori</th>
                <th>Tipe</th>
                <th>Total Stok</th>
                <th>Detail Variant</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stokSaatIni as $item)
                <tr>
                    <td><strong>{{ $item['nama'] }}</strong></td>
                    <td>{{ $item['kategori'] }}</td>
                    <td>{{ $item['tipe'] }}</td>
                    <td class="{{ $item['stok'] <= 10 ? 'status-low' : ($item['stok'] <= 50 ? 'status-medium' : 'status-good') }}">
                        {{ $item['stok'] }}
                    </td>
                    <td>
                        @forelse ($item['variants'] as $variant)
                            <div class="variant-item">
                                {{ $variant['warna'] }}: {{ $variant['stok'] }}
                            </div>
                        @empty
                            <span style="color: #999;">Tidak ada variant</span>
                        @endforelse
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #999;">Tidak ada data stok.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Stok Masuk --}}
    @if($stokMasuk->count() > 0)
        <div class="page-break"></div>
        <div class="section-title">STOK MASUK</div>
        <table>
            <thead>
                <tr>
                    <th>Sumber</th>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Items</th>
                    <th>Total Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stokMasuk as $masuk)
                    <tr>
                        <td><strong>{{ $masuk['sumber'] }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($masuk['tanggal'])->format('d/m/Y H:i') }}</td>
                        <td>{{ $masuk['tipe'] }}</td>
                        <td>
                            @foreach ($masuk['items'] as $item)
                                <div class="variant-item">
                                    <strong>{{ $item['nama'] }}</strong><br>
                                    <span style="color: #666;">{{ $item['keterangan'] }}</span>
                                </div>
                            @endforeach
                        </td>
                        <td class="status-good">{{ collect($masuk['items'])->sum('qty') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Stok Keluar --}}
    @if($stokKeluar->count() > 0)
        <div class="page-break"></div>
        <div class="section-title">STOK KELUAR</div>
        <table>
            <thead>
                <tr>
                    <th>Sumber</th>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Items</th>
                    <th>Total Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stokKeluar as $keluar)
                    <tr>
                        <td><strong>{{ $keluar['sumber'] }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($keluar['tanggal'])->format('d/m/Y H:i') }}</td>
                        <td>{{ $keluar['tipe'] }}</td>
                        <td>
                            @foreach ($keluar['items'] as $item)
                                <div class="variant-item">
                                    <strong>{{ $item['nama'] }}</strong><br>
                                    <span style="color: #666;">{{ $item['keterangan'] }}</span>
                                </div>
                            @endforeach
                        </td>
                        <td class="status-low">{{ collect($keluar['items'])->sum('qty') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem PT. Chaste Gemilang Mandiri</p>
        <p>Untuk informasi lebih lanjut, silakan hubungi tim gudang</p>
    </div>
</body>
</html>
