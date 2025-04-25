<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    body {
      font-family: sans-serif;
      font-size: 12px;
      margin: 20px;
      color: #333;
    }

    h1 {
      text-align: center;
      font-size: 20px;
      margin-bottom: 0;
      color: #075e54;
    }

    p {
      text-align: center;
      margin-top: 4px;
      font-size: 12px;
      color: #555;
    }

    .tanggal {
      text-align: right;
      font-size: 12px;
      margin-top: 10px;
      color: #666;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 24px;
    }

    th, td {
      border: 1px solid #bbb;
      padding: 8px 10px;
      text-align: left;
    }

    th {
      background-color: #d9f2f2;
      color: #075e54;
      font-weight: bold;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .total-row td {
      font-weight: bold;
      background-color: #f1fdfa;
    }
  </style>
</head>
<body>

  <h1>LAPORAN TRANSAKSI PENJUALAN</h1>
  <p>PT. CHASTE GEMILANG MANDIRI</p>
  <div class="tanggal">Tanggal Unduh: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Kode</th>
        <th>Customer</th>
        <th>Admin</th>
        <th>Total</th>
        <th>Status</th>
        <th>Jenis</th>
      </tr>
    </thead>
    <tbody>
      @foreach($invoices as $invoice)
      <tr>
        <td>{{ $invoice->id }}</td>
        <td>{{ $invoice->code }}</td>
        <td>{{ optional($invoice->customer)->name ?? '-' }}</td>
        <td>{{ optional($invoice->employee)->name ?? '-' }}</td>
        <td>Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
        <td>{{ $invoice->status }}</td>
        <td>{{ $invoice->is_online ? 'Offline' : 'Online' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

</body>
</html>
