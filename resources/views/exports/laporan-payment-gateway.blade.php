<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Payment Gateway</title>
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
        .pending { color: #d97706; }
        .gagal { color: #dc2626; }
        .total { color: #3b82f6; }
        
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
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
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
        <h1>LAPORAN PAYMENT GATEWAY</h1>
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
            <h3>✅ Transaksi Berhasil</h3>
            <div class="amount berhasil">{{ $transaksiBerhasil->count() }} transaksi</div>
            <small>Total: Rp {{ number_format($transaksiBerhasil->sum('grand_total'), 0, ',', '.') }}</small>
        </div>
        
        <div class="summary-card">
            <h3>⏳ Transaksi Pending</h3>
            <div class="amount pending">{{ $transaksiPending->count() }} transaksi</div>
            <small>Total: Rp {{ number_format($transaksiPending->sum('grand_total'), 0, ',', '.') }}</small>
        </div>
        
        <div class="summary-card">
            <h3>❌ Transaksi Gagal</h3>
            <div class="amount gagal">{{ $transaksiGagal->count() }} transaksi</div>
            <small>Total: Rp {{ number_format($transaksiGagal->sum('grand_total'), 0, ',', '.') }}</small>
        </div>
        
        <div class="summary-card">
            <h3>💰 Total Penjualan</h3>
            <div class="amount total">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
            <small>{{ $totalTransaksi }} total transaksi</small>
        </div>
    </div>

    {{-- Detail Transaksi Berhasil --}}
    <div class="section">
        <h2>✅ Detail Transaksi Berhasil</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode Invoice</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Metode Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksiBerhasil as $index => $transaksi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $transaksi->created_at ? \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $transaksi->code }}</td>
                    <td>{{ $transaksi->customer->name ?? '-' }}</td>
                    <td>Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</td>
                    <td>
                        @if($transaksi->payments && $transaksi->payments->count())
                            {{ $transaksi->payments->pluck('method')->unique()->implode(', ') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #666;">Tidak ada transaksi berhasil.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Detail Transaksi Pending --}}
    @if($transaksiPending->count() > 0)
    <div class="section">
        <h2>⏳ Detail Transaksi Pending</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode Invoice</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Metode Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksiPending as $index => $transaksi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $transaksi->created_at ? \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $transaksi->code }}</td>
                    <td>{{ $transaksi->customer->name ?? '-' }}</td>
                    <td>Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</td>
                    <td>
                        @if($transaksi->payments && $transaksi->payments->count())
                            {{ $transaksi->payments->pluck('method')->unique()->implode(', ') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Detail Transaksi Gagal --}}
    @if($transaksiGagal->count() > 0)
    <div class="section">
        <h2>❌ Detail Transaksi Gagal</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode Invoice</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Metode Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksiGagal as $index => $transaksi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $transaksi->created_at ? \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $transaksi->code }}</td>
                    <td>{{ $transaksi->customer->name ?? '-' }}</td>
                    <td>Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</td>
                    <td>
                        <span class="status-gagal">{{ ucfirst($transaksi->status) }}</span>
                    </td>
                    <td>
                        @if($transaksi->payments && $transaksi->payments->count())
                            {{ $transaksi->payments->pluck('method')->unique()->implode(', ') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Statistik Tambahan --}}
    <div class="section">
        <h2>📊 Statistik Payment Gateway</h2>
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Rate Keberhasilan</h3>
                <div class="amount berhasil">{{ $totalTransaksi > 0 ? number_format(($transaksiBerhasil->count() / $totalTransaksi) * 100, 1) : 0 }}%</div>
                <small>{{ $transaksiBerhasil->count() }} dari {{ $totalTransaksi }} transaksi</small>
            </div>
            
            <div class="summary-card">
                <h3>Rate Pending</h3>
                <div class="amount pending">{{ $totalTransaksi > 0 ? number_format(($transaksiPending->count() / $totalTransaksi) * 100, 1) : 0 }}%</div>
                <small>{{ $transaksiPending->count() }} dari {{ $totalTransaksi }} transaksi</small>
            </div>
            
            <div class="summary-card">
                <h3>Rate Kegagalan</h3>
                <div class="amount gagal">{{ $totalTransaksi > 0 ? number_format(($transaksiGagal->count() / $totalTransaksi) * 100, 1) : 0 }}%</div>
                <small>{{ $transaksiGagal->count() }} dari {{ $totalTransaksi }} transaksi</small>
            </div>
            
            <div class="summary-card">
                <h3>Rata-rata Nilai Transaksi</h3>
                <div class="amount total">Rp {{ $totalTransaksi > 0 ? number_format($totalPenjualan / $totalTransaksi, 0, ',', '.') : '0' }}</div>
                <small>Per transaksi berhasil</small>
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