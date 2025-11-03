<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Categories;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Get category IDs
        $plastikCategory = Categories::where('name', 'Terpal Plastik')->first();
        $kainCategory = Categories::where('name', 'Terpal Kain')->first();
        $karetCategory = Categories::where('name', 'Terpal Karet')->first();

        // Define colors
        $colors = [
            'Biru Silver', 'Biru Polos', 'Oranye Silver', 'Oranye Polos',
            'Hijau Polos', 'Hijau Silver', 'Coklat Silver', 'Coklat Polos'
        ];

        // Fixed available sizes (used on the product's size field)
        $sizesText = '2x3, 3x4, 4x6, 6x8';

        // Terpal Plastik products (A2 .. A20 with variants as provided)
        $plastikProducts = [
            ['code' => 'A2', 'price_per_m2' => 4200],
            ['code' => 'A3B', 'price_per_m2' => 4400],
            ['code' => 'A3 CN', 'price_per_m2' => 4500],
            ['code' => 'A4', 'price_per_m2' => 5000],
            ['code' => 'A5', 'price_per_m2' => 5800],
            ['code' => 'A6', 'price_per_m2' => 6000],
            ['code' => 'A7', 'price_per_m2' => 7000],
            ['code' => 'A8 LKL', 'price_per_m2' => 8500],
            ['code' => 'A8 SKR', 'price_per_m2' => 9500],
            ['code' => 'A10', 'price_per_m2' => 12000],
            ['code' => 'A12 KOR', 'price_per_m2' => 10500],
            ['code' => 'A12 SKR', 'price_per_m2' => 12500],
            ['code' => 'A15 LKL', 'price_per_m2' => 12000],
            ['code' => 'A15 SKR', 'price_per_m2' => 13000],
            ['code' => 'A17 KOR', 'price_per_m2' => 13500],
            ['code' => 'A17 SKR', 'price_per_m2' => 14000],
            ['code' => 'A20 KOR', 'price_per_m2' => 16000],
            ['code' => 'A20 SKR', 'price_per_m2' => 17000],
            ['code' => 'A20 UV', 'price_per_m2' => 18500],
        ];

        // Terpal Kain products (JP, Super, Keep Jep)
        $kainProducts = [
            ['code' => 'KEEP JEP', 'price_per_m2' => 70000],
            ['code' => 'JP', 'price_per_m2' => 90000],
            ['code' => 'SUPER', 'price_per_m2' => 100000],
        ];

        // Terpal Karet products (Ulin DD, Ulin Orchid, Ulin Samhe)
        $karetProducts = [
            ['code' => 'ULIN DD', 'price_per_m2' => 25000],
            ['code' => 'ULIN ORCHID', 'price_per_m2' => 27000],
            ['code' => 'ULIN SAMHE', 'price_per_m2' => 28000],
        ];

        // Create products for each category
        $this->createProducts($plastikProducts, $plastikCategory, 'Terpal Plastik', $colors, $sizesText);
        $this->createProducts($kainProducts, $kainCategory, 'Terpal Kain', $colors, $sizesText);
        $this->createProducts($karetProducts, $karetCategory, 'Terpal Karet', $colors, $sizesText);
    }

    private function createProducts($products, $category, $categoryName, $colors, $sizesText)
    {
        // Ensure products directory exists in storage
        $productsDir = storage_path('app/public/products');
        if (!File::exists($productsDir)) {
            File::makeDirectory($productsDir, 0755, true);
        }

        foreach ($products as $productData) {
            $description = $categoryName . ' ' . $productData['code'] . ' dengan kualitas unggul, tahan cuaca, dan cocok untuk berbagai kebutuhan industri maupun rumah tangga.';
            $codeSlug = strtolower(str_replace(' ', '-', $productData['code']));
            
            // Get and copy product image to storage
            $imagePath = $this->setupProductImage($productData['code'], $categoryName);
            
            // Create ONE product per code (without size in name)
            $product = Product::create([
                'name' => $categoryName . ' ' . $productData['code'],
                'description' => $description,
                'image' => $imagePath, // Path relative to storage/app/public (for asset('storage/...'))
                'price' => $productData['price_per_m2'] * 2 * 3, // Default price for 2x3
                'min_price' => $productData['price_per_m2'] * 2 * 3,
                'min_buying_stock' => 10,
                'size' => $sizesText, // All available sizes
                'live' => true,
                'category_id' => $category->id,
            ]);

            // Attach ALL colors as variants under this product
            foreach ($colors as $color) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'color' => strtolower($color),
                    'stock' => 100,
                ]);
            }
        }
    }

    /**
     * Setup product image: copy from public/images to storage/app/public/products
     * Returns path relative to storage/app/public (e.g., 'products/Terpal-A2.png')
     */
    private function setupProductImage($code, $categoryName)
    {
        // Map product codes to source image filenames in public/images
        $sourceImageMap = [
            'A2' => 'Terpal-A2.png',
            'A3B' => 'Terpal-A3B.png',
            'A3 CN' => 'Terpal-A3CN.png',
            'A4' => 'Terpal-A4.png',
            'A5' => 'Terpal-A5.jpg',
        ];
        
        $storageProductsDir = storage_path('app/public/products');
        $imageFileName = null;
        $sourcePath = null;
        
        // Check if we have a specific image for this product
        if (isset($sourceImageMap[$code])) {
            $sourceFileName = $sourceImageMap[$code];
            $sourcePath = public_path('images/' . $sourceFileName);
            
            // Determine extension from source file
            $ext = pathinfo($sourceFileName, PATHINFO_EXTENSION);
            $imageFileName = 'terpal-' . strtolower(str_replace(' ', '-', $code)) . '.' . $ext;
        }
        
        // If source image exists, copy it to storage (always update if exists)
        if ($sourcePath && File::exists($sourcePath)) {
            $destinationPath = $storageProductsDir . '/' . $imageFileName;
            
            // Always copy to update image if source has changed
            File::copy($sourcePath, $destinationPath);
            $this->command->info("Copied/Updated image for product {$code}: {$sourceFileName} -> products/{$imageFileName}");
            
            return 'products/' . $imageFileName;
        }
        
        // Use default placeholder for products without specific images
        $placeholderPath = public_path('images/gulungan-terpal.png');
        $placeholderFileName = 'terpal-' . strtolower(str_replace(' ', '-', $code)) . '.png';
        $placeholderDestination = $storageProductsDir . '/' . $placeholderFileName;
        
        if (File::exists($placeholderPath) && !File::exists($placeholderDestination)) {
            File::copy($placeholderPath, $placeholderDestination);
        }
        
        return 'products/' . $placeholderFileName;
    }
}
