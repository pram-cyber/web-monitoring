<?php

namespace App\Http\Controllers;

use App\Models\TrashBin;
use App\Models\SensorLog;
use App\Models\Report;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $bins            = TrashBin::all();
        $totalBins       = $bins->count();
        $criticalBins    = $bins->where('percentage', '>', 85)->count();
        $normalBins      = $bins->whereBetween('percentage', [25, 85])->count();
        $avgFill         = round($bins->avg('percentage') ?? 0);
        $activeBins      = $bins->where('is_active', true)->count();
        $maintenanceBins = $bins->where('is_active', false)->count();

        if (request()->ajax() || request()->has('api')) {
            return response()->json([
                'status' => 'success',
                'totalBins' => $totalBins,
                'criticalBins' => $criticalBins,
                'normalBins' => $normalBins,
                'avgFill' => $avgFill,
                'activeBins' => $activeBins,
                'maintenanceBins' => $maintenanceBins,
                'bins' => $bins
            ]);
        }

        // Weekly
        $weeklyLabels = [];
        $weeklyData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $weeklyLabels[] = $date->format('D');
            $weeklyData[]   = round(SensorLog::whereDate('created_at', $date)->avg('percentage') ?? 0);
        }

        // Monthly
        $monthlyLabels = [];
        $monthlyData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $date->format('M');
            $monthlyData[]   = round(
                SensorLog::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->avg('percentage') ?? 0
            );
        }

        return view('admin.dashboard', compact(
            'bins', 'totalBins', 'criticalBins', 'normalBins',
            'avgFill', 'activeBins', 'maintenanceBins',
            'weeklyLabels', 'weeklyData', 'monthlyLabels', 'monthlyData'
        ));
    }

    public function userDashboard()
    {
        $bins         = TrashBin::all();
        $totalBins    = $bins->count();
        $criticalBins = $bins->where('percentage', '>', 85)->count();
        $myReports    = Report::where('user_id', auth()->id())->count();

        if (request()->ajax() || request()->has('api')) {
            return response()->json([
                'status' => 'success',
                'totalBins' => $totalBins,
                'criticalBins' => $criticalBins,
                'myReports' => $myReports,
                'bins' => $bins
            ]);
        }

        return view('user.dashboard', compact(
            'bins', 'totalBins', 'criticalBins', 'myReports'
        ));
    }
}