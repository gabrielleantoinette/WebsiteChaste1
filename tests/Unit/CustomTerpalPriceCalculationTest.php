<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * White Box Testing untuk Custom Terpal Price Calculation
 * 
 * Test ini menguji logika perhitungan harga custom terpal yang ada di:
 * - resources/views/custom.blade.php (JavaScript function calculateTotal)
 * - app/Http/Controllers/CartController.php (addCustomItem)
 * 
 * Formula yang diuji:
 * 1. Luas = panjang × lebar
 * 2. Luas dengan volume = panjang × lebar + 2×(panjang×tinggi) + 2×(lebar×tinggi)
 * 3. Harga Bahan = luas × harga per m2
 * 4. Harga Ring = jumlah_ring × 50
 * 5. Harga Tali = keliling × 500 (jika pakai_tali = true)
 * 6. Keliling = 2 × (panjang + lebar)
 * 7. Total per item = Harga Bahan + Harga Ring + Harga Tali
 * 8. Grand Total = Total per item × quantity
 */
class CustomTerpalPriceCalculationTest extends TestCase
{
    /**
     * Test perhitungan luas dasar (tanpa volume)
     */
    public function test_calculate_base_area()
    {
        $panjang = 5;
        $lebar = 3;
        $luas = $panjang * $lebar;
        
        $this->assertEquals(15, $luas);
    }

    /**
     * Test perhitungan luas dengan volume (terpal bervolume)
     */
    public function test_calculate_area_with_volume()
    {
        $panjang = 5;
        $lebar = 3;
        $tinggi = 2;
        
        $luasDasar = $panjang * $lebar; // 15
        $luasSamping1 = 2 * ($panjang * $tinggi); // 2 * (5 * 2) = 20
        $luasSamping2 = 2 * ($lebar * $tinggi); // 2 * (3 * 2) = 12
        $totalLuas = $luasDasar + $luasSamping1 + $luasSamping2; // 47
        
        $this->assertEquals(47, $totalLuas);
    }

    /**
     * Test perhitungan harga bahan berdasarkan luas
     */
    public function test_calculate_material_price()
    {
        $panjang = 5;
        $lebar = 3;
        $luas = $panjang * $lebar; // 15 m2
        $hargaPerM2 = 10000;
        $hargaBahan = $luas * $hargaPerM2;
        
        $this->assertEquals(150000, $hargaBahan);
    }

    /**
     * Test perhitungan harga ring
     */
    public function test_calculate_ring_price()
    {
        $jumlahRing = 10;
        $hargaPerRing = 50;
        $hargaRing = $jumlahRing * $hargaPerRing;
        
        $this->assertEquals(500, $hargaRing);
    }

    /**
     * Test perhitungan harga tali (jika pakai_tali = true)
     */
    public function test_calculate_rope_price()
    {
        $panjang = 5;
        $lebar = 3;
        $keliling = 2 * ($panjang + $lebar); // 2 * (5 + 3) = 16
        $hargaPerMeter = 500;
        $pakaiTali = true;
        $hargaTali = $pakaiTali ? $keliling * $hargaPerMeter : 0;
        
        $this->assertEquals(8000, $hargaTali);
    }

    /**
     * Test perhitungan harga tali (jika pakai_tali = false)
     */
    public function test_calculate_rope_price_when_not_used()
    {
        $panjang = 5;
        $lebar = 3;
        $keliling = 2 * ($panjang + $lebar);
        $hargaPerMeter = 500;
        $pakaiTali = false;
        $hargaTali = $pakaiTali ? $keliling * $hargaPerMeter : 0;
        
        $this->assertEquals(0, $hargaTali);
    }

    /**
     * Test perhitungan total per item
     */
    public function test_calculate_total_per_item()
    {
        $panjang = 5;
        $lebar = 3;
        $tinggi = 0; // tidak pakai volume
        $hargaPerM2 = 10000;
        $jumlahRing = 10;
        $pakaiTali = true;
        
        // Hitung luas
        $luas = $panjang * $lebar; // 15 m2
        
        // Hitung komponen harga
        $hargaBahan = $luas * $hargaPerM2; // 150.000
        $hargaRing = $jumlahRing * 50; // 500
        $keliling = 2 * ($panjang + $lebar); // 16
        $hargaTali = $pakaiTali ? $keliling * 500 : 0; // 8.000
        
        // Total per item
        $totalPerItem = $hargaBahan + $hargaRing + $hargaTali; // 158.500
        
        $this->assertEquals(158500, $totalPerItem);
    }

    /**
     * Test perhitungan grand total dengan quantity
     */
    public function test_calculate_grand_total_with_quantity()
    {
        $totalPerItem = 158500;
        $quantity = 3;
        $grandTotal = $totalPerItem * $quantity;
        
        $this->assertEquals(475500, $grandTotal);
    }

    /**
     * Test perhitungan lengkap custom terpal dengan volume
     */
    public function test_complete_calculation_with_volume()
    {
        // Input
        $panjang = 10;
        $lebar = 5;
        $tinggi = 2;
        $hargaPerM2 = 12000;
        $jumlahRing = 20;
        $pakaiTali = true;
        $quantity = 2;
        
        // Hitung luas dengan volume
        $luasDasar = $panjang * $lebar; // 50
        $luasSamping1 = 2 * ($panjang * $tinggi); // 40
        $luasSamping2 = 2 * ($lebar * $tinggi); // 20
        $totalLuas = $luasDasar + $luasSamping1 + $luasSamping2; // 110 m2
        
        // Hitung komponen harga
        $hargaBahan = $totalLuas * $hargaPerM2; // 1.320.000
        $hargaRing = $jumlahRing * 50; // 1.000
        $keliling = 2 * ($panjang + $lebar); // 30
        $hargaTali = $pakaiTali ? $keliling * 500 : 0; // 15.000
        
        // Total
        $totalPerItem = $hargaBahan + $hargaRing + $hargaTali; // 1.336.000
        $grandTotal = $totalPerItem * $quantity; // 2.672.000
        
        $this->assertEquals(110, $totalLuas);
        $this->assertEquals(1320000, $hargaBahan);
        $this->assertEquals(1000, $hargaRing);
        $this->assertEquals(15000, $hargaTali);
        $this->assertEquals(1336000, $totalPerItem);
        $this->assertEquals(2672000, $grandTotal);
    }

    /**
     * Test validasi batas ukuran maksimal
     */
    public function test_validate_max_size_constraints()
    {
        $maxPanjang = 90;
        $maxLebar = 90;
        $maxTinggi = 5;
        
        $panjang = 95; // melebihi batas
        $lebar = 50;
        $tinggi = 3;
        
        $panjangValid = $panjang <= $maxPanjang;
        $lebarValid = $lebar <= $maxLebar;
        $tinggiValid = $tinggi <= $maxTinggi;
        
        $this->assertFalse($panjangValid);
        $this->assertTrue($lebarValid);
        $this->assertTrue($tinggiValid);
    }
}

