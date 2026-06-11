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
            'trash_bin_id'  => 'required|exists:trash_bins,id',
            'distance_cm'   => 'required|numeric',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'gps_accuracy'  => 'nullable|numeric',
            'percentage'    => 'nullable|numeric|between:0,100',
            'tinggi_sampah' => 'nullable|integer',
            'sisa_ruang'    => 'nullable|integer',
        ]);

        $bin = TrashBin::findOrFail($validated['trash_bin_id']);
        $previousPercentage = $bin->percentage;
        $previousStatus = $bin->status;
        $previousDistance = $bin->distance_cm;
        $previousLatitude = $bin->latitude;
        $previousLongitude = $bin->longitude;

        // Hitung persentase, tinggi_sampah, dan sisa_ruang berdasarkan rumus IoT baru
        $max_depth = floatval($bin->max_depth_cm ?? 55);
        $distance = floatval($validated['distance_cm']);

        // Batasi jarak fisik agar berada dalam rentang sensor [11.0, 59.4]
        $clampedDistance = max(11.0, min(59.4, $distance));

        // Terapkan rumus sisa ruang dan tinggi sampah sesuai logika IoT
        $sisaRuangVal = (($clampedDistance - 11.0) / (59.4 - 11.0)) * $max_depth;
        $tinggiSampahVal = $max_depth - $sisaRuangVal;

        // Persentase kapasitas terisi (Tinggi Sampah / Tinggi Wadah)
        $percentage = ($tinggiSampahVal / $max_depth) * 100;
        $percentage = max(0, min(100, round($percentage)));

        $tinggiSampah = (int)round($tinggiSampahVal);
        $sisaRuang = (int)round($sisaRuangVal);

        // Tentukan status berdasarkan persentase baru
        if ($percentage < 25) {
            $status = 'empty';
        } elseif ($percentage > 85) {
            $status = 'full';
        } else {
            $status = 'half';
        }

        // AMBIL LOG RIWAYAT TERAKHIR UNTUK TONG INI (is_history = true)
        $lastHistoryLog = SensorLog::where('trash_bin_id', $bin->id)
            ->where('is_history', true)
            ->orderBy('created_at', 'desc')
            ->first();
            
        $lastIsFull = $lastHistoryLog && ($lastHistoryLog->status === 'full' || $lastHistoryLog->percentage > 85);
        $lastIsEmpty = !$lastHistoryLog || ($lastHistoryLog->status === 'empty' || $lastHistoryLog->percentage < 25);
        $lastIsHalf = $lastHistoryLog && ($lastHistoryLog->status === 'half' || ($lastHistoryLog->percentage >= 25 && $lastHistoryLog->percentage <= 85));

        // 1. OTOMATIS CATAT EVENT "PENUH" (Jika kapasitas > 85% dan riwayat terakhir bukan "full")
        if ($percentage > 85) {
            if (!$lastIsFull) {
                SensorLog::create([
                    'trash_bin_id'  => $bin->id,
                    'distance_cm'   => $sisaRuang,
                    'percentage'    => $percentage,
                    'status'        => 'full',
                    'latitude'      => $validated['latitude'],
                    'longitude'     => $validated['longitude'],
                    'is_history'    => true,
                    'tinggi_sampah' => $tinggiSampah,
                    'sisa_ruang'    => $sisaRuang,
                ]);
            }
        }

        // 2. OTOMATIS CATAT EVENT "NORMAL" (Jika kapasitas 25% - 85% dan riwayat terakhir adalah "empty" atau belum ada riwayat)
        if ($percentage >= 25 && $percentage <= 85) {
            if ($lastIsEmpty) {
                SensorLog::create([
                    'trash_bin_id'  => $bin->id,
                    'distance_cm'   => $sisaRuang,
                    'percentage'    => $percentage,
                    'status'        => 'half',
                    'latitude'      => $validated['latitude'],
                    'longitude'     => $validated['longitude'],
                    'is_history'    => true,
                    'tinggi_sampah' => $tinggiSampah,
                    'sisa_ruang'    => $sisaRuang,
                ]);
            }
        }

        // 3. OTOMATIS CATAT EVENT "DIAMBIL" (Jika kapasitas < 25% dan riwayat terakhir "full" atau "half" ATAU kapasitas sebelumnya >= 25%)
        if ($percentage < 25) {
            $wasFull = $lastIsFull || ($previousPercentage > 85);
            $wasHalf = $lastIsHalf || ($previousPercentage >= 25 && $previousPercentage <= 85);

            if ($wasFull || $wasHalf) {
                // Jika belum ada log penuh tercatat sebagai riwayat terakhir, mari buat log penuh buatan agar alur timelinenya bagus
                if ($wasFull && !$lastIsFull) {
                    SensorLog::create([
                        'trash_bin_id'  => $bin->id,
                        'distance_cm'   => $previousDistance,
                        'percentage'    => $previousPercentage,
                        'status'        => 'full',
                        'latitude'      => $previousLatitude,
                        'longitude'     => $previousLongitude,
                        'is_history'    => true,
                        'created_at'    => now()->subSeconds(2),
                        'tinggi_sampah' => $bin->tinggi_sampah ?? (int)round($bin->max_depth_cm - $previousDistance),
                        'sisa_ruang'    => $bin->sisa_ruang ?? (int)round($previousDistance),
                    ]);
                }

                // Catat event "Diambil/Kosong" ke riwayat resmi
                SensorLog::create([
                    'trash_bin_id'  => $bin->id,
                    'distance_cm'   => $sisaRuang,
                    'percentage'    => $percentage,
                    'status'        => 'empty',
                    'latitude'      => $validated['latitude'],
                    'longitude'     => $validated['longitude'],
                    'is_history'    => true,
                    'tinggi_sampah' => $tinggiSampah,
                    'sisa_ruang'    => $sisaRuang,
                ]);
            }
        }

        // Update trash bin INSTANTLY (Agar Dashboard Peta & Volume Tetap 100% Real-time)
        $bin->update([
            'latitude'      => $validated['latitude'],
            'longitude'     => $validated['longitude'],
            'distance_cm'   => $sisaRuang,
            'percentage'    => $percentage,
            'status'        => $status,
            'tinggi_sampah' => $tinggiSampah,
            'sisa_ruang'    => $sisaRuang,
        ]);

        // LOGIKA PENYARINGAN LOG SENSOR (Hanya simpan ke database sensor_logs setiap 5 menit sekali)
        $lastLog = SensorLog::where('trash_bin_id', $bin->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $shouldSaveLog = false;
        // Simpan log jika belum ada log sebelumnya, atau log terakhir sudah lebih dari 5 menit yang lalu
        if (!$lastLog || now()->gt($lastLog->created_at->addMinutes(5))) {
            $shouldSaveLog = true;
        }

        $log = null;
        if ($shouldSaveLog) {
            $log = SensorLog::create([
                ...$validated,
                'distance_cm'   => $sisaRuang,
                'percentage'    => $percentage,
                'status'        => $status,
                'is_history'    => false,
                'tinggi_sampah' => $tinggiSampah,
                'sisa_ruang'    => $sisaRuang,
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => $shouldSaveLog ? 'Data sensor berhasil disimpan ke log' : 'Dashboard terupdate real-time (log dilewati)',
            'data'    => $log ?? $bin
        ], 201);
    }
}