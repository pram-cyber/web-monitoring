@extends('layouts.main')

@section('content')

<div class="mb-6">
    <h5 class="text-xl font-bold text-slate-800 dark:text-slate-100">Pengaturan Notifikasi & Akun</h5>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Atur jangkauan radius notifikasi dan kelola akun Anda</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Notification Settings Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
        <div>
            <h6 class="font-bold text-slate-850 dark:text-slate-100 text-sm mb-5 flex items-center gap-2">
                <i class="fas fa-bell text-amber-500 animate-swing"></i> Radius Notifikasi
            </h6>
            
            <form action="{{ route('user.settings.update') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-slate-550 dark:text-slate-400">Jangkauan Notifikasi</label>
                        <span id="radius-val" class="font-black text-sm text-blue-600 dark:text-emerald-400 bg-blue-50 dark:bg-emerald-950/40 px-2.5 py-0.5 border border-blue-100 dark:border-emerald-500/20 rounded-lg">
                            {{ auth()->user()->notification_radius }}m
                        </span>
                    </div>

                    <input type="range" name="notification_radius" class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-lg appearance-none cursor-pointer accent-blue-600 dark:accent-emerald-500"
                           min="50" max="1000" step="50" 
                           value="{{ auth()->user()->notification_radius }}"
                           oninput="document.getElementById('radius-val').textContent = this.value + 'm'">
                    
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-3.5 leading-relaxed">
                        Peringatan alarm dan notifikasi visual akan otomatis muncul jika ada tong sampah penuh (&gt;85%) di sekitar koordinat lokasi Anda dalam radius ini.
                    </p>
                </div>
                
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-600 dark:bg-emerald-600 hover:bg-blue-700 dark:hover:bg-emerald-700 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer">
                    <i class="fas fa-save text-[10px]"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    <!-- Profile & Account Management Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm flex flex-col justify-between relative min-h-[260px]">
        <div>
            <h6 class="font-bold text-slate-850 dark:text-slate-100 text-sm mb-5 flex items-center gap-2">
                <i class="fas fa-user text-blue-500"></i> Profil Saya
            </h6>
            
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 text-white flex items-center justify-center text-xl font-black shadow-md shadow-blue-500/10">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-grow">
                    <div class="font-bold text-slate-800 dark:text-slate-200 text-sm truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ auth()->user()->email }}</div>
                    <span class="inline-block px-2.5 py-0.5 text-[9px] font-extrabold bg-emerald-500/10 text-emerald-650 dark:text-emerald-450 border border-emerald-500/20 rounded-md mt-1.5">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>
        </div>

        <!-- Delete Account Button (aligned bottom right) -->
        <div class="absolute bottom-6 right-6">
            <form action="{{ route('settings.destroy') }}" method="POST"
                  onsubmit="return confirmAction(event, 'Apakah Anda yakin ingin menghapus akun Anda secara permanen? Semua data laporan dan profil Anda akan terhapus selamanya.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-[10px] font-bold border border-red-500/35 text-red-650 dark:text-red-400 hover:bg-red-500/10 dark:hover:bg-red-500/20 rounded-full transition-all cursor-pointer">
                    <i class="fas fa-trash-alt text-[10px]"></i> Hapus Akun
                </button>
            </form>
        </div>
    </div>
</div>

@endsection