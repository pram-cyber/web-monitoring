<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TrashBin;
use App\Models\SensorLog;
use App\Models\Report;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function nearby()
    {
        $bins = TrashBin::all();
        return view('user.nearby', compact('bins'));
    }

    public function history()
    {
        $histories = SensorLog::with('trashBin')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('user.history', compact('histories'));
    }

    public function reports()
    {
        $bins      = TrashBin::all();
        $myReports = Report::with('trashBin')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('user.reports', compact('bins', 'myReports'));
    }

    public function storeReport(Request $request)
    {
        $request->validate([
            'trash_bin_id' => 'required|exists:trash_bins,id',
            'type'         => 'required',
            'description'  => 'required',
        ]);
        Report::create([
            'user_id'      => auth()->id(),
            'trash_bin_id' => $request->trash_bin_id,
            'type'         => $request->type,
            'description'  => $request->description,
        ]);
        return back()->with('success', 'Laporan berhasil dikirim!');
    }

    public function updateSettings(Request $request)
    {
        auth()->user()->update([
            'notification_radius' => $request->notification_radius,
        ]);
        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }

    public function markEmpty($id)
    {
        $bin = TrashBin::findOrFail($id);

        // Simpan log pengambilan
        SensorLog::create([
            'trash_bin_id' => $bin->id,
            'distance_cm'  => $bin->max_depth_cm,
            'percentage'   => 0,
            'status'       => 'empty',
            'latitude'     => $bin->latitude,
            'longitude'    => $bin->longitude,
            'recorded_at'  => now(),
        ]);

        // Update status bin
        $bin->update([
            'percentage'  => 0,
            'status'      => 'empty',
            'distance_cm' => $bin->max_depth_cm,
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Tong sampah berhasil ditandai sudah diambil!'
        ]);
    }
}