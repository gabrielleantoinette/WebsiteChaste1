<?php

namespace Database\Seeders;

use App\Models\CustomMaterial;
use App\Models\CustomMaterialVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CustomMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data (handle foreign key constraints)
        CustomMaterialVariant::query()->delete();
        CustomMaterial::query()->delete();

        // Array of custom materials with their details - Sesuai dengan tabel harga
        $materials = [
            // TERPAL PLASTIK - Sesuai dengan tabel harga
            [
                'name' => 'Terpal Plastik A2 CN',
                'price' => 4200,
                'color' => 'Biru',
                'stock' => 100,
                'variants' => [
                    ['color' => 'biru silver', 'stock' => 50],
                    ['color' => 'biru polos', 'stock' => 30],
                    ['color' => 'hijau silver', 'stock' => 20]
                ]
            ],
            [
                'name' => 'Terpal Plastik A3B LKL',
                'price' => 4400,
                'color' => 'Hijau',
                'stock' => 80,
                'variants' => [
                    ['color' => 'hijau silver', 'stock' => 40],
                    ['color' => 'hijau polos', 'stock' => 25],
                    ['color' => 'biru silver', 'stock' => 15]
                ]
            ],
            [
                'name' => 'Terpal Plastik A3 CN',
                'price' => 4500,
                'color' => 'Hijau',
                'stock' => 75,
                'variants' => [
                    ['color' => 'hijau silver', 'stock' => 35],
                    ['color' => 'hijau polos', 'stock' => 25],
                    ['color' => 'biru silver', 'stock' => 15]
                ]
            ],
            [
                'name' => 'Terpal Plastik A4 LKL',
                'price' => 5000,
                'color' => 'Merah',
                'stock' => 70,
                'variants' => [
                    ['color' => 'merah silver', 'stock' => 35],
                    ['color' => 'merah polos', 'stock' => 20],
                    ['color' => 'orange silver', 'stock' => 15]
                ]
            ],
            [
                'name' => 'Terpal Plastik A5 CN',
                'price' => 5800,
                'color' => 'Merah',
                'stock' => 65,
                'variants' => [
                    ['color' => 'merah silver', 'stock' => 30],
                    ['color' => 'merah polos', 'stock' => 20],
                    ['color' => 'orange silver', 'stock' => 15]
                ]
            ],
            [
                'name' => 'Terpal Plastik A6 LKL',
                'price' => 6000,
                'color' => 'Orange',
                'stock' => 60,
                'variants' => [
                    ['color' => 'orange silver', 'stock' => 30],
                    ['color' => 'orange polos', 'stock' => 20],
                    ['color' => 'kuning silver', 'stock' => 10]
                ]
            ],
            [
                'name' => 'Terpal Plastik A7 KOR',
                'price' => 7000,
                'color' => 'Orange',
                'stock' => 55,
                'variants' => [
                    ['color' => 'orange silver', 'stock' => 25],
                    ['color' => 'orange polos', 'stock' => 20],
                    ['color' => 'kuning silver', 'stock' => 10]
                ]
            ],
            [
                'name' => 'Terpal Plastik A7 LKL',
                'price' => 7000,
                'color' => 'Orange',
                'stock' => 50,
                'variants' => [
                    ['color' => 'orange silver', 'stock' => 25],
                    ['color' => 'orange polos', 'stock' => 15],
                    ['color' => 'kuning silver', 'stock' => 10]
                ]
            ],
            [
                'name' => 'Terpal Plastik A8 LKL',
                'price' => 8500,
                'color' => 'Kuning',
                'stock' => 45,
                'variants' => [
                    ['color' => 'kuning silver', 'stock' => 25],
                    ['color' => 'kuning polos', 'stock' => 15],
                    ['color' => 'hijau silver', 'stock' => 5]
                ]
            ],
            [
                'name' => 'Terpal Plastik A8 SKR',
                'price' => 9500,
                'color' => 'Kuning',
                'stock' => 40,
                'variants' => [
                    ['color' => 'kuning silver', 'stock' => 20],
                    ['color' => 'kuning polos', 'stock' => 12],
                    ['color' => 'hijau silver', 'stock' => 8]
                ]
            ],
            [
                'name' => 'Terpal Plastik A10 SKR',
                'price' => 12000,
                'color' => 'Ungu',
                'stock' => 35,
                'variants' => [
                    ['color' => 'ungu silver', 'stock' => 18],
                    ['color' => 'ungu polos', 'stock' => 10],
                    ['color' => 'biru silver', 'stock' => 7]
                ]
            ],
            [
                'name' => 'Terpal Plastik A12 SKR',
                'price' => 12500,
                'color' => 'Ungu',
                'stock' => 30,
                'variants' => [
                    ['color' => 'ungu silver', 'stock' => 15],
                    ['color' => 'ungu polos', 'stock' => 10],
                    ['color' => 'biru silver', 'stock' => 5]
                ]
            ],
            [
                'name' => 'Terpal Plastik A12 KOR',
                'price' => 10500,
                'color' => 'Ungu',
                'stock' => 28,
                'variants' => [
                    ['color' => 'ungu silver', 'stock' => 14],
                    ['color' => 'ungu polos', 'stock' => 9],
                    ['color' => 'biru silver', 'stock' => 5]
                ]
            ],
            [
                'name' => 'Terpal Plastik A12 LKL',
                'price' => 10500,
                'color' => 'Ungu',
                'stock' => 25,
                'variants' => [
                    ['color' => 'ungu silver', 'stock' => 12],
                    ['color' => 'ungu polos', 'stock' => 8],
                    ['color' => 'biru silver', 'stock' => 5]
                ]
            ],
            [
                'name' => 'Terpal Plastik A15 SKR',
                'price' => 13000,
                'color' => 'Coklat',
                'stock' => 22,
                'variants' => [
                    ['color' => 'coklat silver', 'stock' => 12],
                    ['color' => 'coklat polos', 'stock' => 7],
                    ['color' => 'hitam silver', 'stock' => 3]
                ]
            ],
            [
                'name' => 'Terpal Plastik A15 LKL',
                'price' => 12000,
                'color' => 'Coklat',
                'stock' => 20,
                'variants' => [
                    ['color' => 'coklat silver', 'stock' => 10],
                    ['color' => 'coklat polos', 'stock' => 6],
                    ['color' => 'hitam silver', 'stock' => 4]
                ]
            ],
            [
                'name' => 'Terpal Plastik A17 SKR',
                'price' => 14000,
                'color' => 'Hitam',
                'stock' => 18,
                'variants' => [
                    ['color' => 'hitam silver', 'stock' => 9],
                    ['color' => 'hitam polos', 'stock' => 6],
                    ['color' => 'abu silver', 'stock' => 3]
                ]
            ],
            [
                'name' => 'Terpal Plastik A17 KOR',
                'price' => 13500,
                'color' => 'Hitam',
                'stock' => 16,
                'variants' => [
                    ['color' => 'hitam silver', 'stock' => 8],
                    ['color' => 'hitam polos', 'stock' => 5],
                    ['color' => 'abu silver', 'stock' => 3]
                ]
            ],
            [
                'name' => 'Terpal Plastik A20 SKR PELANGI',
                'price' => 17000,
                'color' => 'Pelangi',
                'stock' => 15,
                'variants' => [
                    ['color' => 'pelangi silver', 'stock' => 8],
                    ['color' => 'pelangi polos', 'stock' => 4],
                    ['color' => 'pelangi metalik', 'stock' => 3]
                ]
            ],
            [
                'name' => 'Terpal Plastik A20 KOR PELANGI',
                'price' => 16000,
                'color' => 'Pelangi',
                'stock' => 14,
                'variants' => [
                    ['color' => 'pelangi silver', 'stock' => 7],
                    ['color' => 'pelangi polos', 'stock' => 4],
                    ['color' => 'pelangi metalik', 'stock' => 3]
                ]
            ],
            [
                'name' => 'Terpal Plastik A20 UV/KOR SPR ORC',
                'price' => 18500,
                'color' => 'Silver',
                'stock' => 12,
                'variants' => [
                    ['color' => 'silver metalik', 'stock' => 6],
                    ['color' => 'silver matte', 'stock' => 4],
                    ['color' => 'silver reflektif', 'stock' => 2]
                ]
            ],
            [
                'name' => 'Terpal Plastik A20 KOR RD',
                'price' => 16500,
                'color' => 'Merah',
                'stock' => 10,
                'variants' => [
                    ['color' => 'merah silver', 'stock' => 5],
                    ['color' => 'merah polos', 'stock' => 3],
                    ['color' => 'merah metalik', 'stock' => 2]
                ]
            ],
            [
                'name' => 'Terpal Plastik A20 SKR',
                'price' => 16500,
                'color' => 'Hitam',
                'stock' => 8,
                'variants' => [
                    ['color' => 'hitam metalik', 'stock' => 4],
                    ['color' => 'hitam matte', 'stock' => 3],
                    ['color' => 'hitam reflektif', 'stock' => 1]
                ]
            ],

            // TERPAL KARET
            [
                'name' => 'Terpal Karet Ulin DD/SP/KOS',
                'price' => 25000,
                'color' => 'Coklat',
                'stock' => 15,
                'variants' => [
                    ['color' => 'coklat karet', 'stock' => 8],
                    ['color' => 'hitam karet', 'stock' => 4],
                    ['color' => 'abu karet', 'stock' => 3]
                ]
            ],
            [
                'name' => 'Terpal Karet Orchid',
                'price' => 27000,
                'color' => 'Merah',
                'stock' => 12,
                'variants' => [
                    ['color' => 'merah karet', 'stock' => 6],
                    ['color' => 'orange karet', 'stock' => 4],
                    ['color' => 'kuning karet', 'stock' => 2]
                ]
            ],
            [
                'name' => 'Terpal Karet Samhe',
                'price' => 28000,
                'color' => 'Hijau',
                'stock' => 10,
                'variants' => [
                    ['color' => 'hijau karet', 'stock' => 5],
                    ['color' => 'biru karet', 'stock' => 3],
                    ['color' => 'abu karet', 'stock' => 2]
                ]
            ],

            // TERPAL KAIN (CANVAS)
            [
                'name' => 'Terpal Kain JP',
                'price' => 90000,
                'color' => 'Coklat',
                'stock' => 8,
                'variants' => [
                    ['color' => 'coklat canvas', 'stock' => 4],
                    ['color' => 'hitam canvas', 'stock' => 2],
                    ['color' => 'abu canvas', 'stock' => 2]
                ]
            ],
            [
                'name' => 'Terpal Kain SUPER',
                'price' => 100000,
                'color' => 'Biru',
                'stock' => 6,
                'variants' => [
                    ['color' => 'biru canvas', 'stock' => 3],
                    ['color' => 'hijau canvas', 'stock' => 2],
                    ['color' => 'abu canvas', 'stock' => 1]
                ]
            ],
            [
                'name' => 'Terpal Kain KEEP JEP',
                'price' => 70000,
                'color' => 'Hijau',
                'stock' => 10,
                'variants' => [
                    ['color' => 'hijau canvas', 'stock' => 5],
                    ['color' => 'biru canvas', 'stock' => 3],
                    ['color' => 'abu canvas', 'stock' => 2]
                ]
            ]
        ];

        // Create custom materials
        foreach ($materials as $materialData) {
            $variants = $materialData['variants'];
            unset($materialData['variants']);

            $customMaterial = CustomMaterial::create($materialData);

            // Create variants for each material
            foreach ($variants as $variantData) {
                CustomMaterialVariant::create([
                    'custom_material_id' => $customMaterial->id,
                    'color' => $variantData['color'],
                    'stock' => $variantData['stock'],
                ]);
            }
        }

        $this->command->info('Custom materials seeded successfully!');
        $this->command->info('Created ' . count($materials) . ' custom materials with variants.');
    }
}