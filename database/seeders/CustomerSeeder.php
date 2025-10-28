<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Customer untuk data penjualan 1/11/2024
        Customer::create([
            'name' => 'BP GILANG',
            'email' => 'bp.gilang@example.com',
            'phone' => '081234567891',
            'password' => '123',
            'address' => 'Alamat BP GILANG',
        ]);

        Customer::create([
            'name' => 'GRACIA',
            'email' => 'gracia@example.com',
            'phone' => '081234567892',
            'password' => '123',
            'address' => 'Alamat GRACIA',
        ]);

        Customer::create([
            'name' => 'PT TAN (BP TEKKO)',
            'email' => 'pt.tan@example.com',
            'phone' => '081234567893',
            'password' => '123',
            'address' => 'Alamat PT TAN (BP TEKKO)',
        ]);

        Customer::create([
            'name' => 'AZHAR JAYA',
            'email' => 'azhar.jaya@example.com',
            'phone' => '081234567894',
            'password' => '123',
            'address' => 'Alamat AZHAR JAYA',
        ]);

        Customer::create([
            'name' => 'BP SOLEH',
            'email' => 'bp.soleh@example.com',
            'phone' => '081234567895',
            'password' => '123',
            'address' => 'Alamat BP SOLEH',
        ]);

        $this->command->info('Customer data berhasil dibuat!');
    }
}
