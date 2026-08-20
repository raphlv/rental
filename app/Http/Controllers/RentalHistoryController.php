<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RentalHistoryController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'daily'); // 'daily', 'weekly', 'monthly', 'yearly'
        $selectedDate = $request->query('date', now()->format('Y-m-d'));
        $selectedMonth = $request->query('month', now()->format('Y-m'));
        $selectedYear = $request->query('year', now()->format('Y'));

        $query = Rental::with(['unit', 'customer']);

        // Apply period filter logic
        if ($period === 'daily') {
            $date = Carbon::parse($selectedDate);
            $query->whereDate('start_time', $date);
            $filterLabel = 'Hari: ' . $date->isoFormat('DD MMMM YYYY');
        } elseif ($period === 'weekly') {
            $date = Carbon::parse($selectedDate);
            $startOfWeek = $date->copy()->startOfWeek();
            $endOfWeek = $date->copy()->endOfWeek();
            $query->whereBetween('start_time', [$startOfWeek, $endOfWeek]);
            $filterLabel = 'Minggu: ' . $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y');
        } elseif ($period === 'monthly') {
            $date = Carbon::parse($selectedMonth . '-01');
            $query->whereYear('start_time', $date->year)
                  ->whereMonth('start_time', $date->month);
            $filterLabel = 'Bulan: ' . $date->isoFormat('MMMM YYYY');
        } elseif ($period === 'yearly') {
            $query->whereYear('start_time', $selectedYear);
            $filterLabel = 'Tahun: ' . $selectedYear;
        }

        // Clone query for computing summary statistics
        $statsQuery = clone $query;
        $totalRentals = $statsQuery->count();
        $totalHours = $statsQuery->sum('duration_hours');
        $totalRevenue = $statsQuery->sum('total_price');

        // Most popular PS console type in selected period
        $popularConsole = DB::table('rentals')
            ->join('units', 'rentals.unit_id', '=', 'units.id')
            ->select('units.type', DB::raw('count(*) as count'))
            ->when($period === 'daily', function ($q) use ($selectedDate) {
                return $q->whereDate('start_time', $selectedDate);
            })
            ->when($period === 'weekly', function ($q) use ($selectedDate) {
                $d = Carbon::parse($selectedDate);
                return $q->whereBetween('start_time', [$d->copy()->startOfWeek(), $d->copy()->endOfWeek()]);
            })
            ->when($period === 'monthly', function ($q) use ($selectedMonth) {
                $d = Carbon::parse($selectedMonth . '-01');
                return $q->whereYear('start_time', $d->year)->whereMonth('start_time', $d->month);
            })
            ->when($period === 'yearly', function ($q) use ($selectedYear) {
                return $q->whereYear('start_time', $selectedYear);
            })
            ->groupBy('units.type')
            ->orderBy('count', 'desc')
            ->first();

        $topConsoleName = $popularConsole ? $popularConsole->type : '-';

        $rentals = $query->orderBy('start_time', 'desc')->paginate(15);

        return view('rental.history.index', compact(
            'rentals',
            'period',
            'selectedDate',
            'selectedMonth',
            'selectedYear',
            'filterLabel',
            'totalRentals',
            'totalHours',
            'totalRevenue',
            'topConsoleName'
        ));
    }
}
