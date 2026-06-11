@extends('layouts.main')

@section('content')

<div class="mb-6">
    <h5 class="text-xl font-bold text-slate-800 dark:text-slate-100">Edit Trash Bin</h5>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Ubah konfigurasi dan koordinat GPS untuk tong sampah pintar</p>
</div>

<div class="max-w-2xl">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm">
        <!-- Global error display -->
        @if ($errors->any())
        <div class="bg-rose-500/10 text-rose-600 dark:text-rose-455 border border-rose-500/20 rounded-xl p-4 mb-5 text-xs">
            <div class="font-bold flex items-center gap-1.5 mb-2">
                <i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan pengisian data:
            </div>
            <ul class="list-disc list-inside space-y-1 font-semibold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.trash-bins.update', $bin->id) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-455 uppercase mb-1.5">Nama</label>
                <input type="text" name="name" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all @error('name') border-rose-500 focus:ring-rose-500 @enderror" value="{{ old('name', $bin->name) }}" required>
                @error('name')
                    <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-455 uppercase mb-1.5">Lokasi</label>
                <input type="text" name="location" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all @error('location') border-rose-500 focus:ring-rose-500 @enderror" value="{{ old('location', $bin->location) }}">
                @error('location')
                    <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-455 uppercase mb-1.5">Latitude</label>
                    <input type="text" name="latitude" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all @error('latitude') border-rose-500 focus:ring-rose-500 @enderror" value="{{ old('latitude', $bin->latitude) }}" required>
                    @error('latitude')
                        <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-455 uppercase mb-1.5">Longitude</label>
                    <input type="text" name="longitude" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all @error('longitude') border-rose-500 focus:ring-rose-500 @enderror" value="{{ old('longitude', $bin->longitude) }}" required>
                    @error('longitude')
                        <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-455 uppercase mb-1.5">Kedalaman Maksimum (cm)</label>
                <input type="number" name="max_depth_cm" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all @error('max_depth_cm') border-rose-500 focus:ring-rose-500 @enderror" value="{{ old('max_depth_cm', $bin->max_depth_cm) }}" required>
                @error('max_depth_cm')
                    <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-455 uppercase mb-1.5">Status Aktif</label>
                <select name="is_active" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all @error('is_active') border-rose-500 focus:ring-rose-500 @enderror">
                    <option value="1" {{ old('is_active', $bin->is_active) ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !old('is_active', $bin->is_active) ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('is_active')
                    <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 border-t border-slate-100 dark:border-slate-800 pt-5">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold bg-amber-500 hover:bg-amber-600 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer">
                    <i class="fas fa-save text-xs"></i> Update
                </button>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold border border-slate-300 dark:border-slate-805 text-slate-650 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all cursor-pointer">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection