<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Rental;
use App\Models\Customer;

class RentalDashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedType = $request->query('type', 'Semua');
        $validTypes = ['Semua', 'PS 3', 'PS 4', 'PS 5'];

        // Total inventory stats
        $totalUnitsCount = Unit::count();
        $availableUnitsCount = Unit::where('status', 'ada')->count();
        $rentedUnitsCount = Unit::where('status', 'disewa')->count();
        $maintenanceUnitsCount = Unit::where('status', 'maintenance')->count();

        // Breakdown stats per Console Type (PS 3: 30, PS 4: 30, PS 5: 10)
        $ps3Stats = [
            'total' => Unit::where('type', 'PS 3')->count(),
            'ada' => Unit::where('type', 'PS 3')->where('status', 'ada')->count(),
            'disewa' => Unit::where('type', 'PS 3')->where('status', 'disewa')->count(),
        ];

        $ps4Stats = [
            'total' => Unit::where('type', 'PS 4')->count(),
            'ada' => Unit::where('type', 'PS 4')->where('status', 'ada')->count(),
            'disewa' => Unit::where('type', 'PS 4')->where('status', 'disewa')->count(),
        ];

        $ps5Stats = [
            'total' => Unit::where('type', 'PS 5')->count(),
            'ada' => Unit::where('type', 'PS 5')->where('status', 'ada')->count(),
            'disewa' => Unit::where('type', 'PS 5')->where('status', 'disewa')->count(),
        ];

        // Fetch units for current sheet filter
        $query = Unit::with(['activeRental', 'activeRental.customer']);
        if ($selectedType !== 'Semua' && in_array($selectedType, ['PS 3', 'PS 4', 'PS 5'])) {
            $query->where('type', $selectedType);
        }
        $units = $query->orderBy('code', 'asc')->get();

        // Available units for quick dropdown select in modal
        $availableUnits = Unit::where('status', 'ada')->orderBy('code', 'asc')->get();

        // Customer list for fast auto-complete in modal
        $customers = Customer::orderBy('name', 'asc')->get();

        return view('rental.dashboard', compact(
            'units',
            'selectedType',
            'totalUnitsCount',
            'availableUnitsCount',
            'rentedUnitsCount',
            'maintenanceUnitsCount',
            'ps3Stats',
            'ps4Stats',
            'ps5Stats',
            'availableUnits',
            'customers'
        ));
    }
}
