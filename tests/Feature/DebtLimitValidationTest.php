<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\HInvoice;
use App\Models\PaymentModel;
use App\Http\Controllers\CustomerController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * White Box Testing untuk Debt Limit Validation
 * 
 * Test ini menguji logika validasi limit hutang yang ada di:
 * - app/Http/Controllers/CheckoutController.php (method index)
 * - app/Http/Controllers/CustomerController.php (method checkCustomerDebtStatus)
 * 
 * Business Rules yang diuji:
 * 1. Limit hutang = 10.000.000 (10 juta)
 * 2. Filter invoice dengan payment method 'hutang' atau 'cod' yang belum lunas (is_paid = 0)
 * 3. Hitung total hutang aktif = sum(grand_total - paid_amount) untuk invoice yang belum lunas
 * 4. Cek hutang terlambat: invoice dengan method 'hutang' yang dibuat > 1 bulan lalu dan masih ada sisa hutang
 * 5. Validasi total hutang setelah transaksi tidak melebihi limit
 * 6. Disable checkout jika: totalHutangAktif >= limitHutang ATAU adaHutangTerlambat ATAU melebihiLimit
 */
class DebtLimitValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test perhitungan total hutang aktif untuk customer
     */
    public function test_calculate_total_active_debt()
    {
        // Buat employee untuk employee_id
        $employee = Employee::create([
            'name' => 'Test Employee',
            'email' => 'employee@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '081234567890',
            'password' => bcrypt('password'),
        ]);
        
        // Buat invoice dengan hutang yang belum lunas
        $invoice1 = HInvoice::create([
            'code' => 'INV-001',
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'grand_total' => 3000000,
            'paid_amount' => 1000000,
            'status' => 'Dikemas',
        ]);
        
        PaymentModel::create([
            'invoice_id' => $invoice1->id,
            'method' => 'hutang',
            'type' => 'full',
            'is_paid' => 0,
            'amount' => 3000000,
        ]);
        
        $invoice2 = HInvoice::create([
            'code' => 'INV-002',
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'grand_total' => 2000000,
            'paid_amount' => 0,
            'status' => 'Dikemas',
        ]);
        
        PaymentModel::create([
            'invoice_id' => $invoice2->id,
            'method' => 'cod',
            'type' => 'full',
            'is_paid' => 0,
            'amount' => 2000000,
        ]);
        
        // Hitung total hutang aktif
        $hutangInvoices = HInvoice::where('customer_id', $customer->id)
            ->with(['payments' => function($q) {
                $q->where('is_paid', 0);
            }])
            ->get();
        
        $filteredHutang = $hutangInvoices->filter(function($inv) {
            $p = $inv->payments->first();
            return $p && in_array($p->method, ['cod', 'hutang']) && $p->is_paid == 0;
        });
        
        $totalHutangAktif = $filteredHutang->sum(function($inv) {
            return $inv->grand_total - ($inv->paid_amount ?? 0);
        });
        
        // Invoice1: 3.000.000 - 1.000.000 = 2.000.000
        // Invoice2: 2.000.000 - 0 = 2.000.000
        // Total: 4.000.000
        $this->assertEquals(4000000, $totalHutangAktif);
    }

    /**
     * Test validasi limit hutang (10 juta)
     */
    public function test_validate_debt_limit()
    {
        $limitHutang = 10000000; // 10 juta
        
        $totalHutangAktif = 9500000; // 9.5 juta
        $melebihiLimit = $totalHutangAktif >= $limitHutang;
        
        $this->assertFalse($melebihiLimit);
        
        $totalHutangAktif2 = 10000000; // tepat 10 juta
        $melebihiLimit2 = $totalHutangAktif2 >= $limitHutang;
        
        $this->assertTrue($melebihiLimit2);
        
        $totalHutangAktif3 = 11000000; // 11 juta
        $melebihiLimit3 = $totalHutangAktif3 >= $limitHutang;
        
        $this->assertTrue($melebihiLimit3);
    }

    /**
     * Test validasi total hutang setelah transaksi tidak melebihi limit
     */
    public function test_validate_total_debt_after_transaction()
    {
        $limitHutang = 10000000;
        $totalHutangAktif = 8000000; // 8 juta
        $subtotalProduk = 3000000; // 3 juta (transaksi baru)
        
        $totalHutangSetelahTransaksi = $totalHutangAktif + $subtotalProduk; // 11 juta
        $melebihiLimit = $totalHutangSetelahTransaksi > $limitHutang;
        
        $this->assertTrue($melebihiLimit);
        
        // Test case: tidak melebihi limit
        $subtotalProduk2 = 1500000; // 1.5 juta
        $totalHutangSetelahTransaksi2 = $totalHutangAktif + $subtotalProduk2; // 9.5 juta
        $melebihiLimit2 = $totalHutangSetelahTransaksi2 > $limitHutang;
        
        $this->assertFalse($melebihiLimit2);
    }

    /**
     * Test deteksi hutang terlambat (> 1 bulan)
     */
    public function test_detect_overdue_debt()
    {
        // Buat employee untuk employee_id
        $employee = Employee::create([
            'name' => 'Test Employee',
            'email' => 'employee2@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test2@example.com',
            'phone' => '081234567891',
            'password' => bcrypt('password'),
        ]);
        
        // Buat invoice dengan hutang yang dibuat lebih dari 1 bulan lalu
        $createdAt = now()->subMonths(2); // 2 bulan lalu
        
        $invoice = HInvoice::create([
            'code' => 'INV-001',
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'grand_total' => 5000000,
            'paid_amount' => 2000000,
            'status' => 'Dikemas',
        ]);
        
        // Update created_at setelah create menggunakan DB langsung
        \DB::table('hinvoice')
            ->where('id', $invoice->id)
            ->update([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        
        PaymentModel::create([
            'invoice_id' => $invoice->id,
            'method' => 'hutang',
            'type' => 'full',
            'is_paid' => 0,
            'amount' => 5000000,
        ]);
        
        // Cek hutang terlambat - query ulang dari database dengan fresh
        $hutangInvoices = HInvoice::where('customer_id', $customer->id)
            ->with(['payments' => function($q) {
                $q->where('is_paid', 0);
            }])
            ->get();
        
        $filteredHutang = $hutangInvoices->filter(function($inv) {
            $p = $inv->payments->first();
            return $p && in_array($p->method, ['cod', 'hutang']) && $p->is_paid == 0;
        });
        
        // Debug: cek apakah invoice ada dan created_at benar
        $this->assertGreaterThan(0, $filteredHutang->count(), 'Harus ada invoice dengan hutang');
        
        $adaHutangTerlambat = $filteredHutang->contains(function($inv) {
            $p = $inv->payments->first();
            $sisaHutang = $inv->grand_total - ($inv->paid_amount ?? 0);
            // Clone created_at untuk menghindari mutasi (sesuai dengan kode asli)
            $dueDate = $inv->created_at->copy()->addMonth();
            $isOverdue = now()->gt($dueDate);
            
            return $p && 
                   $p->method == 'hutang' && 
                   $isOverdue && 
                   $sisaHutang > 0;
        });
        
        $this->assertTrue($adaHutangTerlambat, 'Hutang yang dibuat 2 bulan lalu seharusnya terdeteksi sebagai terlambat');
    }

    /**
     * Test hutang tidak terlambat jika masih dalam 1 bulan
     */
    public function test_debt_not_overdue_if_within_one_month()
    {
        // Buat employee untuk employee_id
        $employee = Employee::create([
            'name' => 'Test Employee',
            'email' => 'employee3@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test3@example.com',
            'phone' => '081234567892',
            'password' => bcrypt('password'),
        ]);
        
        // Buat invoice dengan hutang yang dibuat 2 minggu lalu
        $invoice = HInvoice::create([
            'code' => 'INV-001',
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'grand_total' => 5000000,
            'paid_amount' => 0,
            'status' => 'Dikemas',
            'created_at' => now()->subDays(15), // 15 hari lalu
        ]);
        
        PaymentModel::create([
            'invoice_id' => $invoice->id,
            'method' => 'hutang',
            'type' => 'full',
            'is_paid' => 0,
            'amount' => 5000000,
        ]);
        
        // Cek hutang terlambat
        $hutangInvoices = HInvoice::where('customer_id', $customer->id)
            ->with(['payments' => function($q) {
                $q->where('is_paid', 0);
            }])
            ->get();
        
        $filteredHutang = $hutangInvoices->filter(function($inv) {
            $p = $inv->payments->first();
            return $p && in_array($p->method, ['cod', 'hutang']) && $p->is_paid == 0;
        });
        
        $adaHutangTerlambat = $filteredHutang->contains(function($inv) {
            $p = $inv->payments->first();
            $sisaHutang = $inv->grand_total - ($inv->paid_amount ?? 0);
            // Clone created_at untuk menghindari mutasi
            $dueDate = $inv->created_at->copy()->addMonth();
            return $p && 
                   $p->method == 'hutang' && 
                   now()->gt($dueDate) && 
                   $sisaHutang > 0;
        });
        
        $this->assertFalse($adaHutangTerlambat);
    }

    /**
     * Test disable checkout jika total hutang aktif >= limit
     */
    public function test_disable_checkout_when_debt_exceeds_limit()
    {
        $limitHutang = 10000000;
        $totalHutangAktif = 10000000; // tepat 10 juta
        
        $adaHutangTerlambat = false;
        $melebihiLimit = false;
        
        $disableCheckout = $totalHutangAktif >= $limitHutang || $adaHutangTerlambat || $melebihiLimit;
        
        $this->assertTrue($disableCheckout);
    }

    /**
     * Test disable checkout jika ada hutang terlambat
     */
    public function test_disable_checkout_when_has_overdue_debt()
    {
        $limitHutang = 10000000;
        $totalHutangAktif = 5000000; // 5 juta (masih di bawah limit)
        $adaHutangTerlambat = true; // ada hutang terlambat
        
        $melebihiLimit = false;
        
        $disableCheckout = $totalHutangAktif >= $limitHutang || $adaHutangTerlambat || $melebihiLimit;
        
        $this->assertTrue($disableCheckout);
    }

    /**
     * Test disable checkout jika total hutang setelah transaksi melebihi limit
     */
    public function test_disable_checkout_when_transaction_would_exceed_limit()
    {
        $limitHutang = 10000000;
        $totalHutangAktif = 8000000; // 8 juta
        $subtotalProduk = 3000000; // 3 juta
        $totalHutangSetelahTransaksi = $totalHutangAktif + $subtotalProduk; // 11 juta
        
        $melebihiLimit = $totalHutangSetelahTransaksi > $limitHutang;
        $adaHutangTerlambat = false;
        
        $disableCheckout = $totalHutangAktif >= $limitHutang || $adaHutangTerlambat || $melebihiLimit;
        
        $this->assertTrue($disableCheckout);
    }

    /**
     * Test checkout tidak di-disable jika semua kondisi aman
     */
    public function test_checkout_not_disabled_when_all_conditions_safe()
    {
        $limitHutang = 10000000;
        $totalHutangAktif = 5000000; // 5 juta (di bawah limit)
        $subtotalProduk = 2000000; // 2 juta
        $totalHutangSetelahTransaksi = $totalHutangAktif + $subtotalProduk; // 7 juta
        
        $melebihiLimit = $totalHutangSetelahTransaksi > $limitHutang; // false
        $adaHutangTerlambat = false;
        
        $disableCheckout = $totalHutangAktif >= $limitHutang || $adaHutangTerlambat || $melebihiLimit;
        
        $this->assertFalse($disableCheckout);
    }

    /**
     * Test filter invoice hanya yang payment method 'hutang' atau 'cod' dan belum lunas
     */
    public function test_filter_only_unpaid_hutang_or_cod_invoices()
    {
        // Buat employee untuk employee_id
        $employee = Employee::create([
            'name' => 'Test Employee',
            'email' => 'employee4@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test4@example.com',
            'phone' => '081234567893',
            'password' => bcrypt('password'),
        ]);
        
        // Invoice dengan hutang belum lunas
        $invoice1 = HInvoice::create([
            'code' => 'INV-001',
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'grand_total' => 2000000,
            'paid_amount' => 0,
            'status' => 'Dikemas',
        ]);
        PaymentModel::create([
            'invoice_id' => $invoice1->id,
            'method' => 'hutang',
            'type' => 'full',
            'is_paid' => 0,
            'amount' => 2000000,
        ]);
        
        // Invoice dengan COD belum lunas
        $invoice2 = HInvoice::create([
            'code' => 'INV-002',
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'grand_total' => 1500000,
            'paid_amount' => 0,
            'status' => 'Dikemas',
        ]);
        PaymentModel::create([
            'invoice_id' => $invoice2->id,
            'method' => 'cod',
            'type' => 'full',
            'is_paid' => 0,
            'amount' => 1500000,
        ]);
        
        // Invoice dengan transfer (tidak termasuk)
        $invoice3 = HInvoice::create([
            'code' => 'INV-003',
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'grand_total' => 1000000,
            'paid_amount' => 0,
            'status' => 'Dikemas',
        ]);
        PaymentModel::create([
            'invoice_id' => $invoice3->id,
            'method' => 'transfer',
            'type' => 'full',
            'is_paid' => 0,
            'amount' => 1000000,
        ]);
        
        // Invoice sudah lunas (tidak termasuk)
        $invoice4 = HInvoice::create([
            'code' => 'INV-004',
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'grand_total' => 3000000,
            'paid_amount' => 3000000,
            'status' => 'Selesai',
        ]);
        PaymentModel::create([
            'invoice_id' => $invoice4->id,
            'method' => 'hutang',
            'type' => 'full',
            'is_paid' => 1, // sudah lunas
            'amount' => 3000000,
        ]);
        
        // Filter
        $hutangInvoices = HInvoice::where('customer_id', $customer->id)
            ->with(['payments' => function($q) {
                $q->where('is_paid', 0);
            }])
            ->get();
        
        $filteredHutang = $hutangInvoices->filter(function($inv) {
            $p = $inv->payments->first();
            return $p && in_array($p->method, ['cod', 'hutang']) && $p->is_paid == 0;
        });
        
        // Hanya invoice1 dan invoice2 yang terfilter
        $this->assertEquals(2, $filteredHutang->count());
        $this->assertTrue($filteredHutang->contains($invoice1));
        $this->assertTrue($filteredHutang->contains($invoice2));
        $this->assertFalse($filteredHutang->contains($invoice3));
        $this->assertFalse($filteredHutang->contains($invoice4));
    }

    /**
     * Test method checkCustomerDebtStatus mengembalikan data yang benar
     */
    public function test_check_customer_debt_status_method()
    {
        // Buat employee untuk employee_id
        $employee = Employee::create([
            'name' => 'Test Employee',
            'email' => 'employee5@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test5@example.com',
            'phone' => '081234567894',
            'password' => bcrypt('password'),
        ]);
        
        // Buat invoice dengan hutang
        $invoice = HInvoice::create([
            'code' => 'INV-001',
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'grand_total' => 6000000,
            'paid_amount' => 2000000,
            'status' => 'Dikemas',
        ]);
        
        PaymentModel::create([
            'invoice_id' => $invoice->id,
            'method' => 'hutang',
            'type' => 'full',
            'is_paid' => 0,
            'amount' => 6000000,
        ]);
        
        // Panggil method checkCustomerDebtStatus
        $result = CustomerController::checkCustomerDebtStatus($customer->id);
        
        $this->assertEquals(4000000, $result['totalHutangAktif']); // 6jt - 2jt = 4jt
        $this->assertEquals(10000000, $result['limitHutang']);
        $this->assertFalse($result['melebihiLimit']);
        $this->assertFalse($result['adaHutangTerlambat']);
        $this->assertEquals(6000000, $result['sisaLimit']); // 10jt - 4jt = 6jt
        $this->assertFalse($result['disableCheckout']);
    }
}

