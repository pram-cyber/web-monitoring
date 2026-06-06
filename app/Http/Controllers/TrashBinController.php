<?php

namespace App\Http\Controllers;

use App\Models\TrashBin;
use Illuminate\Http\Request;

class TrashBinController extends Controller
{
    // ===== WEB METHODS =====
    public function index()
    {
        // Kita tambah request()->has('api') sebagai jalur paksa dari JavaScript
        if (request()->expectsJson() || request()->has('api')) {
            // Ubah menjadi TrashBin::all() agar sama persis dengan versi web manual
            $bins = TrashBin::all(); 
            $pendingReports = \App\Models\Report::where('status', 'pending')->count();
            return response()->json([
                'status' => 'success', 
                'data' => $bins,
                'pendingReports' => $pendingReports
            ]);
        }
        
        return redirect()->route('admin.dashboard');
    }

    public function create()
    {
        return view('admin.trash-bins.create');
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required'         => 'Nama tempat sampah wajib diisi.',
            'latitude.required'     => 'Latitude wajib diisi.',
            'latitude.numeric'      => 'Latitude harus berupa angka.',
            'latitude.between'      => 'Latitude tidak valid! Harus di antara -90 sampai 90 (gunakan titik untuk desimal, cth: -8.756789).',
            'longitude.required'    => 'Longitude wajib diisi.',
            'longitude.numeric'     => 'Longitude harus berupa angka.',
            'longitude.between'     => 'Longitude tidak valid! Harus di antara -180 sampai 180 (gunakan titik untuk desimal, cth: 112.34567).',
            'max_depth_cm.required' => 'Kedalaman maksimal wajib diisi.',
            'max_depth_cm.integer'  => 'Kedalaman maksimal harus berupa angka bulat.',
        ];

        // Kondisi jika request datang dari API (JSON)
        if (request()->expectsJson()) {
            $validated = $request->validate([
                'name'         => 'required|string',
                'location'     => 'nullable|string',
                'latitude'     => 'required|numeric|between:-90,90',
                'longitude'    => 'required|numeric|between:-180,180',
                'max_depth_cm' => 'required|integer',
            ], $messages);
            $bin = TrashBin::create($validated);
            return response()->json(['status' => 'success', 'message' => 'Trash bin berhasil ditambahkan', 'data' => $bin], 201);
        }

        // Kondisi jika request datang dari form Web
        $validatedWeb = $request->validate([
            'name'         => 'required',
            'location'     => 'nullable',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'max_depth_cm' => 'required|integer',
        ], $messages);
        
        $validatedWeb['latitude']  = $validatedWeb['latitude'] ?? -7.9797;
        $validatedWeb['longitude'] = $validatedWeb['longitude'] ?? 112.6304;
        
        TrashBin::create($validatedWeb);
        
        return redirect()->route('admin.trash-bins.index')->with('success', 'Trash bin berhasil ditambahkan!');
    }

    public function show($id)
    {
        $bin = TrashBin::with('sensorLogs')->findOrFail($id);
        if (request()->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $bin]);
        }
        return view('admin.trash-bins.show', compact('bin'));
    }

    public function edit($id)
    {
        $bin = TrashBin::findOrFail($id);
        return view('admin.trash-bins.edit', compact('bin'));
    }

    public function update(Request $request, $id)
    {
        $bin = TrashBin::findOrFail($id);
        
        $messages = [
            'name.required'         => 'Nama tempat sampah wajib diisi.',
            'latitude.required'     => 'Latitude wajib diisi.',
            'latitude.numeric'      => 'Latitude harus berupa angka.',
            'latitude.between'      => 'Latitude tidak valid! Harus di antara -90 sampai 90 (gunakan titik untuk desimal, cth: -8.756789).',
            'longitude.required'    => 'Longitude wajib diisi.',
            'longitude.numeric'     => 'Longitude harus berupa angka.',
            'longitude.between'     => 'Longitude tidak valid! Harus di antara -180 sampai 180 (gunakan titik untuk desimal, cth: 112.34567).',
            'max_depth_cm.required' => 'Kedalaman maksimal wajib diisi.',
            'max_depth_cm.integer'  => 'Kedalaman maksimal harus berupa angka bulat.',
        ];

        // Validasi keamanan sebelum melakukan update
        $validatedUpdate = $request->validate([
            'name'         => 'required|string',
            'location'     => 'nullable|string',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'max_depth_cm' => 'required|integer',
        ], $messages);

        $bin->update($validatedUpdate);
        
        if (request()->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Berhasil diupdate', 'data' => $bin]);
        }
        return redirect()->route('admin.trash-bins.index')->with('success', 'Trash bin berhasil diupdate!');
    }

    public function destroy($id)
    {
        TrashBin::findOrFail($id)->delete();
        if (request()->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Berhasil dihapus']);
        }
        return redirect()->route('admin.trash-bins.index')->with('success', 'Trash bin berhasil dihapus!');
    }

    public function markAsEmpty($id)
    {
        $bin = TrashBin::findOrFail($id);
        
        // 1. Jika tong sampah sebelumnya terisi (>= 70%), catat event "Penuh" terlebih dahulu ke history
        if ($bin->percentage >= 70) {
            \App\Models\SensorLog::create([
                'trash_bin_id' => $bin->id,
                'distance_cm'  => $bin->distance_cm,
                'percentage'   => $bin->percentage,
                'status'       => 'full',
                'latitude'     => $bin->latitude,
                'longitude'    => $bin->longitude,
                'recorded_at'  => now()->subSeconds(2), // Sedikit lebih awal agar urutan timeline bagus
                'is_history'   => true, 
            ]);
        }
        
        // 2. Simpan log pengambilan ("Diambil") ke history
        \App\Models\SensorLog::create([
            'trash_bin_id' => $bin->id,
            'distance_cm'  => $bin->max_depth_cm,
            'percentage'   => 0,
            'status'       => 'empty',
            'latitude'     => $bin->latitude,
            'longitude'    => $bin->longitude,
            'recorded_at'  => now(),
            'is_history'   => true, // Event resmi pengosongan sampah!
        ]);

        $bin->update([
            'percentage'  => 0,
            'status'      => 'empty',
            'distance_cm' => $bin->max_depth_cm
        ]);

        return response()->json(['success' => true, 'message' => 'Tong sampah berhasil ditandai kosong!']);
    }
}