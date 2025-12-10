<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BiteshipService;
use Illuminate\Support\Facades\Session;

class ShippingController extends Controller
{
    protected $biteship;

    public function __construct(BiteshipService $biteship)
    {
        $this->biteship = $biteship;
    }

    /**
     * Get shipping cost via AJAX
     */
    public function getShippingCost(Request $request)
    {
        \Log::info('=== ShippingController getShippingCost CALLED ===', [
            'request_data' => $request->all(),
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);
        
        if ($request->has('test') || $request->input('test') === '1') {
            return response()->json(['status' => 'ok', 'message' => 'Controller is accessible', 'test' => true]);
        }
        
        try {
            $request->validate([
                'destination_city' => 'required|string',
                'weight' => 'required|integer|min:1'
            ]);

            $weight = max($request->weight, 1000); // Minimal 1 kg
            $originCity = 'Surabaya';
            $destinationCity = $request->destination_city;
            
            $originArea = $this->biteship->searchArea($originCity);
            if (!$originArea) {
                \Log::error('Origin area (Surabaya) not found in Biteship');
                return response()->json([
                    'success' => false,
                    'message' => 'Kota asal (Surabaya) tidak ditemukan. Silakan hubungi administrator.'
                ], 200);
            }
            $originAreaId = $originArea['id'];
            
            $destinationArea = $this->biteship->searchArea($destinationCity);
            if (!$destinationArea) {
                \Log::warning('Destination area not found in Biteship', [
                    'searched_city' => $destinationCity,
                    'weight' => $weight
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Kota tujuan tidak ditemukan. Pastikan alamat pengiriman mencantumkan nama kota/kabupaten (contoh: Banyuwangi, Surabaya, Jakarta).'
                ], 200);
            }

            $destinationAreaId = $destinationArea['id'] ?? null;
            
            // Validasi area ID
            if (!$destinationAreaId) {
                \Log::error('Destination area ID is missing', [
                    'destination_area' => $destinationArea,
                    'searched_city' => $destinationCity,
                    'area_keys' => array_keys($destinationArea ?? [])
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Area ID tidak valid. Silakan coba lagi atau hubungi customer service.'
                ], 200);
            }
            
            // Pastikan area ID adalah string
            $destinationAreaId = (string) $destinationAreaId;
            $originAreaId = (string) $originAreaId;
            
            // Log area IDs untuk debugging
            \Log::info('Area IDs validated', [
                'origin_area_id' => $originAreaId,
                'origin_area_id_type' => gettype($originAreaId),
                'destination_area_id' => $destinationAreaId,
                'destination_area_id_type' => gettype($destinationAreaId),
                'destination_area_full' => $destinationArea
            ]);
            
            \Log::info('Biteship area IDs', [
                'origin_area_id' => $originAreaId,
                'destination_area_id' => $destinationAreaId,
                'weight' => $weight
            ]);

            $couriers = ['jne', 'pos', 'tiki'];
            $results = $this->biteship->getRatesMultipleCouriers(
                $originAreaId,
                $destinationAreaId,
                $weight,
                $couriers
            );
            
            if (empty($results)) {
                $alternativeCouriers = ['sicepat', 'jnt', 'anteraja', 'ninja', 'lion', 'wahana'];
                $results = $this->biteship->getRatesMultipleCouriers(
                    $originAreaId,
                    $destinationAreaId,
                    $weight,
                    $alternativeCouriers
                );
            }

            if (empty($results)) {
                \Log::error('No shipping rates available from both APIs', [
                    'destination_city' => $destinationCity,
                    'weight' => $weight
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada layanan pengiriman tersedia untuk tujuan ini. Silakan hubungi customer service untuk informasi ongkir.'
                ], 200);
            }

            return response()->json([
                'success' => true,
                'destination_city' => ($destinationArea['city'] ?? '') . ($destinationArea['name'] ? ', ' . $destinationArea['name'] : ''),
                'couriers' => $results
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors()['destination_city'] ?? [])
            ], 422);
        } catch (\Exception $e) {
            \Log::error('ShippingController getShippingCost error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            // Jika error terkait API, tampilkan pesan yang lebih jelas
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, 'Biteship')) {
                $errorMessage = 'Layanan cek ongkir sedang tidak tersedia. Silakan hubungi customer service untuk informasi ongkir.';
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
    }

    /**
     * Search city by name
     */
    public function searchCity(Request $request)
    {
        $request->validate([
            'city_name' => 'required|string|min:3'
        ]);

        $area = $this->biteship->searchArea($request->city_name);

        if (!$area) {
            return response()->json([
                'success' => false,
                'message' => 'Kota tidak ditemukan'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'area' => $area
        ]);
    }
}

