<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrashBin;
use App\Models\SensorLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Report;

class AdminController extends Controller
{
    public function history()
    {
        $bins = TrashBin::all();
        
        // Hanya ambil log yang merupakan EVENT RESMI (is_history = true)
        $histories = SensorLog::with('trashBin')
            ->where('is_history', true)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $fullToday  = SensorLog::whereDate('created_at', today())
            ->where('is_history', true)
            ->where('percentage', '>=', 90)
            ->count();
            
        $takenToday = SensorLog::whereDate('created_at', today())
            ->where('is_history', true)
            ->where('percentage', '<', 10)
            ->count();
            
        $fullWeek   = SensorLog::whereBetween('created_at', [now()->startOfWeek(), now()])
            ->where('is_history', true)
            ->where('percentage', '>=', 90)
            ->count();

        $mostFull = SensorLog::with('trashBin')
            ->where('is_history', true)
            ->where('percentage', '>=', 90)
            ->selectRaw('trash_bin_id, count(*) as total')
            ->groupBy('trash_bin_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.history', compact(
            'bins', 'histories', 'fullToday', 'takenToday', 'fullWeek', 'mostFull'
        ));
    }



    public function sensorLogs(Request $request)
    {
        $query = SensorLog::with('trashBin')->orderBy('created_at', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.sensor-logs', compact('logs'));
    }

    public function destroySensorLog($id)
    {
        SensorLog::findOrFail($id)->delete();
        return back()->with('success', 'Log sensor berhasil dihapus!');
    }

    public function clearSensorLogs()
    {
        SensorLog::truncate();
        return back()->with('success', 'Semua riwayat log sensor berhasil dikosongkan!');
    }

    public function trends()
    {
        $bins = TrashBin::all();

        // Weekly
        $weeklyLabels = [];
        $weeklyData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $weeklyLabels[] = $date->format('D, d M');
            $weeklyData[]   = round(SensorLog::whereDate('created_at', $date)->avg('percentage') ?? 0);
        }

        // Monthly
        $monthlyLabels = [];
        $monthlyData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');
            $monthlyData[]   = round(
                SensorLog::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->avg('percentage') ?? 0
            );
        }

        return view('admin.trends', compact(
            'bins', 'weeklyLabels', 'weeklyData', 'monthlyLabels', 'monthlyData'
        ));
    }
    public function reports()
{
    $reports  = Report::with(['user', 'trashBin'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
    $pending  = Report::where('status', 'pending')->count();
    $resolved = Report::where('status', 'resolved')->count();

    return view('admin.reports', compact('reports', 'pending', 'resolved'));
}

public function resolveReport($id)
{
    Report::findOrFail($id)->update(['status' => 'resolved']);
    return back()->with('success', 'Laporan ditandai selesai!');
}

public function destroyReport($id)
{
    Report::findOrFail($id)->delete();
    return back()->with('success', 'Laporan berhasil dihapus!');
}

    public function volume()
    {
        return redirect()->route('admin.dashboard');
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,user',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => $request->role,
        ]);

        return back()->with('success', 'User berhasil ditambahkan!');
    }

    public function destroyUser($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'User berhasil dihapus!');
    }

    public function toggleUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => $request->is_active]);
        return response()->json(['success' => true]);
    }
}