<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Rental;

class RentalDashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedType = $request->query('type', 'All');
        $validTypes = ['PS2', 'PS3', 'PS4', 'Nintendo Switch', 'TV 32 Inch'];

        // Get count stats
        $stats = [
            'total' => Unit::count(),
            'ada' => Unit::where('status', 'ada')->count(),
            'disewa' => Unit::where('status', 'disewa')->count(),
            'maintenance' => Unit::where('status', 'maintenance')->count(),
        ];

        // Group stats per sheet type for visual summary
        $typeStats = [];
        foreach ($validTypes as $t) {
            $typeStats[$t] = [
                'total' => Unit::where('type', $t)->count(),
                'ada' => Unit::where('type', $t)->where('status', 'ada')->count(),
                'disewa' => Unit::where('type', $t)->where('status', 'disewa')->count(),
                'maintenance' => Unit::where('type', $t)->where('status', 'maintenance')->count(),
            ];
        }

        // Fetch units for current "sheet"
        $query = Unit::with('activeRental');
        if (in_array($selectedType, $validTypes)) {
            $query->where('type', $selectedType);
        }
        $units = $query->get();

        return view('rental.dashboard', compact('units', 'selectedType', 'validTypes', 'stats', 'typeStats'));
    }

    public function history(Request $request)
    {
        $rentals = Rental::with('unit')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('rental.history', compact('rentals'));
    }
}
