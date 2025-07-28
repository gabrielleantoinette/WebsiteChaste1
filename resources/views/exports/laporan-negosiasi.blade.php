<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Negosiasi Harga</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #075e54;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #075e54;
            font-size: 24px;
            margin: 0;
            font-weight: bold;
        }
        
        .header p {
            color: #666;
            margin: 5px 0;
        }
        
        .periode {
            background-color: #f0f9ff;
            border: 1px solid #0ea5e9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .summary-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .summary-card {
            width: 48%;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        .summary-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #075e54;
        }
        
        .summary-card .amount {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .berhasil { color: #059669; }
        .gagal { color: #dc2626; }
        .pending { color: #d97706; }
        .total { color: #8b5cf6; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #075e54;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section h2 {
            color: #075e54;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 1px solid #075e54;
            padding-bottom: 5px;
        }
        
        .status-berhasil {
            background-color: #dcfce7;
            color: #166534;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .status-gagal {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .status-lunas {
            background-color: #dcfce7;
            color: #166534;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .status-hutang {
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN NEGOSIASI HARGA</h1>
        <p>PT. CHASTE GEMILANG MANDIRI</p>
        <p>Jl. Raya Terpal No. 123, Jakarta</p>
        <p>Telp: (021) 1234-5678 | Email: info@chaste.com</p>
    </div>

    <div class="periode">
        <strong>Periode Laporan:</strong> {{ $periode }}
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="summary-grid">
        <div class="summary-card">
            <h3>✅ Negosiasi Berhasil</h3>
            <div class="amount berhasil">{{ $negosiasiBerhasil->count() }} negosiasi</div>
            <small>Persentase: {{ number_format($persentaseBerhasil, 1) }}%</small>
        </div>
        
        <div class="summary-card">
            <h3>❌ Negosiasi Gagal</h3>
            <div class="amount gagal">{{ $negosiasiGagal->count() }} negosiasi</div>
            <small>Persentase: {{ $totalNegosiasi > 0 ? number_format(($negosiasiGagal->count() / $totalNegosiasi) * 100, 1) : 0 }}%</small>
        </div>
        
        <div class="summary-card">
            <h3>⏳ Negosiasi Pending</h3>
            <div class="amount pending">{{ $negosiasiPending->count() }} negosiasi</div>
            <small>Persentase: {{ $totalNegosiasi > 0 ? number_format(($negosiasiPending->count() / $totalNegosiasi) * 100, 1) : 0 }}%</small>
        </div>
        
        <div class="summary-card">
            <h3>📊 Total Negosiasi</h3>
            <div class="amount total">{{ $totalNegosiasi }} negosiasi</div>
            <small>Rate keberhasilan: {{ number_format($persentaseBerhasil, 1) }}%</small>
        </div>
    </div>

    {{-- Detail Negosiasi Berhasil --}}
    <div class="section">
        <h2>✅ Detail Negosiasi Berhasil</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Produk</th>
                    <th>Harga Asli</th>
                    <th>Harga Negosiasi</th>
                    <th>Selisih</th>
                </tr>
            </thead>
            <tbody>
                @forelse($negosiasiBerhasil as $index => $negosiasi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $negosiasi->created_at ? \Carbon\Carbon::parse($negosiasi->created_at)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $negosiasi->customer->name ?? '-' }}</td>
                    <td>{{ $negosiasi->product->name ?? '-' }}</td>
                    <td>Rp {{ number_format($negosiasi->original_price ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($negosiasi->negotiated_price ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format(($negosiasi->original_price ?? 0) - ($negosiasi->negotiated_price ?? 0), 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #666;">Tidak ada negosiasi berhasil.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Detail Negosiasi Gagal --}}
    @if($negosiasiGagal->count() > 0)
    <div class="section">
        <h2>❌ Detail Negosiasi Gagal</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Produk</th>
                    <th>Harga Asli</th>
                    <th>Harga Negosiasi</th>
                    <th>Alasan Penolakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($negosiasiGagal as $index => $negosiasi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $negosiasi->created_at ? \Carbon\Carbon::parse($negosiasi->created_at)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $negosiasi->customer->name ?? '-' }}</td>
                    <td>{{ $negosiasi->product->name ?? '-' }}</td>
                    <td>Rp {{ number_format($negosiasi->original_price ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($negosiasi->negotiated_price ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $negosiasi->rejection_reason ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Detail Negosiasi Pending --}}
    @if($negosiasiPending->count() > 0)
    <div class="section">
        <h2>⏳ Detail Negosiasi Pending</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Produk</th>
                    <th>Harga Asli</th>
                    <th>Harga Negosiasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($negosiasiPending as $index => $negosiasi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $negosiasi->created_at ? \Carbon\Carbon::parse($negosiasi->created_at)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $negosiasi->customer->name ?? '-' }}</td>
                    <td>{{ $negosiasi->product->name ?? '-' }}</td>
                    <td>Rp {{ number_format($negosiasi->original_price ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($negosiasi->negotiated_price ?? 0, 0, ',', '.') }}</td>
                    <td>
                        <span class="status-pending">{{ ucfirst($negosiasi->status) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Statistik Tambahan --}}
    <div class="section">
        <h2>📊 Analisis Negosiasi</h2>
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Total Potensi Penjualan</h3>
                <div class="amount total">Rp {{ number_format($negosiasiBerhasil->sum('negotiated_price') + $negosiasiPending->sum('negotiated_price'), 0, ',', '.') }}</div>
                <small>Dari negosiasi berhasil + pending</small>
            </div>
            
            <div class="summary-card">
                <h3>Total Potensi Kerugian</h3>
                <div class="amount gagal">Rp {{ number_format($negosiasiGagal->sum('original_price'), 0, ',', '.') }}</div>
                <small>Dari negosiasi yang ditolak</small>
            </div>
            
            <div class="summary-card">
                <h3>Rata-rata Diskon</h3>
                <div class="amount berhasil">{{ $negosiasiBerhasil->count() > 0 ? number_format($negosiasiBerhasil->avg(function($n) { return (($n->original_price - $n->negotiated_price) / $n->original_price) * 100; }), 1) : 0 }}%</div>
                <small>Dari negosiasi berhasil</small>
            </div>
            
            <div class="summary-card">
                <h3>Efisiensi Negosiasi</h3>
                <div class="amount pending">{{ $totalNegosiasi > 0 ? number_format(($negosiasiBerhasil->count() / $totalNegosiasi) * 100, 1) : 0 }}%</div>
                <small>Persentase berhasil dari total</small>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis pada {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</p>
        <p>PT. CHASTE GEMILANG MANDIRI - Sistem Informasi Terpal</p>
        <p>Dicetak oleh: {{ Session::get('user')->name ?? 'System' }}</p>
    </div>
</body>
</html> 