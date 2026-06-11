@extends('layouts.main')

@section('content')

<div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4">
    <div>
        <h5 class="text-xl font-bold text-slate-800 dark:text-slate-100">Riwayat Log Sensor</h5>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Data pembacaan sensor ultrasonik & GPS</p>
    </div>
    
    <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
        <!-- Date Filter Form -->
        <form method="GET" action="{{ route('admin.sensor-logs') }}" class="flex flex-wrap items-center gap-2.5 w-full xl:w-auto">
            <div class="relative w-full sm:w-40">
                <input type="date" name="start_date" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl ps-9 pe-3 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all text-slate-700 dark:text-slate-200" value="{{ request('start_date') }}" title="Tanggal Mulai">
                <i class="fas fa-calendar-alt absolute top-1/2 left-3 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs"></i>
            </div>
            <div class="text-slate-400 dark:text-slate-500 text-xs font-bold self-center">s/d</div>
            <div class="relative w-full sm:w-40">
                <input type="date" name="end_date" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl ps-9 pe-3 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all text-slate-700 dark:text-slate-200" value="{{ request('end_date') }}" title="Tanggal Akhir">
                <i class="fas fa-calendar-alt absolute top-1/2 left-3 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs"></i>
            </div>
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-600 dark:bg-emerald-600 hover:bg-blue-700 dark:hover:bg-emerald-700 text-white rounded-xl shadow-md transition-all active:scale-95">
                <i class="fas fa-filter text-[10px]"></i> Filter
            </button>
            @if(request()->filled('start_date') || request()->filled('end_date'))
                <a href="{{ route('admin.sensor-logs') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold border border-slate-350 dark:border-slate-805 text-slate-650 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 rounded-xl transition-all">
                    <i class="fas fa-sync-alt text-[10px]"></i> Reset
                </a>
            @endif
        </form>

        <!-- Clear All Button -->
        @if($logs->total() > 0)
            <form method="POST" action="{{ route('admin.sensor-logs.clear') }}" onsubmit="return confirmAction(event, 'Apakah Anda yakin ingin menghapus semua riwayat log sensor? Tindakan ini tidak dapat dibatalkan.')" class="w-full sm:w-auto sm:ml-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-bold border border-red-500/35 text-red-600 dark:text-red-400 hover:bg-red-500/10 dark:hover:bg-red-500/20 rounded-xl transition-all cursor-pointer">
                    <i class="fas fa-trash-alt text-[10px]"></i> Kosongkan Log
                </button>
            </form>
        @endif
    </div>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Trash Bin</th>
                    <th class="px-6 py-4">Tinggi Sampah (cm)</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Koordinat GPS</th>
                    <th class="px-6 py-4">Waktu</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-150 dark:divide-slate-800/60">
                @forelse($logs as $log)
                @php 
                    $statusVal = strtolower($log->status);
                    
                    if ($statusVal === 'full') {
                        $badgeStyle = 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20';
                        $label = 'Full';
                    } elseif ($statusVal === 'empty') {
                        $badgeStyle = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
                        $label = 'Empty';
                    } else {
                        $badgeStyle = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
                        $label = 'Normal';
                    }
                @endphp
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-all">
                    <td class="px-6 py-4 text-xs font-mono text-slate-400 dark:text-slate-500">
                        {{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-800 dark:text-slate-200">{{ $log->trashBin->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $log->tinggi_sampah ?? '-' }} cm</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-full {{ $badgeStyle }}">
                            {{ $label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-mono text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-950 px-2 py-1 border border-slate-100 dark:border-slate-850 rounded-lg">
                            {{ $log->latitude }}, {{ $log->longitude }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-400 dark:text-slate-500">
                        {{ $log->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form method="POST" action="{{ route('admin.sensor-logs.destroy', $log->id) }}" onsubmit="return confirmAction(event, 'Hapus log sensor ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:bg-red-500/10 rounded-lg border border-red-500/20 transition-all cursor-pointer" title="Hapus Log">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-450 dark:text-slate-500">
                        <i class="fas fa-microchip fa-2x mb-3 opacity-40"></i>
                        <div class="text-sm font-medium">Tidak Ditemukan Log Sensor</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Belum ada data log pembacaan sensor atau filter tidak cocok.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
    <div class="p-4 border-t border-slate-200 dark:border-slate-800">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection