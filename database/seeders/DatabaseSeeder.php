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

        $this->createUser();
        
        // Seed customers
        $this->call(CustomerSeeder::class);
        
        // Seed custom materials
        $this->call(CustomMaterialSeeder::class);
        
        // Seed categories
        $this->call(CategorySeeder::class);
        
        // Seed products
        $this->call(ProductSeeder::class);
        
        // Seed sales data
        $this->call(FiveMonthsSalesSeeder::class);
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
