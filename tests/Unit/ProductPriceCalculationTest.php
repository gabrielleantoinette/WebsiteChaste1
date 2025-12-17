<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPriceCalculationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test perhitungan harga berdasarkan ukuran dengan size_prices yang sudah diset
     */
    public function test_get_price_for_size_with_custom_size_prices()
    {
        $product = Product::create([
            'name' => 'Terpal Test',
            'price' => 100000, // harga dasar 2x3
            'size_prices' => [
                '2x3' => 100000,
                '3x4' => 180000,
                '4x6' => 350000,
                '6x8' => 700000,
            ],
        ]);

        $this->assertEquals(100000, $product->getPriceForSize('2x3'));
        $this->assertEquals(180000, $product->getPriceForSize('3x4'));
        $this->assertEquals(350000, $product->getPriceForSize('4x6'));
        $this->assertEquals(700000, $product->getPriceForSize('6x8'));
    }

    /**
     * Test perhitungan harga otomatis berdasarkan luas (m2) jika size_prices tidak ada
     */
    public function test_get_price_for_size_auto_calculation()
    {
        $product = Product::create([
            'name' => 'Terpal Test',
            'price' => 60000, // harga dasar 2x3 (6 m2) = 10.000 per m2
        ]);

        // 2x3 = 6 m2 = 60.000
        $this->assertEquals(60000, $product->getPriceForSize('2x3'));
        
        // 3x4 = 12 m2 = 120.000
        $this->assertEquals(120000, $product->getPriceForSize('3x4'));
        
        // 4x6 = 24 m2 = 240.000
        $this->assertEquals(240000, $product->getPriceForSize('4x6'));
        
        // 6x8 = 48 m2 = 480.000
        $this->assertEquals(480000, $product->getPriceForSize('6x8'));
    }

    /**
     * Test perhitungan min_price untuk negosiasi dengan min_price_per_size
     */
    public function test_get_min_price_for_size_with_custom_min_price()
    {
        $product = Product::create([
            'name' => 'Terpal Test',
            'price' => 100000,
            'min_price' => 65000, // 65% dari harga dasar
            'min_price_per_size' => [
                '2x3' => 65000,
                '3x4' => 120000,
            ],
        ]);

        $this->assertEquals(65000, $product->getMinPriceForSize('2x3'));
        $this->assertEquals(120000, $product->getMinPriceForSize('3x4'));
    }

    /**
     * Test perhitungan min_price otomatis (65% dari harga) jika min_price tidak diset
     */
    public function test_get_min_price_for_size_auto_calculation()
    {
        $product = Product::create([
            'name' => 'Terpal Test',
            'price' => 100000, // harga 2x3
        ]);

        // Min price untuk 2x3 = 65% dari 100.000 = 65.000
        $minPrice2x3 = $product->getMinPriceForSize('2x3');
        $this->assertEquals(65000, $minPrice2x3);

        // Min price untuk 3x4 = proporsi dari harga 3x4
        // Harga 3x4 = 200.000 (12 m2 / 6 m2 * 100.000)
        // Min price = 200.000 * (65.000 / 100.000) = 130.000
        $minPrice3x4 = $product->getMinPriceForSize('3x4');
        $expectedMinPrice3x4 = 200000 * (65000 / 100000);
        $this->assertEquals($expectedMinPrice3x4, $minPrice3x4);
    }

    /**
     * Test edge case: ukuran tidak valid, default ke 2x3
     */
    public function test_get_price_for_invalid_size_defaults_to_2x3()
    {
        $product = Product::create([
            'name' => 'Terpal Test',
            'price' => 60000,
        ]);

        $invalidSize = $product->getPriceForSize('invalid_size');
        $this->assertEquals(60000, $invalidSize); // Default ke 2x3 (6 m2)
    }
}

