<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.biteship.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.biteship.api_key');
    }

    /**
     * Get list of areas (cities/subdistricts)
     * 
     * @param string $input Search query (city name, postal code, etc)
     * @param string $type Type: 'single' for single result, 'multiple' for multiple results
     * @return array
     */
    public function getAreas($input, $type = 'single')
    {
        try {
            // Biteship menggunakan format authorization langsung dengan token (tanpa Bearer)
            $response = Http::withHeaders([
                'authorization' => $this->apiKey,
                'content-type' => 'application/json'
            ])->get($this->baseUrl . '/maps/areas', [
                'countries' => 'ID',
                'input' => $input,
                'type' => $type
            ]);
            
            // Log response untuk debugging
            if (!$response->successful()) {
                Log::error('Biteship getAreas failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'input' => $input,
                    'api_key_prefix' => substr($this->apiKey, 0, 20) . '...'
                ]);
            }

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['areas']) && !empty($data['areas'])) {
                    return $data['areas'];
                }
            }
            return [];
        } catch (\Exception $e) {
            Log::error('Biteship getAreas error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search area by city name
     * 
     * @param string $cityName City name to search
     * @return array|null
     */
    public function searchArea($cityName)
    {
        $areas = $this->getAreas($cityName, 'multiple');
        
        if (empty($areas)) {
            Log::warning('Biteship searchArea: No areas found', [
                'searched_city' => $cityName
            ]);
            return null;
        }

        // Normalize city name
        $cityNameLower = strtolower(trim($cityName));
        $normalizedCityName = preg_replace('/\b(kabupaten|kota|kec\.?)\s*/i', '', $cityNameLower);
        $normalizedCityName = trim($normalizedCityName);

        // Try to find exact match first
        foreach ($areas as $area) {
            $areaName = strtolower($area['name'] ?? '');
            $areaCity = strtolower($area['city'] ?? '');
            
            if ($areaName === $normalizedCityName || 
                $areaCity === $normalizedCityName ||
                $areaName === $cityNameLower ||
                $areaCity === $cityNameLower) {
                Log::info('Biteship searchArea: Exact match found', [
                    'searched' => $cityName,
                    'matched_area' => $area
                ]);
                return $area;
            }
        }

        // Try partial match
        foreach ($areas as $area) {
            $areaName = strtolower($area['name'] ?? '');
            $areaCity = strtolower($area['city'] ?? '');
            
            if (str_contains($areaName, $normalizedCityName) || 
                str_contains($areaCity, $normalizedCityName) ||
                str_contains($areaName, $cityNameLower) ||
                str_contains($areaCity, $cityNameLower)) {
                Log::info('Biteship searchArea: Partial match found', [
                    'searched' => $cityName,
                    'matched_area' => $area
                ]);
                return $area;
            }
        }

        // Return first result if no match found
        $firstArea = $areas[0] ?? null;
        if ($firstArea) {
            Log::info('Biteship searchArea: Using first result', [
                'searched' => $cityName,
                'returned_area' => $firstArea
            ]);
        }
        return $firstArea;
    }

    /**
     * Calculate shipping rates
     * 
     * @param string $originAreaId Origin area ID
     * @param string $destinationAreaId Destination area ID
     * @param int $weight Weight in grams
     * @param array $couriers List of courier codes (jne, pos, tiki, sicepat, jnt, anteraja, ninja, etc)
     * @return array
     */
    public function getRates($originAreaId, $destinationAreaId, $weight, $couriers = ['jne', 'pos', 'tiki'])
    {
        try {
            // Validasi area ID sebelum request
            if (empty($originAreaId) || empty($destinationAreaId)) {
                Log::error('Biteship getRates: Invalid area IDs', [
                    'origin_area_id' => $originAreaId,
                    'destination_area_id' => $destinationAreaId
                ]);
                return [];
            }
            
            // Validasi weight minimal
            if ($weight < 1) {
                Log::error('Biteship getRates: Invalid weight', [
                    'weight' => $weight
                ]);
                return [];
            }
            
            // Biteship menerima couriers sebagai array
            // Pastikan couriers adalah array
            $couriersArray = is_array($couriers) ? $couriers : explode(',', $couriers);
            // Filter couriers yang valid (hapus yang kosong)
            $couriersArray = array_filter(array_map('trim', $couriersArray));
            
            if (empty($couriersArray)) {
                Log::error('Biteship getRates: No valid couriers provided');
                return [];
            }
            
            $requestPayload = [
                'origin_area_id' => $originAreaId,
                'destination_area_id' => $destinationAreaId,
                'items' => [
                    [
                        'name' => 'Item',
                        'description' => 'Item',
                        'value' => 0,
                        'length' => 10,
                        'width' => 10,
                        'height' => 10,
                        'weight' => $weight
                    ]
                ],
                'couriers' => $couriersArray
            ];
            
            Log::info('Biteship getRates request', [
                'api_key_prefix' => substr($this->apiKey, 0, 20) . '...',
                'base_url' => $this->baseUrl,
                'origin_area_id' => $originAreaId,
                'destination_area_id' => $destinationAreaId,
                'weight' => $weight,
                'couriers' => $couriersArray
            ]);
            
            $response = Http::withHeaders([
                'authorization' => $this->apiKey,
                'content-type' => 'application/json'
            ])->post($this->baseUrl . '/rates/couriers', $requestPayload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Log detail response untuk debugging
                Log::info('Biteship getRates response', [
                    'has_pricing' => isset($data['pricing']),
                    'pricing_count' => isset($data['pricing']) ? count($data['pricing']) : 0,
                    'response_keys' => array_keys($data ?? []),
                    'pricing_sample' => isset($data['pricing']) && !empty($data['pricing']) ? $data['pricing'][0] : null
                ]);
                
                if (isset($data['pricing']) && !empty($data['pricing'])) {
                    // Log detail setiap pricing untuk debugging
                    foreach ($data['pricing'] as $index => $pricing) {
                        Log::info("Biteship pricing #{$index}", [
                            'courier_code' => $pricing['courier_code'] ?? null,
                            'courier_name' => $pricing['courier_name'] ?? null,
                            'courier_service_name' => $pricing['courier_service_name'] ?? null,
                            'courier_service_type' => $pricing['courier_service_type'] ?? null,
                            'price' => $pricing['price'] ?? null,
                            'duration' => $pricing['duration'] ?? null,
                            'available_keys' => array_keys($pricing ?? [])
                        ]);
                    }
                    
                    return $data['pricing'];
                }
                
                // Log jika pricing kosong
                Log::warning('Biteship pricing is empty', [
                    'response_data' => $data
                ]);
            } else {
                $errorBody = $response->json();
                $statusCode = $response->status();
                
                Log::error('Biteship getRates failed', [
                    'status' => $statusCode,
                    'body' => $response->body(),
                    'error_json' => $errorBody,
                    'origin_area_id' => $originAreaId,
                    'destination_area_id' => $destinationAreaId,
                    'weight' => $weight,
                    'couriers' => $couriers,
                    'couriers_array' => $couriersArray,
                    'api_key_type' => str_starts_with($this->apiKey, 'biteship_test') ? 'testing' : 'production',
                    'api_key_prefix' => substr($this->apiKey, 0, 20) . '...'
                ]);
                
                // Handle specific error codes
                if ($statusCode === 401) {
                    Log::error('Biteship API: Unauthorized - API key mungkin tidak valid atau expired');
                } elseif ($statusCode === 400) {
                    Log::error('Biteship API: Bad Request - Format request atau parameter tidak valid', [
                        'error_details' => $errorBody
                    ]);
                } elseif ($statusCode === 403) {
                    Log::error('Biteship API: Forbidden - API key tidak memiliki permission');
                } elseif ($statusCode === 404) {
                    Log::error('Biteship API: Not Found - Endpoint atau resource tidak ditemukan');
                }
                
                // Jika error "No sufficient balance" dan menggunakan testing key, log warning khusus
                if (isset($errorBody['error']) && 
                    str_contains(strtolower($errorBody['error']), 'balance') && 
                    str_starts_with($this->apiKey, 'biteship_test')) {
                    Log::warning('Biteship testing mode still requires balance. Please check dashboard or contact support.');
                }
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('Biteship getRates error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get rates from multiple couriers (formatted for frontend compatibility)
     * 
     * @param string $originAreaId Origin area ID
     * @param string $destinationAreaId Destination area ID
     * @param int $weight Weight in grams
     * @param array $couriers List of courier codes
     * @return array
     */
    public function getRatesMultipleCouriers($originAreaId, $destinationAreaId, $weight, $couriers = ['jne', 'pos', 'tiki'])
    {
        $rates = $this->getRates($originAreaId, $destinationAreaId, $weight, $couriers);
        
        if (empty($rates)) {
            return [];
        }
        
        // Group rates by courier
        $groupedRates = [];
        foreach ($rates as $rate) {
            $courierCode = strtolower($rate['courier_code'] ?? 'unknown');
            $courierName = $rate['courier_name'] ?? strtoupper($courierCode);
            
            // Log untuk debugging
            Log::info('Processing rate', [
                'courier_code' => $courierCode,
                'courier_name' => $courierName,
                'service_name' => $rate['courier_service_name'] ?? null,
                'price' => $rate['price'] ?? null,
                'rate_keys' => array_keys($rate ?? [])
            ]);
            
            if (!isset($groupedRates[$courierCode])) {
                $groupedRates[$courierCode] = [
                    'courier' => $courierCode,
                    'courier_name' => $courierName,
                    'services' => []
                ];
            }
            
            // Pastikan service name unik (jika ada duplikat, tambahkan identifier)
            $serviceName = $rate['courier_service_name'] ?? $rate['courier_service_type'] ?? 'Standard';
            $serviceType = $rate['courier_service_type'] ?? '';
            $serviceCode = $rate['courier_service_code'] ?? strtolower($serviceName); // Gunakan courier_service_code jika ada
            $price = $rate['price'] ?? 0;
            $duration = $rate['duration'] ?? '-';
            
            $groupedRates[$courierCode]['services'][] = [
                'service' => $serviceName,
                'service_code' => $serviceCode, // Tambahkan service_code untuk createOrder
                'description' => $serviceType,
                'cost' => [
                    [
                        'value' => $price,
                        'etd' => $duration
                    ]
                ]
            ];
        }
        
        // Log hasil grouping
        Log::info('Grouped rates result', [
            'total_couriers' => count($groupedRates),
            'couriers' => array_keys($groupedRates),
            'services_per_courier' => array_map(function($courier) {
                return count($courier['services']);
            }, $groupedRates)
        ]);
        
        return array_values($groupedRates);
    }

    /**
     * Create order/pengiriman di Biteship
     * 
     * @param array $orderData Data order yang berisi:
     *   - origin_contact_name: Nama pengirim
     *   - origin_contact_phone: Telepon pengirim
     *   - origin_address: Alamat pengirim
     *   - origin_area_id: Area ID pengirim
     *   - destination_contact_name: Nama penerima
     *   - destination_contact_phone: Telepon penerima
     *   - destination_address: Alamat penerima
     *   - destination_area_id: Area ID penerima
     *   - courier_company: Kode kurir (jne, pos, tiki, dll)
     *   - courier_type: Tipe layanan kurir
     *   - courier_insurance: Nilai asuransi (optional)
     *   - delivery_type: Tipe pengiriman (now, schedule, later)
     *   - items: Array items yang dikirim
     * @return array|null Mengembalikan data order dengan waybill ID atau null jika gagal
     */
    public function createOrder($orderData)
    {
        try {
            Log::info('Biteship createOrder request', [
                'order_data' => $orderData
            ]);

            $payload = [
                'origin_contact_name' => $orderData['origin_contact_name'] ?? 'Pengirim',
                'origin_contact_phone' => $orderData['origin_contact_phone'] ?? '',
                'origin_address' => $orderData['origin_address'] ?? '',
                'origin_area_id' => $orderData['origin_area_id'] ?? '',
                'destination_contact_name' => $orderData['destination_contact_name'] ?? '',
                'destination_contact_phone' => $orderData['destination_contact_phone'] ?? '',
                'destination_address' => $orderData['destination_address'] ?? '',
                'destination_area_id' => $orderData['destination_area_id'] ?? '',
                'courier_company' => $orderData['courier_company'] ?? 'jne',
                'courier_type' => $orderData['courier_type'] ?? '',
                'delivery_type' => $orderData['delivery_type'] ?? 'later',
                'items' => $orderData['items'] ?? []
            ];

            // Tambahkan courier_insurance jika ada
            if (isset($orderData['courier_insurance']) && $orderData['courier_insurance'] > 0) {
                $payload['courier_insurance'] = $orderData['courier_insurance'];
            }

            // Tambahkan delivery_date jika delivery_type adalah schedule
            if (isset($orderData['delivery_date']) && $orderData['delivery_type'] === 'schedule') {
                $payload['delivery_date'] = $orderData['delivery_date'];
            }

            $response = Http::withHeaders([
                'authorization' => $this->apiKey,
                'content-type' => 'application/json'
            ])->post($this->baseUrl . '/orders', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Log full response untuk debugging
                Log::info('Biteship createOrder success - Full Response', [
                    'full_response' => $data,
                    'response_keys' => array_keys($data ?? []),
                    'order_id' => $data['id'] ?? null,
                    'waybill_id' => $data['waybill_id'] ?? null,
                    'waybill_number' => $data['waybill_number'] ?? null,
                    'tracking_id' => $data['tracking_id'] ?? null,
                    'tracking_number' => $data['tracking_number'] ?? null,
                    'status' => $data['status'] ?? null
                ]);

                // Cek berbagai kemungkinan field untuk waybill ID
                // Struktur response: courier.waybill_id atau courier.tracking_id
                $waybillId = $data['courier']['waybill_id'] ?? 
                            $data['courier']['tracking_id'] ?? 
                            $data['waybill_id'] ?? 
                            $data['waybill_number'] ?? 
                            $data['tracking_id'] ?? 
                            $data['tracking_number'] ?? 
                            null;

                if ($waybillId) {
                    $sourceField = isset($data['courier']['waybill_id']) ? 'courier.waybill_id' : 
                                  (isset($data['courier']['tracking_id']) ? 'courier.tracking_id' : 
                                  (isset($data['waybill_id']) ? 'waybill_id' : 
                                  (isset($data['waybill_number']) ? 'waybill_number' : 
                                  (isset($data['tracking_id']) ? 'tracking_id' : 'tracking_number'))));
                    
                    Log::info('Biteship waybill ID found', [
                        'waybill_id' => $waybillId,
                        'source_field' => $sourceField
                    ]);
                } else {
                    Log::warning('Biteship waybill ID not found in response', [
                        'available_fields' => array_keys($data ?? []),
                        'courier_fields' => isset($data['courier']) ? array_keys($data['courier']) : null,
                        'note' => 'Waybill ID mungkin akan tersedia setelah order diproses atau status berubah'
                    ]);
                }

                return $data;
            } else {
                $errorBody = $response->json();
                Log::error('Biteship createOrder failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'error_json' => $errorBody
                ]);

                return null;
            }
        } catch (\Exception $e) {
            Log::error('Biteship createOrder error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Extract waybill ID from order response
     * 
     * @param array $orderResponse Response dari createOrder atau getOrder
     * @return string|null
     */
    public function extractWaybillId($orderResponse)
    {
        if (!$orderResponse || !is_array($orderResponse)) {
            return null;
        }

        // Cek berbagai kemungkinan field untuk waybill ID
        // Prioritas: courier.waybill_id > courier.tracking_id > root level fields
        return $orderResponse['courier']['waybill_id'] ?? 
               $orderResponse['courier']['tracking_id'] ?? 
               $orderResponse['waybill_id'] ?? 
               $orderResponse['waybill_number'] ?? 
               $orderResponse['tracking_id'] ?? 
               $orderResponse['tracking_number'] ?? 
               null;
    }

    /**
     * Get waybill ID dengan polling jika belum tersedia
     * 
     * @param string $orderId Biteship order ID
     * @param int $maxAttempts Maksimal percobaan polling
     * @param int $delay Delay antar percobaan (detik)
     * @return string|null
     */
    public function getWaybillIdWithPolling($orderId, $maxAttempts = 5, $delay = 2)
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            $order = $this->getOrder($orderId);
            
            if ($order) {
                $waybillId = $this->extractWaybillId($order);
                
                if ($waybillId) {
                    Log::info('Biteship waybill ID obtained via polling', [
                        'order_id' => $orderId,
                        'waybill_id' => $waybillId,
                        'attempt' => $i + 1
                    ]);
                    return $waybillId;
                }
            }
            
            // Tunggu sebelum coba lagi (kecuali percobaan terakhir)
            if ($i < $maxAttempts - 1) {
                sleep($delay);
            }
        }
        
        Log::warning('Biteship waybill ID not found after polling', [
            'order_id' => $orderId,
            'attempts' => $maxAttempts
        ]);
        
        return null;
    }

    /**
     * Get order details by order ID
     * 
     * @param string $orderId Biteship order ID
     * @return array|null
     */
    public function getOrder($orderId)
    {
        try {
            $response = Http::withHeaders([
                'authorization' => $this->apiKey,
                'content-type' => 'application/json'
            ])->get($this->baseUrl . '/orders/' . $orderId);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Biteship getOrder success', [
                    'order_id' => $orderId,
                    'waybill_id' => $data['waybill_id'] ?? null,
                    'status' => $data['status'] ?? null
                ]);

                return $data;
            } else {
                Log::error('Biteship getOrder failed', [
                    'order_id' => $orderId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return null;
            }
        } catch (\Exception $e) {
            Log::error('Biteship getOrder error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Track order by waybill ID
     * 
     * @param string $waybillId Waybill ID untuk tracking
     * @param string $courierCode Kode kurir (jne, pos, tiki, dll)
     * @return array|null
     */
    public function trackOrder($waybillId, $courierCode)
    {
        try {
            $response = Http::withHeaders([
                'authorization' => $this->apiKey,
                'content-type' => 'application/json'
            ])->get($this->baseUrl . '/trackings/' . $waybillId . '/' . $courierCode);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Biteship trackOrder success', [
                    'waybill_id' => $waybillId,
                    'courier_code' => $courierCode,
                    'status' => $data['status'] ?? null
                ]);

                return $data;
            } else {
                Log::error('Biteship trackOrder failed', [
                    'waybill_id' => $waybillId,
                    'courier_code' => $courierCode,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return null;
            }
        } catch (\Exception $e) {
            Log::error('Biteship trackOrder error: ' . $e->getMessage());
            return null;
        }
    }
}
