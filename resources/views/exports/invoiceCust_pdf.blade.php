<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->code }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        h1 { color: teal; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ccc; }
        th { background-color: #E0F2F1; }
        .total { text-align: right; font-weight: bold; }
    </style>
</head>
<body>

    <h1>Invoice</h1>
    <hr>

    <p><strong>Kode Invoice:</strong> {{ $invoice->code }}</p>
    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') }}</p>
    <p><strong>Alamat Pengiriman:</strong> {{ $invoice->address }}</p>

    <h3>Detail Pesanan:</h3>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Warna</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cartItems as $item)
                <tr>
                    <td>
                        @if($item->kebutuhan_custom)
                        Produk Custom ( {{ $item->kebutuhan_custom }} )
                        @else
                            {{ $item->product_name ?? 'Produk Biasa' }}
                        @endif
                    </td>
                    <td>
                        @if($item->warna_custom)
                            {{ $item->warna_custom }}
                        @else
                            {{ $item->variant_color ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>
                        Rp 
                        @if($item->kebutuhan_custom)
                            {{ number_format($item->harga_custom ?? 0, 0, ',', '.') }}
                        @else
                            {{ number_format($item->product_price ?? 0, 0, ',', '.') }}
                        @endif
                    </td>
                    <td>
                        Rp 
                        @if($item->kebutuhan_custom)
                            {{ number_format(($item->harga_custom ?? 0) * $item->quantity, 0, ',', '.') }}
                        @else
                            {{ number_format(($item->product_price ?? 0) * $item->quantity, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="total">Total: Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</h2>

</body>
</html>
