<?php

namespace App\Http\Controllers;

use App\Models\RainfallData;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDays = RainfallData::count();
        $avgTemp = RainfallData::avg('tavg') ?? 0.0;
        $avgHumid = RainfallData::avg('rh_avg') ?? 0.0;
        $avgRain = RainfallData::avg('rr') ?? 0.0;
        
        $extremeDays = RainfallData::where('class_actual', 1)->count();
        $extremePercentage = $totalDays > 0 ? ($extremeDays / $totalDays) * 100 : 0.0;

        // Get monthly rainfall statistics for a small chart
        $monthlyStats = RainfallData::selectRaw("
                DATE_FORMAT(tanggal, '%Y-%m') as month, 
                AVG(tavg) as avg_temp, 
                AVG(rh_avg) as avg_humid, 
                SUM(rr) as total_rain,
                COUNT(CASE WHEN class_actual = 1 THEN 1 END) as extreme_count
            ")
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(12)
            ->get();

        return view('dashboard', compact(
            'totalDays', 
            'avgTemp', 
            'avgHumid', 
            'avgRain', 
            'extremeDays', 
            'extremePercentage',
            'monthlyStats'
        ));
    }
}
