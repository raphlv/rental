<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultUnits = [
            // PS2
            ['name' => 'PS2 - Unit 01', 'type' => 'PS2', 'status' => 'ada', 'price_per_hour' => 3000.00],
            ['name' => 'PS2 - Unit 02', 'type' => 'PS2', 'status' => 'ada', 'price_per_hour' => 3000.00],
            
            // PS3
            ['name' => 'PS3 - Unit 01', 'type' => 'PS3', 'status' => 'ada', 'price_per_hour' => 5000.00],
            ['name' => 'PS3 - Unit 02', 'type' => 'PS3', 'status' => 'ada', 'price_per_hour' => 5000.00],
            ['name' => 'PS3 - Unit 03', 'type' => 'PS3', 'status' => 'ada', 'price_per_hour' => 5000.00],
            
            // PS4
            ['name' => 'PS4 - Unit 01', 'type' => 'PS4', 'status' => 'ada', 'price_per_hour' => 8000.00],
            ['name' => 'PS4 - Unit 02', 'type' => 'PS4', 'status' => 'ada', 'price_per_hour' => 8000.00],
            ['name' => 'PS4 - Unit 03', 'type' => 'PS4', 'status' => 'ada', 'price_per_hour' => 8000.00],
            
            // Nintendo Switch
            ['name' => 'Switch - Unit 01', 'type' => 'Nintendo Switch', 'status' => 'ada', 'price_per_hour' => 10000.00],
            ['name' => 'Switch - Unit 02', 'type' => 'Nintendo Switch', 'status' => 'ada', 'price_per_hour' => 10000.00],
            
            // TV 32 Inch
            ['name' => 'TV 32" - Unit 01', 'type' => 'TV 32 Inch', 'status' => 'ada', 'price_per_hour' => 4000.00],
            ['name' => 'TV 32" - Unit 02', 'type' => 'TV 32 Inch', 'status' => 'ada', 'price_per_hour' => 4000.00],
        ];

        foreach ($defaultUnits as $unit) {
            Unit::updateOrCreate(
                ['name' => $unit['name']],
                $unit
            );
        }
    }
}
