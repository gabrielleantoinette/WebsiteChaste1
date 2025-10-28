#!/bin/bash

# Script untuk menjalankan semua seeder
# Gunakan: ./restore_data.sh

echo "🔄 Memulai restore data..."

echo "📊 Menjalankan CustomerSeeder..."
php artisan db:seed --class=CustomerSeeder

echo "💰 Menjalankan SalesSeeder..."
php artisan sales:seed --data=sales_data_20241101.php

echo "📦 Menjalankan CategorySeeder..."
php artisan db:seed --class=CategorySeeder

echo "🏭 Menjalankan CustomMaterialSeeder..."
php artisan db:seed --class=CustomMaterialSeeder

echo "✅ Data berhasil direstore!"
echo ""
echo "📈 Data yang tersedia:"
php artisan tinker --execute="
echo 'Products: ' . App\Models\Product::count() . PHP_EOL;
echo 'Customers: ' . App\Models\Customer::count() . PHP_EOL;
echo 'Employees: ' . App\Models\Employee::count() . PHP_EOL;
echo 'Invoices: ' . App\Models\HInvoice::count() . PHP_EOL;
echo 'Categories: ' . App\Models\Categories::count() . PHP_EOL;
"
