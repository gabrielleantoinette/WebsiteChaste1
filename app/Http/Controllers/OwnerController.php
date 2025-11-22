<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\HInvoice;
use Illuminate\Http\Request;
use App\Models\PaymentModel;
use App\Models\DebtPayment;
use Carbon\Carbon;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\NegotiationTable;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\NotificationService;
use App\Support\ReportDateRange;

class OwnerController extends Controller
{
    public function viewAssignDriver()
    {
        $drivers = Employee::where('role', 'driver')->get();
        
        $pengirimanNormal = HInvoice::whereIn('status', ['dikemas', 'dikirim_ke_agen'])
            ->whereNotNull('gudang_id')
            ->whereNotIn('status', ['retur_diajukan', 'retur_diambil'])
            ->get();
            
        $pengirimanKurir = $pengirimanNormal->filter(function($inv) {
            return !$inv->shipping_courier || $inv->shipping_courier === 'kurir';
        });
        
        $pengirimanEkspedisi = $pengirimanNormal->filter(function($inv) {
            return $inv->shipping_courier && $inv->shipping_courier !== 'kurir';
        });
            
        $pengirimanRetur = HInvoice::whereIn('status', ['retur_diajukan', 'retur_diambil'])->get();
        
        return view('admin.assign-driver.view', compact('pengirimanNormal', 'pengirimanKurir', 'pengirimanEkspedisi', 'pengirimanRetur', 'drivers'));
    }

    public function createAssignDriver($id)
    {
        $invoice = HInvoice::with('customer')->findOrFail($id);
        $drivers = Employee::where('role', 'driver')->where('active', true)->get();
        
        return view('admin.assign-driver.create', compact('invoice', 'drivers'));
    }

    public function assignDriver($id, Request $request)
    {
        $request->validate([
            'driver_id' => 'required|exists:employees,id'
        ]);

        $invoice = HInvoice::with(['customer', 'details.product', 'details.variant'])->findOrFail($id);
        
        // Verify driver exists and is active
        $driver = Employee::where('id', $request->driver_id)
                          ->where('role', 'driver')
                          ->where('active', true)
                          ->first();
        
        if (!$driver) {
            return redirect()->back()->with('error', 'Driver tidak ditemukan atau tidak aktif.');
        }
        
        $invoice->driver_id = $request->driver_id;
        
        if ($invoice->status === 'retur_diajukan') {
            $invoice->status = 'retur_diambil';
            
            $notificationService = app(NotificationService::class);
            $notificationService->notifyReturnReadyForPickup([
                'id' => $invoice->id,
                'customer_name' => $invoice->customer->name
            ]);

            $notificationService->notifyReturnApproved($invoice->id, $invoice->customer_id, [
                'order_id' => $invoice->code,
                'customer_name' => $invoice->customer->name
            ]);
        } else {
            if ($invoice->shipping_courier && $invoice->shipping_courier !== 'kurir') {
                \Log::info('Creating Biteship order for expedition', [
                    'invoice_id' => $invoice->id,
                    'invoice_code' => $invoice->code,
                    'shipping_courier' => $invoice->shipping_courier,
                    'shipping_service' => $invoice->shipping_service,
                    'address' => $invoice->address
                ]);
                
                try {
                    $biteshipService = app(\App\Services\BiteshipService::class);
                    
                    $originArea = $biteshipService->searchArea('Surabaya');
                    $destinationCity = $this->extractCityFromAddress($invoice->address);
                    $destinationArea = $biteshipService->searchArea($destinationCity);
                    
                    \Log::info('Biteship area search results', [
                        'origin_area' => $originArea ? ['id' => $originArea['id'], 'name' => $originArea['name'] ?? null] : null,
                        'destination_city' => $destinationCity,
                        'destination_area' => $destinationArea ? ['id' => $destinationArea['id'], 'name' => $destinationArea['name'] ?? null] : null
                    ]);
                    
                    if ($originArea && $destinationArea) {
                        $totalWeight = max(count($invoice->details) * 1000, 1000);
                        
                        $items = [];
                        foreach ($invoice->details as $detail) {
                            $items[] = [
                                'name' => $detail->product->name ?? 'Item',
                                'description' => $detail->variant->color ?? '',
                                'value' => $detail->price * $detail->quantity,
                                'length' => 10,
                                'width' => 10,
                                'height' => 10,
                                'weight' => 1000 // Estimasi 1kg per item
                            ];
                        }
                        
                        if (empty($items)) {
                            $items[] = [
                                'name' => 'Paket',
                                'description' => 'Paket pengiriman',
                                'value' => $invoice->grand_total,
                                'length' => 10,
                                'width' => 10,
                                'height' => 10,
                                'weight' => $totalWeight
                            ];
                        }
                        
                        $courierType = null;
                        $rates = $biteshipService->getRates(
                            $originArea['id'],
                            $destinationArea['id'],
                            $totalWeight,
                            [strtolower($invoice->shipping_courier)]
                        );
                        
                        foreach ($rates as $rate) {
                            $serviceName = $rate['courier_service_name'] ?? '';
                            if (strcasecmp($serviceName, $invoice->shipping_service) === 0 || 
                                str_contains(strtolower($serviceName), strtolower($invoice->shipping_service))) {
                                $courierType = $rate['courier_service_code'] ?? $rate['type'] ?? null;
                                \Log::info('Found matching service code from getRates', [
                                    'service_name' => $serviceName,
                                    'service_code' => $courierType,
                                    'rate_keys' => array_keys($rate ?? [])
                                ]);
                                break;
                            }
                        }
                        
                        if (!$courierType) {
                            $courierType = $this->mapShippingServiceToBiteship(
                                $invoice->shipping_service, 
                                strtolower($invoice->shipping_courier)
                            );
                            
                            \Log::info('Using mapped shipping service to Biteship format', [
                                'original_service' => $invoice->shipping_service,
                                'courier' => $invoice->shipping_courier,
                                'mapped_courier_type' => $courierType
                            ]);
                        }
                        
                        $orderData = [
                            'origin_contact_name' => 'Chaste Gemilang Mandiri',
                            'origin_contact_phone' => '081234567890', // Ganti dengan nomor perusahaan
                            'origin_address' => 'Surabaya', // Ganti dengan alamat lengkap perusahaan
                            'origin_area_id' => $originArea['id'],
                            'destination_contact_name' => $invoice->customer->name,
                            'destination_contact_phone' => $invoice->customer->phone ?? '081234567890',
                            'destination_address' => $invoice->address,
                            'destination_area_id' => $destinationArea['id'],
                            'courier_company' => strtolower($invoice->shipping_courier), // jne, pos, tiki, dll
                            'courier_type' => $courierType,
                            'delivery_type' => 'now',
                            'items' => $items
                        ];
                        
                        $biteshipOrder = $biteshipService->createOrder($orderData);
                        
                        if ($biteshipOrder) {
                            $waybillId = $biteshipService->extractWaybillId($biteshipOrder);
                            
                            if (!$waybillId && isset($biteshipOrder['id'])) {
                                $waybillId = $biteshipService->getWaybillIdWithPolling($biteshipOrder['id'], 3, 2);
                            }
                            
                            if ($waybillId) {
                                $invoice->tracking_number = $waybillId;
                                \Log::info('Biteship waybill ID saved to invoice', [
                                    'invoice_id' => $invoice->id,
                                    'waybill_id' => $waybillId,
                                    'before_save' => $invoice->getOriginal('tracking_number')
                                ]);
                            } else {
                                \Log::warning('Biteship order created but waybill ID not available yet', [
                                    'invoice_id' => $invoice->id,
                                    'biteship_order_id' => $biteshipOrder['id'] ?? null,
                                    'full_response' => $biteshipOrder
                                ]);
                            }
                        } else {
                            \Log::error('Failed to create Biteship order', [
                                'invoice_id' => $invoice->id
                            ]);
                        }
                    } else {
                        \Log::warning('Failed to find area for Biteship order', [
                            'invoice_id' => $invoice->id,
                            'origin_area' => $originArea ? 'found' : 'not found',
                            'destination_area' => $destinationArea ? 'found' : 'not found',
                            'destination_city' => $destinationCity
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error creating Biteship order', [
                        'invoice_id' => $invoice->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                $invoice->status = 'dikirim_ke_agen';
            } else {
                $invoice->status = 'dikirim';
            }
            
            $notificationService = app(NotificationService::class);
            $notificationService->notifyOrderReadyForDelivery([
                'id' => $invoice->id,
                'code' => $invoice->code,
                'customer_name' => $invoice->customer->name
            ]);

            if (!$invoice->shipping_courier || $invoice->shipping_courier === 'kurir') {
            $notificationService->notifyOrderShipped($invoice->id, $invoice->customer_id, [
                'invoice_code' => $invoice->code,
                'customer_name' => $invoice->customer->name
            ]);
            }
        }
        
        $invoice->save();
        
        $invoice->refresh();
        \Log::info('Invoice saved after assign driver', [
            'invoice_id' => $invoice->id,
            'tracking_number' => $invoice->tracking_number,
            'status' => $invoice->status,
            'shipping_courier' => $invoice->shipping_courier
        ]);

        return redirect()->route('admin.assign-driver.index')->with('success', 'Driver berhasil di-assign untuk pengiriman ini.');
    }

    /**
     * Map shipping service name to Biteship courier type format
     * 
     * @param string $serviceName Service name from database (e.g., "Reguler", "REG", "OKE", etc.)
     * @param string $courier Courier code (jne, pos, tiki, etc.)
     * @return string Biteship courier type format
     */
    private function mapShippingServiceToBiteship($serviceName, $courier)
    {
        if (!$serviceName) {
            return 'reg';
        }

        $serviceNameUpper = strtoupper(trim($serviceName));
        
        if ($courier === 'jne') {
            $jneMapping = [
                'REGULER' => 'reg',
                'REG' => 'reg',
                'OKE' => 'oke',
                'YES' => 'yes',
                'JTR' => 'jtr',
                'JTR250' => 'jtr250',
                'JTR150' => 'jtr150',
                'JTR18' => 'jtr18',
                'JTR250Y' => 'jtr250y',
                'JTR150Y' => 'jtr150y',
                'JTR74' => 'jtr74',
                'JTR74Y' => 'jtr74y',
                'JTRLITE' => 'jtrlite',
                'JTRLITEY' => 'jtrlitey',
            ];
            
            if (isset($jneMapping[$serviceNameUpper])) {
                return $jneMapping[$serviceNameUpper];
            }
            
            if (str_contains($serviceNameUpper, 'REGULER') || str_contains($serviceNameUpper, 'REGULAR')) {
                return 'reg';
            }
        }
        
        if ($courier === 'pos') {
            $posMapping = [
                'REGULER' => 'reg',
                'REG' => 'reg',
                'KILAT' => 'kilat',
                'EXPRESS' => 'express',
                'SURAT KILAT KHUSUS' => 'surat kilat khusus',
            ];
            
            if (isset($posMapping[$serviceNameUpper])) {
                return $posMapping[$serviceNameUpper];
            }
        }
        
        if ($courier === 'tiki') {
            $tikiMapping = [
                'REGULER' => 'reg',
                'REG' => 'reg',
                'ECO' => 'eco',
                'ONS' => 'ons',
                'HDS' => 'hds',
            ];
            
            if (isset($tikiMapping[$serviceNameUpper])) {
                return $tikiMapping[$serviceNameUpper];
            }
        }
        
        if (preg_match('/^[a-z0-9]+$/', strtolower($serviceName))) {
            return strtolower($serviceName);
        }
        
        \Log::warning('Unknown shipping service format, using reg as default', [
            'service_name' => $serviceName,
            'courier' => $courier
        ]);
        
        return 'reg';
    }

    /**
     * Extract city name from address string
     * 
     * @param string $address Full address string
     * @return string City name
     */
    private function extractCityFromAddress($address)
    {
        if (!$address) {
            return 'Surabaya';
        }

        $cities = [
            'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 
            'Palembang', 'Depok', 'Tangerang', 'Bekasi', 'Yogyakarta', 'Malang',
            'Banyuwangi', 'Jember', 'Kediri', 'Pasuruan', 'Mojokerto', 'Gresik',
            'Sidoarjo', 'Denpasar', 'Bogor', 'Padang', 'Pekanbaru', 'Balikpapan',
            'Samarinda', 'Pontianak', 'Manado', 'Cirebon', 'Sukabumi', 'Tasikmalaya'
        ];

        $addressLower = strtolower($address);
        
        // Cari kota yang disebutkan di alamat
        foreach ($cities as $city) {
            if (str_contains($addressLower, strtolower($city))) {
                return $city;
            }
        }

        if (preg_match('/kota\s+([^,\s]+)/i', $address, $matches)) {
            return ucfirst(trim($matches[1]));
        }

        if (preg_match('/kab\.?\s+([^,\s]+)/i', $address, $matches)) {
            return ucfirst(trim($matches[1]));
        }

        return 'Surabaya';
    }

    public function transactionsIndex(Request $request)
    {
        $filter = $request->input('filter', 'semua');
        $search = $request->input('search');

        if ($filter !== 'semua') {
            $now = Carbon::now();
            switch ($filter) {
                case 'hari':
                    $start = $now->copy()->startOfDay();
                    $end = $now->copy()->endOfDay();
                    break;
                case 'minggu':
                    $start = $now->copy()->startOfWeek();
                    $end = $now->copy()->endOfWeek();
                    break;
                case 'tahun':
                    $start = $now->copy()->startOfYear();
                    $end = $now->copy()->endOfYear();
                    break;
                case 'bulan':
                default:
                    $start = $now->copy()->startOfMonth();
                    $end = $now->copy()->endOfMonth();
                    break;
            }
        }

        $pendapatanQuery = \App\Models\HInvoice::query();
        if ($filter !== 'semua') {
            $pendapatanQuery->whereBetween('receive_date', [$start, $end]);
        }
        if ($search) {
            $pendapatanQuery->where('code', 'like', "%$search%");
        }
        $pendapatan = $pendapatanQuery->get();

        $pengeluaranQuery = DebtPayment::query();
        if ($filter !== 'semua') {
            $pengeluaranQuery->whereBetween('payment_date', [$start, $end]);
        }
        if ($search) {
            $pengeluaranQuery->whereHas('purchaseOrder', function($q) use ($search) {
                $q->where('code', 'like', "%$search%");
            });
        }
        $pengeluaran = $pengeluaranQuery->get();

        $hutangQuery = PurchaseOrder::query();
        if ($filter !== 'semua') {
            $hutangQuery->whereBetween('order_date', [$start, $end]);
        }
        if ($search) {
            $hutangQuery->where('code', 'like', "%$search%");
        }
        $hutang = $hutangQuery->get();

        // Payments data untuk view - menggunakan receive_date dengan pagination
        $paymentsQuery = \App\Models\PaymentModel::with(['hinvoice.customer']);
        if ($filter !== 'semua') {
            $paymentsQuery->whereHas('hinvoice', function($q) use ($start, $end) {
                $q->whereBetween('receive_date', [$start, $end]);
            });
        }
        if ($search) {
            $paymentsQuery->where(function($query) use ($search) {
                $query->whereHas('hinvoice', function($q) use ($search) {
                    $q->where('code', 'like', "%$search%");
                })->orWhereHas('hinvoice.customer', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            });
        }
        // Stats (global totals, not limited by pagination)
        $paymentsStatsQuery = clone $paymentsQuery;
        $totalPayments = (clone $paymentsStatsQuery)->count();
        $paidPayments = (clone $paymentsStatsQuery)->where('is_paid', true)->count();
        $unpaidPayments = $totalPayments - $paidPayments;
        $totalPaymentsAmount = (clone $paymentsStatsQuery)->sum('amount');

        // Paginated list
        $payments = $paymentsQuery->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('admin.kelola-transaksi.view', compact(
            'pendapatan', 'pengeluaran', 'hutang', 'payments', 'filter', 'search',
            'totalPayments', 'paidPayments', 'unpaidPayments', 'totalPaymentsAmount'
        ));
    }



    public function downloadLaporanTransaksi(Request $request)
    {
        $periode = ReportDateRange::fromRequest($request, 'bulanan');
        $start = $periode['start']->copy();
        $end = $periode['end']->copy();

        $pendapatan = HInvoice::with(['customer', 'payments'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('receive_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('receive_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->orderByRaw('COALESCE(receive_date, created_at) ASC')
            ->get();
        
        $pengeluaran = DebtPayment::with(['purchaseOrder.supplier'])
            ->whereBetween('payment_date', [$start, $end])
            ->orderBy('payment_date')
            ->get();
        
        $hutangPiutang = PurchaseOrder::with(['supplier', 'items'])
            ->whereBetween('order_date', [$start, $end])
            ->orderBy('order_date')
            ->get();
        
        $hutangCustomer = HInvoice::with(['customer', 'payments'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('receive_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('receive_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->whereHas('payments', function($q) {
                $q->where('method', 'hutang')->where('is_paid', 0);
            })
            ->orderByRaw('COALESCE(receive_date, created_at) ASC')
            ->get();

        $totalPendapatan = $pendapatan->sum('grand_total');
        $totalPengeluaran = $pengeluaran->sum('amount_paid');
        $totalHutang = $hutangPiutang->sum('total_amount');
        $totalHutangCustomer = $hutangCustomer->sum('grand_total');
        $laba = $totalPendapatan - $totalPengeluaran;

        $periodeLabel = $periode['label'];
        $periodeStart = $start;
        $periodeEnd = $end;

        $pdf = Pdf::loadView('exports.laporan-transaksi-owner', compact(
            'pendapatan', 'pengeluaran', 'hutangPiutang', 'hutangCustomer',
            'totalPendapatan', 'totalPengeluaran', 'totalHutang', 'totalHutangCustomer',
            'laba', 'periodeLabel', 'periodeStart', 'periodeEnd'
        ));

        return $pdf->download('laporan-transaksi-' . $periode['range'] . '-' . $end->format('Y-m-d') . '.pdf');
    }

    public function downloadLaporanPaymentGateway(Request $request)
    {
        $periode = ReportDateRange::fromRequest($request, 'bulanan');
        $start = $periode['start']->copy();
        $end = $periode['end']->copy();

        $transaksiBerhasil = HInvoice::with(['customer', 'payments'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('receive_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('receive_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->where('status', 'lunas')
            ->whereHas('payments', function($q) {
                $q->whereIn('method', ['midtrans', 'transfer', 'cash']);
            })
            ->orderByRaw('COALESCE(receive_date, created_at) ASC')
            ->get();

        $transaksiPending = HInvoice::with(['customer', 'payments'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('receive_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('receive_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->where('status', 'Menunggu Pembayaran')
            ->whereHas('payments', function($q) {
                $q->whereIn('method', ['midtrans', 'transfer', 'cash']);
            })
            ->orderByRaw('COALESCE(receive_date, created_at) ASC')
            ->get();

        $transaksiGagal = HInvoice::with(['customer', 'payments'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('receive_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('receive_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->where('status', '!=', 'lunas')
            ->where('status', '!=', 'Menunggu Pembayaran')
            ->whereHas('payments', function($q) {
                $q->whereIn('method', ['midtrans', 'transfer', 'cash']);
            })
            ->orderByRaw('COALESCE(receive_date, created_at) ASC')
            ->get();

        $totalPenjualan = $transaksiBerhasil->sum('grand_total');
        $totalTransaksi = $transaksiBerhasil->count() + $transaksiPending->count() + $transaksiGagal->count();

        $periodeLabel = $periode['label'];
        $periodeStart = $start;
        $periodeEnd = $end;

        $pdf = Pdf::loadView('exports.laporan-payment-gateway', compact(
            'transaksiBerhasil', 'transaksiPending', 'transaksiGagal',
            'totalPenjualan', 'totalTransaksi', 'periodeLabel', 'periodeStart', 'periodeEnd'
        ));

        return $pdf->download('laporan-payment-gateway-' . $periode['range'] . '-' . $end->format('Y-m-d') . '.pdf');
    }

    public function downloadLaporanNegosiasi(Request $request)
    {
        $periode = ReportDateRange::fromRequest($request, 'bulanan');
        $start = $periode['start']->copy();
        $end = $periode['end']->copy();

        $negosiasiBerhasil = NegotiationTable::with(['customer', 'product'])
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'disetujui')
            ->orderBy('created_at')
            ->get();

        $negosiasiGagal = NegotiationTable::with(['customer', 'product'])
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'ditolak')
            ->orderBy('created_at')
            ->get();

        $negosiasiPending = NegotiationTable::with(['customer', 'product'])
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $totalNegosiasi = $negosiasiBerhasil->count() + $negosiasiGagal->count() + $negosiasiPending->count();
        $persentaseBerhasil = $totalNegosiasi > 0 ? ($negosiasiBerhasil->count() / $totalNegosiasi) * 100 : 0;

        $periodeLabel = $periode['label'];
        $periodeStart = $start;
        $periodeEnd = $end;

        $pdf = Pdf::loadView('exports.laporan-negosiasi', compact(
            'negosiasiBerhasil', 'negosiasiGagal', 'negosiasiPending',
            'totalNegosiasi', 'persentaseBerhasil', 'periodeLabel', 'periodeStart', 'periodeEnd'
        ));

        return $pdf->download('laporan-negosiasi-' . $periode['range'] . '-' . $end->format('Y-m-d') . '.pdf');
    }

    public function downloadLaporanDriver(Request $request)
    {
        $periode = ReportDateRange::fromRequest($request, 'bulanan');
        $start = $periode['start']->copy();
        $end = $periode['end']->copy();

        $deliveryStatuses = ['dikirim', 'sampai', 'completed'];
        $returnStatuses = ['retur_diajukan', 'retur_diambil', 'retur_selesai'];

        $tasks = HInvoice::with(['customer', 'driver'])
            ->whereNotNull('driver_id')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('receive_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('receive_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->orderByRaw('COALESCE(receive_date, created_at) ASC')
            ->get();

        $driverSummaries = $tasks->groupBy('driver_id')->map(function ($group) use ($deliveryStatuses, $returnStatuses) {
            $driver = $group->first()->driver;
            $deliveryTasks = $group->filter(fn($task) => in_array(strtolower($task->status), $deliveryStatuses));
            $returnTasks = $group->filter(fn($task) => in_array(strtolower($task->status), $returnStatuses));

            return [
                'driver' => $driver,
                'total_tasks' => $group->count(),
                'delivery_total' => $deliveryTasks->count(),
                'delivery_completed' => $deliveryTasks->filter(fn($task) => strtolower($task->status) === 'sampai' || strtolower($task->status) === 'completed')->count(),
                'return_total' => $returnTasks->count(),
                'return_completed' => $returnTasks->filter(fn($task) => strtolower($task->status) === 'retur_selesai')->count(),
            ];
        })->values();

        $totalTasks = $tasks->count();
        $totalDeliveryTasks = $tasks->filter(fn($task) => in_array(strtolower($task->status), $deliveryStatuses))->count();
        $totalReturnTasks = $tasks->filter(fn($task) => in_array(strtolower($task->status), $returnStatuses))->count();
        $totalCompletedTasks = $tasks->filter(fn($task) => in_array(strtolower($task->status), ['sampai', 'completed', 'retur_selesai']))->count();

        $periodeLabel = $periode['label'];
        $periodeStart = $start;
        $periodeEnd = $end;

        $pdf = Pdf::loadView('exports.laporan-driver-owner', [
            'tasks' => $tasks,
            'driverSummaries' => $driverSummaries,
            'totalTasks' => $totalTasks,
            'totalDeliveryTasks' => $totalDeliveryTasks,
            'totalReturnTasks' => $totalReturnTasks,
            'totalCompletedTasks' => $totalCompletedTasks,
            'periodeLabel' => $periodeLabel,
            'periodeStart' => $periodeStart,
            'periodeEnd' => $periodeEnd,
        ]);

        return $pdf->download('laporan-driver-' . $periode['range'] . '-' . $end->format('Y-m-d') . '.pdf');
    }
}
