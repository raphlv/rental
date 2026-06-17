<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:PS2,PS3,PS4,Nintendo Switch,TV 32 Inch',
            'status' => 'required|in:ada,disewa,maintenance',
            'price_per_hour' => 'required|numeric|min:0',
        ]);

        Unit::create($request->all());

        return redirect()->back()->with('success', 'Unit berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:PS2,PS3,PS4,Nintendo Switch,TV 32 Inch',
            'status' => 'required|in:ada,disewa,maintenance',
            'price_per_hour' => 'required|numeric|min:0',
        ]);

        // If status changes to 'ada' and it had an active rental, mark that rental completed
        if ($request->status === 'ada' && $unit->status === 'disewa') {
            $activeRental = $unit->activeRental;
            if ($activeRental) {
                $activeRental->update([
                    'status' => 'completed',
                    'end_time' => now()
                ]);
            }
        }

        $unit->update($request->all());

        return redirect()->back()->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return redirect()->back()->with('success', 'Unit berhasil dihapus.');
    }

    public function reset(Request $request, $type)
    {
        $validTypes = ['PS2', 'PS3', 'PS4', 'Nintendo Switch', 'TV 32 Inch'];
        if (!in_array($type, $validTypes)) {
            return redirect()->back()->with('error', 'Kategori tidak valid.');
        }

        // Define default units for each type
        $defaults = [
            'PS2' => [
                ['name' => 'PS2 - Unit 01', 'type' => 'PS2', 'status' => 'ada', 'price_per_hour' => 3000.00],
                ['name' => 'PS2 - Unit 02', 'type' => 'PS2', 'status' => 'ada', 'price_per_hour' => 3000.00],
            ],
            'PS3' => [
                ['name' => 'PS3 - Unit 01', 'type' => 'PS3', 'status' => 'ada', 'price_per_hour' => 5000.00],
                ['name' => 'PS3 - Unit 02', 'type' => 'PS3', 'status' => 'ada', 'price_per_hour' => 5000.00],
                ['name' => 'PS3 - Unit 03', 'type' => 'PS3', 'status' => 'ada', 'price_per_hour' => 5000.00],
            ],
            'PS4' => [
                ['name' => 'PS4 - Unit 01', 'type' => 'PS4', 'status' => 'ada', 'price_per_hour' => 8000.00],
                ['name' => 'PS4 - Unit 02', 'type' => 'PS4', 'status' => 'ada', 'price_per_hour' => 8000.00],
                ['name' => 'PS4 - Unit 03', 'type' => 'PS4', 'status' => 'ada', 'price_per_hour' => 8000.00],
            ],
            'Nintendo Switch' => [
                ['name' => 'Switch - Unit 01', 'type' => 'Nintendo Switch', 'status' => 'ada', 'price_per_hour' => 10000.00],
                ['name' => 'Switch - Unit 02', 'type' => 'Nintendo Switch', 'status' => 'ada', 'price_per_hour' => 10000.00],
            ],
            'TV 32 Inch' => [
                ['name' => 'TV 32" - Unit 01', 'type' => 'TV 32 Inch', 'status' => 'ada', 'price_per_hour' => 4000.00],
                ['name' => 'TV 32" - Unit 02', 'type' => 'TV 32 Inch', 'status' => 'ada', 'price_per_hour' => 4000.00],
            ]
        ];

        // Delete all units of this type
        // Cascade delete will automatically delete rentals associated with these units
        Unit::where('type', $type)->delete();

        // Create defaults
        foreach ($defaults[$type] as $unitData) {
            Unit::create($unitData);
        }

        return redirect()->back()->with('success', "Kategori $type berhasil dikembalikan ke data default.");
    }
}
