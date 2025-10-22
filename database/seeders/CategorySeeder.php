<?php

namespace Database\Seeders;

use App\Models\Categories;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        Categories::query()->delete();

        $categories = [
            [
                'name' => 'Terpal Plastik',
                'is_active' => true,
            ],
            [
                'name' => 'Terpal Kain',
                'is_active' => true,
            ],
            [
                'name' => 'Terpal Karet',
                'is_active' => true,
            ]
        ];

        foreach ($categories as $category) {
            Categories::create($category);
        }

        $this->command->info('Categories seeded successfully!');
        $this->command->info('Created ' . count($categories) . ' categories.');
    }
}