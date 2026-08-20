<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Customer;
use App\Models\Rental;
use Carbon\Carbon;

class AbdillansGamingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Units: 30 PS3, 30 PS4, 10 PS5
        $unitsData = [];

        // 30 units of PS 3
        for ($i = 1; $i <= 30; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $unitsData[] = [
                'code' => 'PS3-' . $num,
                'name' => 'Console PS3 #' . $num,
                'type' => 'PS 3',
                'status' => 'ada',
                'price_per_hour' => 8000.00,
                'notes' => 'PS 3 Slim standard setup',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 30 units of PS 4
        for ($i = 1; $i <= 30; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $unitsData[] = [
                'code' => 'PS4-' . $num,
                'name' => 'Console PS4 #' . $num,
                'type' => 'PS 4',
                'status' => 'ada',
                'price_per_hour' => 12000.00,
                'notes' => 'PS 4 Pro HDR setup',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 10 units of PS 5
        for ($i = 1; $i <= 10; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $unitsData[] = [
                'code' => 'PS5-' . $num,
                'name' => 'Console PS5 #' . $num,
                'type' => 'PS 5',
                'status' => 'ada',
                'price_per_hour' => 20000.00,
                'notes' => 'PS 5 4K 120Hz OLED Setup',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Unit::insert($unitsData);

        // 2. Seed Customers
        $customers = [
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'nik_ktp' => '3171012345670001',
                'address' => 'Jl. Merdeka No. 12, Jakarta',
                'notes' => 'Pelanggan Setia / Member Gold',
            ],
            [
                'name' => 'Rian Ardianto',
                'phone' => '085711223344',
                'nik_ktp' => '3171012345670002',
                'address' => 'Jl. Mawar No. 45, Jakarta',
                'notes' => 'Sering main FIFA & eFootball',
            ],
            [
                'name' => 'Dimas Pratama',
                'phone' => '089699887766',
                'nik_ktp' => '3171012345670003',
                'address' => 'Jl. Kebon Jeruk No. 8, Jakarta',
                'notes' => 'Sewa mingguan',
            ],
            [
                'name' => 'Eko Wijaya',
                'phone' => '082155443322',
                'nik_ktp' => '3171012345670004',
                'address' => 'Jl. Melati No. 99, Jakarta',
                'notes' => 'Pelanggan baru',
            ],
            [
                'name' => 'Fajar Nugroho',
                'phone' => '083812344321',
                'nik_ktp' => '3171012345670005',
                'address' => 'Jl. Sudirman No. 101, Jakarta',
                'notes' => 'Turnamen lokal',
            ],
        ];

        foreach ($customers as $c) {
            Customer::create($c);
        }

        // 3. Seed Active & Historical Rentals (Daily, Weekly, Monthly, Yearly)
        $customerList = Customer::all();
        $ps3Units = Unit::where('type', 'PS 3')->get();
        $ps4Units = Unit::where('type', 'PS 4')->get();
        $ps5Units = Unit::where('type', 'PS 5')->get();

        // Active rentals right now (e.g. 3 active rentals to demonstrate timer)
        $activeUnit1 = $ps3Units[0]; // PS3-01
        $activeUnit1->update(['status' => 'disewa']);
        Rental::create([
            'unit_id' => $activeUnit1->id,
            'customer_id' => $customerList[0]->id,
            'customer_name' => $customerList[0]->name,
            'customer_phone' => $customerList[0]->phone,
            'start_time' => now()->subHours(1),
            'end_time' => now()->addHours(2),
            'duration_hours' => 3,
            'price_per_hour' => $activeUnit1->price_per_hour,
            'total_price' => $activeUnit1->price_per_hour * 3,
            'payment_method' => 'QRIS',
            'payment_status' => 'Lunas',
            'status' => 'active',
            'notes' => 'Main paket 3 jam',
        ]);

        $activeUnit2 = $ps4Units[0]; // PS4-01
        $activeUnit2->update(['status' => 'disewa']);
        Rental::create([
            'unit_id' => $activeUnit2->id,
            'customer_id' => $customerList[1]->id,
            'customer_name' => $customerList[1]->name,
            'customer_phone' => $customerList[1]->phone,
            'start_time' => now()->subMinutes(30),
            'end_time' => now()->addHours(1)->addMinutes(30),
            'duration_hours' => 2,
            'price_per_hour' => $activeUnit2->price_per_hour,
            'total_price' => $activeUnit2->price_per_hour * 2,
            'payment_method' => 'Cash',
            'payment_status' => 'Lunas',
            'status' => 'active',
            'notes' => 'Main eFootball 2024',
        ]);

        $activeUnit3 = $ps4Units[1]; // PS4-02
        $activeUnit3->update(['status' => 'disewa']);
        Rental::create([
            'unit_id' => $activeUnit3->id,
            'customer_id' => $customerList[2]->id,
            'customer_name' => $customerList[2]->name,
            'customer_phone' => $customerList[2]->phone,
            'start_time' => now()->subHours(3)->subMinutes(45),
            'end_time' => now()->addMinutes(15), // 15 mins remaining!
            'duration_hours' => 4,
            'price_per_hour' => $activeUnit3->price_per_hour,
            'total_price' => $activeUnit3->price_per_hour * 4,
            'payment_method' => 'Transfer',
            'payment_status' => 'Lunas',
            'status' => 'active',
            'notes' => 'Hampir selesai',
        ]);

        // Historical completed rentals across past days, weeks, months, years
        $sampleHistoryDates = [
            now()->subHours(5),
            now()->subDays(1),
            now()->subDays(2),
            now()->subDays(4),
            now()->subDays(6),
            now()->subWeeks(2),
            now()->subWeeks(3),
            now()->subMonths(1),
            now()->subMonths(2),
            now()->subMonths(5),
            now()->subYears(1),
        ];

        foreach ($sampleHistoryDates as $idx => $date) {
            $cust = $customerList[$idx % count($customerList)];
            $unit = ($idx % 2 == 0) ? $ps4Units[($idx + 2) % count($ps4Units)] : $ps3Units[($idx + 3) % count($ps3Units)];
            $duration = ($idx % 3) + 2; // 2, 3, or 4 hours

            Rental::create([
                'unit_id' => $unit->id,
                'customer_id' => $cust->id,
                'customer_name' => $cust->name,
                'customer_phone' => $cust->phone,
                'start_time' => Carbon::parse($date),
                'end_time' => Carbon::parse($date)->addHours($duration),
                'duration_hours' => $duration,
                'price_per_hour' => $unit->price_per_hour,
                'total_price' => $unit->price_per_hour * $duration,
                'payment_method' => ($idx % 2 == 0) ? 'Cash' : 'QRIS',
                'payment_status' => 'Lunas',
                'status' => 'completed',
                'created_at' => Carbon::parse($date),
                'updated_at' => Carbon::parse($date)->addHours($duration),
            ]);
        }
    }
}
