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
                <th>Ukuran</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total Harga</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cartItems as $item)
                <tr>
                    <td style="vertical-align: top;">
                        @if(isset($item->kebutuhan_custom) && $item->kebutuhan_custom)
                            <strong>Custom Terpal</strong>
                            <div style="font-size: 11px; color: #666; margin-top: 4px; line-height: 1.4;">
                                @if($item->bahan_custom)
                                    <div><strong>Bahan:</strong> {{ $item->bahan_custom }}</div>
                                @endif
                                <div><strong>Kebutuhan:</strong> {{ $item->kebutuhan_custom }}</div>
                                @if($item->ukuran_custom)
                                    <div><strong>Ukuran:</strong> {{ $item->ukuran_custom }}</div>
                                @endif
                                @if($item->jumlah_ring_custom)
                                    <div><strong>Ring:</strong> {{ $item->jumlah_ring_custom }} buah</div>
                                @endif
                                @if($item->pakai_tali_custom)
                                    <div><strong>Tali:</strong> 
                                        @if($item->pakai_tali_custom == 'ya' || $item->pakai_tali_custom == '1' || $item->pakai_tali_custom == 1)
                                            Ya
                                        @elseif($item->pakai_tali_custom == 'tidak' || $item->pakai_tali_custom == '0' || $item->pakai_tali_custom == 0)
                                            Tidak
                                        @else
                                            {{ $item->pakai_tali_custom }}
                                        @endif
                                    </div>
                                @endif
                                @if($item->catatan_custom)
                                    <div><strong>Catatan:</strong> {{ $item->catatan_custom }}</div>
                                @endif
                            </div>
                        @else
                            {{ $item->product_name ?? 'Produk Biasa' }}
                        @endif
                    </td>
                    <td>{{ $item->warna_custom ?? $item->variant_color ?? '-' }}</td>
                    <td>
                        @php
                            $sizeText = '-';
                            // Prioritas 1: ukuran_custom
                            if (!empty($item->ukuran_custom)) {
                                $sizeText = $item->ukuran_custom;
                            }
                            // Prioritas 2: selected_size
                            elseif(isset($item->selected_size) && $item->selected_size) {
                                $sizeText = $item->selected_size;
                            }
                            // Prioritas 3: ekstrak dari kebutuhan_custom
                            elseif (!empty($item->kebutuhan_custom)) {
                                if (preg_match('/\(?\s*(\d+)\s*[xX×]\s*(\d+)\s*\)?/', $item->kebutuhan_custom, $matches)) {
                                    $sizeText = $matches[1] . 'x' . $matches[2];
                                } elseif (preg_match('/ukuran\s*:?\s*(\d+)\s*[xX×]\s*(\d+)/i', $item->kebutuhan_custom, $matches)) {
                                    $sizeText = $matches[1] . 'x' . $matches[2];
                                } elseif (preg_match('/(\d+)\s*[xX×]\s*(\d+)/', $item->kebutuhan_custom, $matches)) {
                                    $sizeText = $matches[1] . 'x' . $matches[2];
                                }
                            }
                        @endphp
                        {{ $sizeText }}
                    </td>
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
    
        <!-- Rincian Total -->
        <div class="total" style="margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <tr>
                    <td style="text-align: right; padding: 5px 10px; border: none;"><strong>Subtotal Produk:</strong></td>
                    <td style="text-align: right; padding: 5px 10px; border: none; width: 150px;">
                        Rp {{ number_format($invoice->grand_total - ($invoice->shipping_cost ?? 0), 0, ',', '.') }}
                    </td>
                </tr>
                @if($invoice->shipping_cost > 0)
                <tr>
                    <td style="text-align: right; padding: 5px 10px; border: none;">
                        <strong>Ongkos Kirim:</strong>
                        @if($invoice->shipping_courier || $invoice->shipping_service)
                            <br><span style="font-size: 11px; color: #666;">
                                ({{ $invoice->shipping_courier ? ucfirst($invoice->shipping_courier) : 'Kurir Perusahaan' }}
                                @if($invoice->shipping_service)
                                    - {{ $invoice->shipping_service }}
                                @endif)
                            </span>
                        @endif
                    </td>
                    <td style="text-align: right; padding: 5px 10px; border: none;">
                        Rp {{ number_format($invoice->shipping_cost, 0, ',', '.') }}
                    </td>
                </tr>
                @endif
                <tr style="border-top: 2px solid #ddd;">
                    <td style="text-align: right; padding: 10px; border: none;"><strong>Total:</strong></td>
                    <td style="text-align: right; padding: 10px; border: none;">
                        <strong>Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </table>
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
