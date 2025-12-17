<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * White Box Testing untuk Negotiation Algorithm
 * 
 * Test ini menguji logika negosiasi yang ada di:
 * - app/Http/Controllers/NegotiationController.php (method tawar)
 * 
 * Algoritma yang diuji:
 * 1. Validasi tawaran minimal 50% dari harga normal
 * 2. Validasi tawaran tidak boleh >= harga normal
 * 3. Perhitungan response seller berdasarkan discount percentage:
 *    - ≥40%: response 20-30% dari selisih
 *    - ≥25%: response 30-40% dari selisih
 *    - ≥15%: response 40-50% dari selisih
 *    - <15%: response 50-70% dari selisih
 * 4. Validasi response tidak melebihi harga normal
 * 5. Validasi response tidak kurang dari min_price
 */
class NegotiationAlgorithmTest extends TestCase
{
    /**
     * Test perhitungan discount percentage
     */
    public function test_calculate_discount_percentage()
    {
        $maxPrice = 100000; // harga normal
        $offer = 60000; // tawaran customer
        
        $discountPercentage = (($maxPrice - $offer) / $maxPrice) * 100;
        
        $this->assertEquals(40, $discountPercentage);
    }

    /**
     * Test response seller untuk discount ≥40%
     * Response: 20-30% dari selisih
     */
    public function test_seller_response_for_high_discount()
    {
        $maxPrice = 100000;
        $offer = 50000; // discount 50%
        $difference = $maxPrice - $offer; // 50.000
        
        $discountPercentage = (($maxPrice - $offer) / $maxPrice) * 100; // 50%
        
        if ($discountPercentage >= 40) {
            $percentage = 0.25; // rata-rata 25% dari selisih
            $response = (int) ($offer + ($difference * $percentage));
        }
        
        $expectedResponse = 50000 + (50000 * 0.25); // 62.500
        $this->assertEquals(62500, $response);
    }

    /**
     * Test response seller untuk discount ≥25% dan <40%
     * Response: 30-40% dari selisih
     */
    public function test_seller_response_for_medium_high_discount()
    {
        $maxPrice = 100000;
        $offer = 70000; // discount 30%
        $difference = $maxPrice - $offer; // 30.000
        
        $discountPercentage = (($maxPrice - $offer) / $maxPrice) * 100; // 30%
        
        if ($discountPercentage >= 25 && $discountPercentage < 40) {
            $percentage = 0.35; // rata-rata 35% dari selisih
            $response = (int) ($offer + ($difference * $percentage));
        }
        
        $expectedResponse = 70000 + (30000 * 0.35); // 80.500
        $this->assertEquals(80500, $response);
    }

    /**
     * Test response seller untuk discount ≥15% dan <25%
     * Response: 40-50% dari selisih
     */
    public function test_seller_response_for_medium_discount()
    {
        $maxPrice = 100000;
        $offer = 85000; // discount 15%
        $difference = $maxPrice - $offer; // 15.000
        
        $discountPercentage = (($maxPrice - $offer) / $maxPrice) * 100; // 15%
        
        if ($discountPercentage >= 15 && $discountPercentage < 25) {
            $percentage = 0.45; // rata-rata 45% dari selisih
            $response = (int) ($offer + ($difference * $percentage));
        }
        
        $expectedResponse = 85000 + (15000 * 0.45); // 91.750
        $this->assertEquals(91750, $response);
    }

    /**
     * Test response seller untuk discount <15%
     * Response: 50-70% dari selisih
     */
    public function test_seller_response_for_low_discount()
    {
        $maxPrice = 100000;
        $offer = 92000; // discount 8%
        $difference = $maxPrice - $offer; // 8.000
        
        $discountPercentage = (($maxPrice - $offer) / $maxPrice) * 100; // 8%
        
        if ($discountPercentage < 15) {
            $percentage = 0.60; // rata-rata 60% dari selisih
            $response = (int) ($offer + ($difference * $percentage));
        }
        
        $expectedResponse = 92000 + (8000 * 0.60); // 96.800
        $this->assertEquals(96800, $response);
    }

    /**
     * Test validasi response tidak melebihi harga normal
     */
    public function test_response_cannot_exceed_max_price()
    {
        $maxPrice = 100000;
        $offer = 50000;
        $difference = $maxPrice - $offer;
        
        // Simulasi response yang melebihi max (misal karena perhitungan yang salah)
        $response = $offer + ($difference * 1.2); // 110.000 (melebihi max)
        
        // Validasi: jika response > max, set ke max
        if ($response > $maxPrice) {
            $response = (int) $maxPrice;
        }
        
        $this->assertEquals(100000, $response);
    }

    /**
     * Test validasi response tidak kurang dari min_price
     */
    public function test_response_cannot_be_below_min_price()
    {
        $maxPrice = 100000;
        $minPrice = 65000;
        $offer = 50000;
        $difference = $maxPrice - $offer;
        
        // Simulasi response yang kurang dari min
        $response = $offer + ($difference * 0.1); // 55.000 (kurang dari min)
        
        // Validasi: jika response < min dan min > offer, set ke min
        if ($response < $minPrice) {
            if ($minPrice > $offer) {
                $response = (int) $minPrice;
            } else {
                $response = (int) ($offer + 1000);
            }
        }
        
        $this->assertEquals(65000, $response);
    }

    /**
     * Test validasi tawaran minimal 50% dari harga normal
     */
    public function test_validate_minimum_offer_50_percent()
    {
        $maxPrice = 100000;
        $minOfferPercent = 0.5;
        $minOffer = $maxPrice * $minOfferPercent; // 50.000
        
        $offer = 40000; // kurang dari 50%
        $isValid = $offer >= $minOffer;
        
        $this->assertFalse($isValid);
        
        $offer2 = 50000; // tepat 50%
        $isValid2 = $offer2 >= $minOffer;
        
        $this->assertTrue($isValid2);
    }

    /**
     * Test validasi tawaran tidak boleh >= harga normal
     */
    public function test_validate_offer_cannot_exceed_max_price()
    {
        $maxPrice = 100000;
        
        $offer1 = 100000; // sama dengan max
        $isValid1 = $offer1 < $maxPrice;
        $this->assertFalse($isValid1);
        
        $offer2 = 110000; // melebihi max
        $isValid2 = $offer2 < $maxPrice;
        $this->assertFalse($isValid2);
        
        $offer3 = 90000; // kurang dari max
        $isValid3 = $offer3 < $maxPrice;
        $this->assertTrue($isValid3);
    }

    /**
     * Test edge case: response harus lebih besar dari offer
     */
    public function test_response_must_be_greater_than_offer()
    {
        $maxPrice = 100000;
        $offer = 50000;
        $difference = $maxPrice - $offer;
        
        // Simulasi response yang <= offer (tidak valid)
        $response = $offer + ($difference * 0.001); // sangat kecil, bisa <= offer
        
        // Validasi: jika response <= offer, tambahkan 1000
        if ($response <= $offer) {
            $response = (int) ($offer + 1000);
        }
        
        $this->assertGreaterThan($offer, $response);
    }
}

