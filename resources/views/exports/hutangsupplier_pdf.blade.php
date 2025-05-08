<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Piutang (Urut Jatuh Tempo)</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #1f2937;
        }
        h2 {
            color: #0f766e;
            margin-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #ccfbf1;
            color: #0f766e;
        }
        .summary-total {
            margin-top: 10px;
            text-align: right;
            font-weight: bold;
            color: #0f766e;
        }
    </style>
</head>
<body>
    <h2>Laporan Piutang Supplier (Urut Jatuh Tempo)</h2>
    <p>Dicetak: {{ $tanggal }}</p>

    <table>
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Kode PO</th>
                <th>Tanggal PO</th>
                <th>Jatuh Tempo</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hutang as $item)
            <tr>
                <td>{{ $item->supplier->name }}</td>
                <td>{{ $item->code }}</td>
                <td>{{ \Carbon\Carbon::parse($item->order_date)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($item->due_date)->format('d-m-Y') }}</td>
                <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="summary-total">
        Total Seluruh Piutang: Rp {{ number_format($total, 0, ',', '.') }}
    </p>
</body>
</html>
