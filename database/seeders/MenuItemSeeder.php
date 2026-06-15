<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Makanan Utama (category_id = 1)
            [
                'category_id' => 1, 'name' => 'Nasi Goreng Spesial',
                'slug' => 'nasi-goreng-spesial',
                'description' => 'Nasi goreng dengan telur mata sapi, ayam suwir, kerupuk, dan acar segar. Dimasak dengan bumbu rahasia.',
                'price' => 35000, 'labels' => ['best_seller', 'halal'], 'estimated_time' => 15,
            ],
            [
                'category_id' => 1, 'name' => 'Ayam Bakar Madu',
                'slug' => 'ayam-bakar-madu',
                'description' => 'Ayam kampung bakar dengan olesan madu pilihan, disajikan dengan sambal matah dan lalapan.',
                'price' => 55000, 'labels' => ['best_seller', 'halal'], 'estimated_time' => 25,
            ],
            [
                'category_id' => 1, 'name' => 'Rendang Sapi',
                'slug' => 'rendang-sapi',
                'description' => 'Rendang daging sapi empuk dengan santan dan rempah khas Padang. Disajikan dengan nasi putih hangat.',
                'price' => 65000, 'labels' => ['recommended', 'halal', 'spicy'], 'estimated_time' => 20,
            ],
            [
                'category_id' => 1, 'name' => 'Mie Goreng Jawa',
                'slug' => 'mie-goreng-jawa',
                'description' => 'Mie goreng khas Jawa dengan sayuran segar, telur, dan bumbu kecap manis.',
                'price' => 30000, 'labels' => ['vegetarian', 'halal'], 'estimated_time' => 15,
            ],
            [
                'category_id' => 1, 'name' => 'Sate Ayam Madura',
                'slug' => 'sate-ayam-madura',
                'description' => '10 tusuk sate ayam dengan bumbu kacang khas Madura, lontong, dan bawang merah.',
                'price' => 40000, 'labels' => ['best_seller', 'halal'], 'estimated_time' => 20,
            ],
            [
                'category_id' => 1, 'name' => 'Ikan Gurame Bakar',
                'slug' => 'ikan-gurame-bakar',
                'description' => 'Ikan gurame segar dibakar dengan bumbu kecap dan disajikan dengan sambal khas.',
                'price' => 85000, 'labels' => ['recommended', 'halal'], 'estimated_time' => 30,
            ],
            [
                'category_id' => 1, 'name' => 'Sop Buntut',
                'slug' => 'sop-buntut',
                'description' => 'Sop buntut sapi dengan kuah bening rempah, wortel, kentang, dan tomat.',
                'price' => 75000, 'labels' => ['halal'], 'estimated_time' => 25,
            ],
            [
                'category_id' => 1, 'name' => 'Gado-Gado Jakarta',
                'slug' => 'gado-gado-jakarta',
                'description' => 'Sayuran segar dengan bumbu kacang, lontong, tahu, tempe, dan kerupuk.',
                'price' => 28000, 'labels' => ['vegetarian', 'halal'], 'estimated_time' => 10,
            ],

            // Minuman (category_id = 2)
            [
                'category_id' => 2, 'name' => 'Es Teh Manis',
                'slug' => 'es-teh-manis',
                'description' => 'Teh manis segar dengan es batu, diseduh dari teh pilihan.',
                'price' => 8000, 'labels' => ['halal'], 'estimated_time' => 3,
            ],
            [
                'category_id' => 2, 'name' => 'Jus Alpukat',
                'slug' => 'jus-alpukat',
                'description' => 'Jus alpukat segar dengan susu coklat dan gula aren.',
                'price' => 20000, 'labels' => ['best_seller', 'halal'], 'estimated_time' => 5,
            ],
            [
                'category_id' => 2, 'name' => 'Es Jeruk Peras',
                'slug' => 'es-jeruk-peras',
                'description' => 'Jeruk peras segar dengan es batu dan sedikit madu.',
                'price' => 15000, 'labels' => ['halal'], 'estimated_time' => 3,
            ],
            [
                'category_id' => 2, 'name' => 'Kopi Susu Gula Aren',
                'slug' => 'kopi-susu-gula-aren',
                'description' => 'Kopi robusta pilihan dengan susu segar dan gula aren asli.',
                'price' => 25000, 'labels' => ['best_seller', 'halal'], 'estimated_time' => 5,
            ],

            // Dessert (category_id = 3)
            [
                'category_id' => 3, 'name' => 'Es Cendol',
                'slug' => 'es-cendol',
                'description' => 'Cendol pandan dengan santan, gula merah, dan es serut.',
                'price' => 18000, 'labels' => ['best_seller', 'halal'], 'estimated_time' => 5,
            ],
            [
                'category_id' => 3, 'name' => 'Pisang Goreng Keju',
                'slug' => 'pisang-goreng-keju',
                'description' => 'Pisang raja goreng crispy dengan taburan keju dan susu kental manis.',
                'price' => 22000, 'labels' => ['recommended', 'halal'], 'estimated_time' => 10,
            ],
            [
                'category_id' => 3, 'name' => 'Klepon',
                'slug' => 'klepon',
                'description' => 'Bola-bola ketan pandan berisi gula merah dengan taburan kelapa parut. Isi 6 pcs.',
                'price' => 15000, 'labels' => ['vegetarian', 'halal'], 'estimated_time' => 5,
            ],

            // Camilan (category_id = 4)
            [
                'category_id' => 4, 'name' => 'Tahu Crispy',
                'slug' => 'tahu-crispy',
                'description' => 'Tahu sutra goreng crispy dengan sambal kecap dan daun bawang.',
                'price' => 18000, 'labels' => ['vegetarian', 'halal'], 'estimated_time' => 10,
            ],
            [
                'category_id' => 4, 'name' => 'Lumpia Semarang',
                'slug' => 'lumpia-semarang',
                'description' => 'Lumpia goreng isi rebung dan udang khas Semarang. Isi 4 pcs.',
                'price' => 25000, 'labels' => ['halal'], 'estimated_time' => 10,
            ],
            [
                'category_id' => 4, 'name' => 'Kentang Goreng',
                'slug' => 'kentang-goreng',
                'description' => 'Kentang goreng crispy dengan saus sambal mayo.',
                'price' => 20000, 'labels' => ['vegetarian', 'halal'], 'estimated_time' => 10,
            ],

            // Paket Hemat (category_id = 5)
            [
                'category_id' => 5, 'name' => 'Paket Nasi Goreng Komplit',
                'slug' => 'paket-nasi-goreng-komplit',
                'description' => 'Nasi goreng spesial + Es Teh Manis + Kerupuk. Hemat 15%!',
                'price' => 38000, 'labels' => ['recommended', 'halal'], 'estimated_time' => 15,
            ],
            [
                'category_id' => 5, 'name' => 'Paket Ayam Bakar Hemat',
                'slug' => 'paket-ayam-bakar-hemat',
                'description' => 'Ayam Bakar Madu + Nasi Putih + Es Jeruk + Kerupuk. Hemat 20%!',
                'price' => 62000, 'labels' => ['best_seller', 'halal'], 'estimated_time' => 25,
            ],
        ];

        foreach ($items as $item) {
            MenuItem::create($item);
        }
    }
}
