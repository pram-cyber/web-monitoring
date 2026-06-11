@extends('layouts.main')

@section('content')

<div class="mb-6">
    <h5 class="text-xl font-bold text-slate-800 dark:text-slate-100">Tambah Trash Bin</h5>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Registrasikan tong sampah pintar baru ke dalam sistem</p>
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

        <form action="{{ route('admin.trash-bins.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1.5">Nama</label>
                <input type="text" name="name" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all @error('name') border-rose-500 focus:ring-rose-500 @enderror" value="{{ old('name') }}" required placeholder="Contoh: Bin 001">
                @error('name')
                    <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-455 uppercase mb-1.5">Lokasi</label>
                <input type="text" name="location" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all @error('location') border-rose-500 focus:ring-rose-500 @enderror" value="{{ old('location') }}" placeholder="Contoh: Gedung A Lantai 1">
                @error('location')
                    <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-455 uppercase mb-1.5">Kedalaman Maksimum (cm)</label>
                <input type="number" name="max_depth_cm" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all @error('max_depth_cm') border-rose-500 focus:ring-rose-500 @enderror" value="{{ old('max_depth_cm') }}" required placeholder="Contoh: 50">
                @error('max_depth_cm')
                    <p class="text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 border-t border-slate-100 dark:border-slate-800 pt-5">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold bg-blue-600 dark:bg-emerald-600 hover:bg-blue-700 dark:hover:bg-emerald-700 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer">
                    <i class="fas fa-save text-xs"></i> Simpan
                </button>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold border border-slate-300 dark:border-slate-805 text-slate-650 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all cursor-pointer">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection