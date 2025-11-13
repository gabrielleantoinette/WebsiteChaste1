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
            $response = Http::withHeaders([
                'authorization' => $this->apiKey,
                'content-type' => 'application/json'
            ])->get($this->baseUrl . '/maps/areas', [
                'countries' => 'ID',
                'input' => $input,
                'type' => $type
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['areas']) && !empty($data['areas'])) {
                    return $data['areas'];
                }
            } else {
                Log::error('Biteship getAreas failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'input' => $input
                ]);
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
            // Log API key yang digunakan (hanya 20 karakter pertama untuk security)
            Log::info('Biteship getRates request', [
                'api_key_prefix' => substr($this->apiKey, 0, 20) . '...',
                'base_url' => $this->baseUrl,
                'origin_area_id' => $originAreaId,
                'destination_area_id' => $destinationAreaId,
                'weight' => $weight,
                'couriers' => $couriers
            ]);
            
            // Biteship menerima couriers sebagai string (comma-separated) atau array
            // Tapi lebih aman pakai string untuk menghindari error
            $couriersString = is_array($couriers) ? implode(',', $couriers) : $couriers;
            
            $response = Http::withHeaders([
                'authorization' => $this->apiKey,
                'content-type' => 'application/json'
            ])->post($this->baseUrl . '/rates/couriers', [
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
                'couriers' => $couriersString
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Biteship getRates response', [
                    'has_pricing' => isset($data['pricing']),
                    'pricing_count' => isset($data['pricing']) ? count($data['pricing']) : 0,
                    'response_keys' => array_keys($data ?? [])
                ]);
                
                if (isset($data['pricing']) && !empty($data['pricing'])) {
                    return $data['pricing'];
                }
                
                // Log jika pricing kosong
                Log::warning('Biteship pricing is empty', [
                    'response_data' => $data
                ]);
            } else {
                $errorBody = $response->json();
                Log::error('Biteship getRates failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'error_json' => $errorBody,
                    'origin_area_id' => $originAreaId,
                    'destination_area_id' => $destinationAreaId,
                    'weight' => $weight,
                    'couriers' => $couriers,
                    'api_key_type' => str_starts_with($this->apiKey, 'biteship_test') ? 'testing' : 'production'
                ]);
                
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
            
            if (!isset($groupedRates[$courierCode])) {
                $groupedRates[$courierCode] = [
                    'courier' => $courierCode,
                    'courier_name' => $courierName,
                    'services' => []
                ];
            }
            
            $groupedRates[$courierCode]['services'][] = [
                'service' => $rate['courier_service_name'] ?? 'Standard',
                'description' => $rate['courier_service_type'] ?? '',
                'cost' => [
                    [
                        'value' => $rate['price'] ?? 0,
                        'etd' => $rate['duration'] ?? '-'
                    ]
                ]
            ];
        }
        
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
                Log::info('Biteship createOrder success', [
                    'order_id' => $data['id'] ?? null,
                    'waybill_id' => $data['waybill_id'] ?? null,
                    'status' => $data['status'] ?? null
                ]);

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
