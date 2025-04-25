<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    th { background-color: #e5f6f6; }
    h2 { text-align: center; margin-bottom: 0; }
    p { text-align: center; margin-top: 4px; font-size: 12px; }
  </style>
</head>
<body>

  <h1>LAPORAN TRANSAKSI PENJUALAN</h1>
  <p>PT. CHASTE GEMILANG MANDIRI</p>

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
        <td>Rp {{ number_format($invoice->grand_total) }}</td>
        <td>{{ $invoice->status }}</td>
        <td>{{ $invoice->is_online ? 'Offline' : 'Online' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

</body>
</html>
