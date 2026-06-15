<?php

namespace Database\Seeders;

use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [];

        // 8 Indoor tables
        for ($i = 1; $i <= 8; $i++) {
            $tables[] = [
                'number' => $i,
                'capacity' => $i <= 4 ? 2 : 4,
                'location' => 'indoor',
                'status' => 'available',
                'qr_token' => Str::uuid()->toString(),
            ];
        }

        // 2 Outdoor tables
        for ($i = 9; $i <= 10; $i++) {
            $tables[] = [
                'number' => $i,
                'capacity' => 4,
                'location' => 'outdoor',
                'status' => 'available',
                'qr_token' => Str::uuid()->toString(),
            ];
        }

        // 2 VIP tables
        for ($i = 11; $i <= 12; $i++) {
            $tables[] = [
                'number' => $i,
                'capacity' => 8,
                'location' => 'vip',
                'status' => 'available',
                'qr_token' => Str::uuid()->toString(),
            ];
        }

        foreach ($tables as $table) {
            RestaurantTable::create($table);
        }
    }
}
