<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->code }}</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 40px;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        h2 {
            color: #008080;
            margin-bottom: 10px;
        }

        .header-info p {
            margin: 4px 0;
        }

        hr {
            margin: 20px 0;
            border: 1px solid #e0e0e0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 13px;
        }

        th {
            background-color: #E0F2F1;
            color: #004d4d;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .total {
            text-align: right;
            margin-top: 20px;
        }

        .total h3 {
            background-color: #A7FFEB;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 16px;
            color: #004d4d;
        }

        .thanks {
            margin-top: 40px;
            text-align: center;
            font-weight: bold;
            color: #555;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="container">

        <!-- Kop Surat -->
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
            {{-- Logo di kiri --}}
            <img src="{{ public_path('images/logo-perusahaan.png') }}" alt="Logo" style="height: 60px;">
        
            {{-- Teks di kanan --}}
            <div style="line-height: 1.4;">
                <h2 style="margin: 0; color: #008080; font-size: 20px;">PT. Chaste Gemilang Mandiri</h2>
                <p style="margin: 0; font-size: 13px; color: #333;">
                    031-5990710 | chastegemilangmandiri@gmail.com
                </p>
            </div>
        </div>
        
        <hr style="margin-top: 20px;">      
    
        <!-- Judul & Info Invoice -->
        <h2 style="color:#008080;">INVOICE</h2>
    
        <div class="header-info">
            <p><strong>Kepada:</strong> {{ $invoice->customer->name ?? '-' }}</p>
            <p><strong>Alamat:</strong> {{ $invoice->address }}</p>
            <p><strong>No. Invoice:</strong> {{ $invoice->code }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') }}</p>
        </div>
    
        <hr>
    
        <!-- Detail Pesanan -->
        <h3 style="color:#008080;">Detail Pesanan</h3>
        
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
                        @if(isset($item->kebutuhan_custom) && $item->kebutuhan_custom)
                            Produk Custom ({{ $item->kebutuhan_custom }})
                        @else
                            {{ $item->product_name ?? 'Produk Biasa' }}
                        @endif
                    </td>
                    <td>{{ $item->warna_custom ?? $item->variant_color ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>
                        Rp {{ number_format(isset($item->kebutuhan_custom) && $item->kebutuhan_custom ? ($item->harga_custom ?? 0) : ($item->price ?? 0), 0, ',', '.') }}
                    </td>
                    <td>
                        Rp {{ number_format((isset($item->kebutuhan_custom) && $item->kebutuhan_custom ? ($item->harga_custom ?? 0) : ($item->price ?? 0)) * $item->quantity, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    
        <!-- Total -->
        <div class="total">
            <h3 style="
                background-color: #00BFA5;
                color: white;
                padding: 10px 25px;
                display: inline-block;
                border-radius: 8px;
                margin-top: 20px;
            ">
                Total: Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
            </h3>
        </div>
    
        <!-- Penutup -->
        <div class="thanks" style="margin-top: 40px; text-align: center; font-weight: bold; color: #555;">
            TERIMA KASIH ATAS PEMBELIAN ANDA
        </div>
    
        <div class="footer">
            PT. Chaste Gemilang Mandiri |
            031-5990710 |
            chastegemilangmandiri@gmail.com
        </div>
    </div>    

</body>
</html>
