<?php

namespace App\Http\Controllers;

use App\Models\SensorLog;
use App\Models\TrashBin;
use Illuminate\Http\Request;

class SensorLogController extends Controller
{
    // Ambil riwayat log per trash bin
    public function index($trashBinId)
    {
        $logs = SensorLog::where('trash_bin_id', $trashBinId)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data'   => $logs
        ]);
    }

    // Terima data dari sensor
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trash_bin_id' => 'required|exists:trash_bins,id',
            'distance_cm'  => 'required|numeric',
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'gps_accuracy' => 'nullable|numeric',
            'percentage'   => 'nullable|numeric|between:0,100',
        ]);

        $bin = TrashBin::findOrFail($validated['trash_bin_id']);
        $previousPercentage = $bin->percentage;
        $previousStatus = $bin->status;
        $previousDistance = $bin->distance_cm;
        $previousLatitude = $bin->latitude;
        $previousLongitude = $bin->longitude;

        // Hitung persentase (Gunakan persentase dari ESP32 jika dikirim agar sinkron 100% dengan LCD)
        if (isset($validated['percentage'])) {
            $percentage = floatval($validated['percentage']);
        } else {
            $percentage = (($bin->max_depth_cm - $validated['distance_cm']) / $bin->max_depth_cm) * 100;
        }
        $percentage = max(0, min(100, round($percentage)));

        // Tentukan status
        if ($percentage < 30) {
            $status = 'empty';
        } elseif ($percentage < 70) {
            $status = 'half';
        } else {
            $status = 'full';
        }

        // AMBIL LOG RIWAYAT TERAKHIR UNTUK TONG INI (is_history = true)
        $lastHistoryLog = SensorLog::where('trash_bin_id', $bin->id)
            ->where('is_history', true)
            ->orderBy('created_at', 'desc')
            ->first();
            
        $lastIsFull = $lastHistoryLog && ($lastHistoryLog->status === 'full' || $lastHistoryLog->percentage >= 90);

        // 1. OTOMATIS CATAT EVENT "PENUH" (Jika kapasitas >= 90% dan riwayat terakhir bukan "full")
        if ($percentage >= 90) {
            if (!$lastIsFull) {
                SensorLog::create([
                    'trash_bin_id' => $bin->id,
                    'distance_cm'  => $validated['distance_cm'],
                    'percentage'   => $percentage,
                    'status'       => 'full',
                    'latitude'     => $validated['latitude'],
                    'longitude'    => $validated['longitude'],
                    'is_history'   => true,
                ]);
            }
        }

        // 2. OTOMATIS CATAT EVENT "DIAMBIL" (Jika kapasitas < 5% dan riwayat terakhir "full" ATAU kapasitas sebelumnya >= 90%)
        if ($percentage < 5) {
            $wasFull = $lastIsFull || ($previousPercentage >= 90);
            if ($wasFull) {
                // Jika belum ada log penuh tercatat sebagai riwayat terakhir, mari buat log penuh buatan agar alur timelinenya bagus
                if (!$lastIsFull) {
                    SensorLog::create([
                        'trash_bin_id' => $bin->id,
                        'distance_cm'  => $previousDistance,
                        'percentage'   => $previousPercentage,
                        'status'       => 'full',
                        'latitude'     => $previousLatitude,
                        'longitude'    => $previousLongitude,
                        'is_history'   => true,
                        'created_at'   => now()->subSeconds(2),
                    ]);
                }

                // Catat event "Diambil/Kosong" ke riwayat resmi
                SensorLog::create([
                    'trash_bin_id' => $bin->id,
                    'distance_cm'  => $validated['distance_cm'],
                    'percentage'   => $percentage,
                    'status'       => 'empty',
                    'latitude'     => $validated['latitude'],
                    'longitude'    => $validated['longitude'],
                    'is_history'   => true,
                ]);
            }
        }

        // Update trash bin INSTANTLY (Agar Dashboard Peta & Volume Tetap 100% Real-time)
        $bin->update([
            'latitude'    => $validated['latitude'],
            'longitude'   => $validated['longitude'],
            'distance_cm' => $validated['distance_cm'],
            'percentage'  => $percentage,
            'status'      => $status,
        ]);

        // LOGIKA PENYARINGAN LOG SENSOR (Hanya simpan ke database sensor_logs setiap 5 menit sekali)
        $lastLog = SensorLog::where('trash_bin_id', $bin->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $shouldSaveLog = false;
        // Simpan log jika belum ada log sebelumnya, atau log terakhir sudah lebih dari 5 menit (300 detik) yang lalu
        if (!$lastLog || now()->diffInSeconds($lastLog->created_at) >= 300) {
            $shouldSaveLog = true;
        }

        $log = null;
        if ($shouldSaveLog) {
            $log = SensorLog::create([
                ...$validated,
                'percentage' => $percentage,
                'status'     => $status,
                'is_history' => false,
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => $shouldSaveLog ? 'Data sensor berhasil disimpan ke log' : 'Dashboard terupdate real-time (log dilewati)',
            'data'    => $log ?? $bin
        ], 201);
    }
}