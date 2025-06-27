<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->createProduct();
        $this->createUser();
    }

    function createProduct()
    {
        Product::create([
            'id' => 1,
            'name' => 'Terpal A5',
            'description' => 'Description 1',
            'price' => 1000,
            'size' => '2x3',
            'live' => 1,
        ]);
        Product::create([
            'id' => 2,
            'name' => 'Terpal A5',
            'description' => 'Description 1',
            'price' => 2000,
            'size' => '3x4',
            'live' => 1,
        ]);
        Product::create([
            'id' => 3,
            'name' => 'Terpal A5',
            'description' => 'Description 1',
            'price' => 3000,
            'size' => '4x6',
            'live' => 1,
        ]);

        ProductVariant::create([
            'product_id' => 1,
            'color' => 'biru silver',
            'stock' => 100,
        ]);
        ProductVariant::create([
            'product_id' => 1,
            'color' => 'biru polos',
            'stock' => 100,
        ]);
        ProductVariant::create([
            'product_id' => 1,
            'color' => 'oranye silver',
            'stock' => 100,
        ]);

        ProductVariant::create([
            'product_id' => 2,
            'color' => 'biru silver',
            'stock' => 100,
        ]);
        ProductVariant::create([
            'product_id' => 2,
            'color' => 'biru polos',
            'stock' => 100,
        ]);
        ProductVariant::create([
            'product_id' => 2,
            'color' => 'oranye silver',
            'stock' => 100,
        ]);
        ProductVariant::create([
            'product_id' => 2,
            'color' => 'oranye polos',
            'stock' => 100,
        ]);
    }

    function createUser()
    {
        Employee::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => '123',
            'role' => 'admin',
        ]);

        Employee::create([
            'name' => 'Keuangan',
            'email' => 'keuangan@gmail.com',
            'password' => '123',
            'role' => 'keuangan',
        ]);

        Employee::create([
            'name' => 'Owner',
            'email' => 'owner@gmail.com',
            'password' => '123',
            'role' => 'owner',
        ]);

        Employee::create([
            'name' => 'Gudang',
            'email' => 'gudang@gmail.com',
            'password' => '123',
            'role' => 'gudang',
        ]);

        Employee::create([
            'name' => 'Driver',
            'email' => 'driver@gmail.com',
            'password' => '123',
            'role' => 'driver',
        ]);

        Customer::create([
            'name' => 'Customer',
            'email' => 'customer@gmail.com',
            'phone' => '081234567890',
            'password' => '123',
            'address' => 'Jl. Admin',
        ]);
    }
}
