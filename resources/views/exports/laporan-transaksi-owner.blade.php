<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Owner</title>
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
        
        .pendapatan { color: #059669; }
        .pengeluaran { color: #dc2626; }
        .laba { color: #2563eb; }
        .hutang { color: #ea580c; }
        
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
    @include('exports.partials.header')

    <h1 style="text-align: center; color: #0f766e; font-size: 22px; margin-top: 0;">Laporan Transaksi Owner</h1>

    <div class="periode">
        <strong>Periode Laporan:</strong> {{ $periodeLabel ?? $periode ?? '-' }}<br>
        <span style="font-size: 11px; color: #475569;">
            Rentang tanggal: {{ isset($periodeStart) ? \Carbon\Carbon::parse($periodeStart)->translatedFormat('d F Y') : '-' }}
            &mdash;
            {{ isset($periodeEnd) ? \Carbon\Carbon::parse($periodeEnd)->translatedFormat('d F Y') : '-' }}
        </span>
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="summary-grid">
        <div class="summary-card">
            <h3>💰 Total Pendapatan</h3>
            <div class="amount pendapatan">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <small>{{ $pendapatan->count() }} transaksi</small>
        </div>
        
        <div class="summary-card">
            <h3>💸 Total Pengeluaran</h3>
            <div class="amount pengeluaran">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            <small>{{ $pengeluaran->count() }} pembayaran</small>
        </div>
        
        <div class="summary-card">
            <h3>📊 Laba Bersih</h3>
            <div class="amount laba">Rp {{ number_format($laba, 0, ',', '.') }}</div>
            <small>{{ $laba >= 0 ? 'Profit' : 'Rugi' }}</small>
        </div>
        
        <div class="summary-card">
            <h3>🏦 Hutang Customer</h3>
            <div class="amount hutang">Rp {{ number_format($totalHutangCustomer, 0, ',', '.') }}</div>
            <small>{{ $hutangCustomer->count() }} customer</small>
        </div>
    </div>

    {{-- Detail Transaksi Pendapatan --}}
    <div class="section">
        <h2>📈 Detail Transaksi Pendapatan</h2>
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
                @forelse($pendapatan as $index => $transaksi)
                @php
                    $invoiceDate = $transaksi->receive_date ?? $transaksi->created_at;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $invoiceDate ? \Carbon\Carbon::parse($invoiceDate)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $transaksi->code }}</td>
                    <td>{{ $transaksi->customer->name ?? '-' }}</td>
                    <td>Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</td>
                    <td>
                        <span class="{{ $transaksi->status == 'lunas' ? 'status-lunas' : 'status-hutang' }}">
                            {{ ucfirst($transaksi->status) }}
                        </span>
                    </td>
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
                    <td colspan="7" style="text-align: center; color: #666;">Tidak ada data transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Detail Pengeluaran --}}
    @if($pengeluaran->count() > 0)
    <div class="section">
        <h2>💸 Detail Pengeluaran (Pembayaran Hutang)</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Bayar</th>
                    <th>Supplier</th>
                    <th>Kode PO</th>
                    <th>Jumlah Bayar</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengeluaran as $index => $bayar)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $bayar->payment_date ? \Carbon\Carbon::parse($bayar->payment_date)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $bayar->purchaseOrder && $bayar->purchaseOrder->supplier ? $bayar->purchaseOrder->supplier->name : '-' }}</td>
                    <td>{{ $bayar->purchaseOrder ? $bayar->purchaseOrder->code : '-' }}</td>
                    <td>Rp {{ number_format($bayar->amount_paid, 0, ',', '.') }}</td>
                    <td>{{ $bayar->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Detail Hutang Piutang --}}
    @if($hutangPiutang->count() > 0)
    <div class="section">
        <h2>📋 Detail Hutang Piutang</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Order</th>
                    <th>Kode PO</th>
                    <th>Supplier</th>
                    <th>Total Hutang</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hutangPiutang as $index => $hutang)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $hutang->order_date ? \Carbon\Carbon::parse($hutang->order_date)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $hutang->code }}</td>
                    <td>{{ $hutang->supplier ? $hutang->supplier->name : '-' }}</td>
                    <td>Rp {{ number_format($hutang->total_amount, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($hutang->status ?? 'pending') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Detail Hutang Customer --}}
    @if($hutangCustomer->count() > 0)
    <div class="section">
        <h2>👥 Detail Hutang Customer</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Invoice</th>
                    <th>Kode Invoice</th>
                    <th>Customer</th>
                    <th>Total Hutang</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hutangCustomer as $index => $hutang)
                @php
                    $invoiceDate = $hutang->receive_date ?? $hutang->created_at;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $invoiceDate ? \Carbon\Carbon::parse($invoiceDate)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $hutang->code }}</td>
                    <td>{{ $hutang->customer ? $hutang->customer->name : '-' }}</td>
                    <td>Rp {{ number_format($hutang->grand_total, 0, ',', '.') }}</td>
                    <td>
                        <span class="status-hutang">{{ ucfirst($hutang->status) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis pada {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</p>
        <p>PT. CHASTE GEMILANG MANDIRI - Sistem Informasi Terpal</p>
        <p>Dicetak oleh: {{ Session::get('user')->name ?? 'System' }}</p>
    </div>
</body>
</html> 