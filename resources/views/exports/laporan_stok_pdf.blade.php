<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok {{ $judulPeriode }}</title>
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
            font-size: 14px;
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

        .summary-cell h4 {
            margin: 0 0 5px 0;
            font-size: 10px;
            color: #666;
        }

        .summary-cell .value {
            font-size: 16px;
            font-weight: bold;
        }

        .stok-saat-ini { color: #2563eb; }
        .stok-masuk { color: #059669; }
        .stok-keluar { color: #dc2626; }
        .sisa-stok { color: #7c3aed; }

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
            font-size: 10px;
        }

        td {
            padding: 8px;
            border: 1px solid #ccc;
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .status-low {
            color: #dc2626;
            font-weight: bold;
        }

        .status-good {
            color: #059669;
            font-weight: bold;
        }

        .variant-item {
            margin: 2px 0;
            font-size: 9px;
        }

        .empty-state {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }

        .page-break {
            page-break-before: always;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Laporan Stok {{ $judulPeriode }}</h1>

    {{-- Ringkasan --}}
    <div class="summary-grid">
        <div class="summary-cell">
            <h4>Stok Saat Ini</h4>
            <div class="value stok-saat-ini">{{ number_format($stokSaatIni->sum('stok')) }}</div>
        </div>
        <div class="summary-cell">
            <h4>Stok Masuk</h4>
            <div class="value stok-masuk">{{ number_format($stokMasuk->sum(function($item) { return collect($item['items'])->sum('qty'); })) }}</div>
        </div>
        <div class="summary-cell">
            <h4>Stok Keluar</h4>
            <div class="value stok-keluar">{{ number_format($stokKeluar->sum(function($item) { return collect($item['items'])->sum('qty'); })) }}</div>
        </div>
        <div class="summary-cell">
            <h4>Sisa Stok</h4>
            <div class="value sisa-stok">
                {{ number_format($stokSaatIni->sum('stok') + $stokMasuk->sum(function($item) { return collect($item['items'])->sum('qty'); }) - $stokKeluar->sum(function($item) { return collect($item['items'])->sum('qty'); })) }}
            </div>
        </div>
    </div>

    {{-- Stok Saat Ini --}}
    <h3>Stok Saat Ini</h3>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Tipe</th>
                <th class="text-right">Total Stok</th>
                <th>Detail Variant</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stokSaatIni as $item)
                <tr>
                    <td><strong>{{ $item['nama'] }}</strong></td>
                    <td>{{ $item['kategori'] }}</td>
                    <td>{{ $item['tipe'] }}</td>
                    <td class="text-right {{ $item['stok'] <= 10 ? 'status-low' : 'status-good' }}">
                        {{ number_format($item['stok']) }}
                    </td>
                    <td>
                        @forelse ($item['variants'] as $variant)
                            <div class="variant-item">
                                {{ $variant['warna'] }}: {{ $variant['stok'] }}
                            </div>
                        @empty
                            <span class="empty-state">Tidak ada variant</span>
                        @endforelse
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-state">Tidak ada data stok</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Stok Masuk --}}
    <h3>Stok Masuk</h3>
    @if($stokMasuk->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Sumber</th>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Items</th>
                    <th class="text-right">Total Qty</th>
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
                                    {{ $item['nama'] }} ({{ $item['qty'] }})
                                </div>
                            @endforeach
                        </td>
                        <td class="text-right status-good">
                            {{ collect($masuk['items'])->sum('qty') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">Tidak ada stok masuk dalam periode ini</div>
    @endif

    {{-- Stok Keluar --}}
    <h3>Stok Keluar</h3>
    @if($stokKeluar->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Sumber</th>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Items</th>
                    <th class="text-right">Total Qty</th>
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
                                    {{ $item['nama'] }} ({{ $item['qty'] }})
                                </div>
                            @endforeach
                        </td>
                        <td class="text-right status-low">
                            {{ collect($keluar['items'])->sum('qty') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">Tidak ada stok keluar dalam periode ini</div>
    @endif

    <div class="footer">
        <p>Laporan dibuat pada {{ now()->format('d F Y H:i') }}</p>
        <p>Sistem Informasi Mahasiswa iSTTS</p>
    </div>
</body>
</html>
