<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan Utama', 'slug' => 'makanan-utama', 'icon' => '🍛', 'order' => 1],
            ['name' => 'Minuman', 'slug' => 'minuman', 'icon' => '🥤', 'order' => 2],
            ['name' => 'Dessert', 'slug' => 'dessert', 'icon' => '🍰', 'order' => 3],
            ['name' => 'Camilan', 'slug' => 'camilan', 'icon' => '🍟', 'order' => 4],
            ['name' => 'Paket Hemat', 'slug' => 'paket-hemat', 'icon' => '🎁', 'order' => 5],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
