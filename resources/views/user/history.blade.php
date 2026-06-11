@extends('layouts.main')

@section('content')

<div class="mb-6">
    <h5 class="text-xl font-bold text-slate-800 dark:text-slate-100">Riwayat Tong Sampah</h5>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">History kapan tong penuh dan diambil</p>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden p-5">
    <div class="overflow-x-auto rounded-xl border border-slate-150 dark:border-slate-800">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-4">Tong Sampah</th>
                    <th class="px-6 py-4">Lokasi</th>
                    <th class="px-6 py-4">Volume</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-150 dark:divide-slate-800/60">
                @forelse($histories as $log)
                @php
                    $isFull = ($log->status === 'full' || $log->percentage > 85);
                    $isNormal = ($log->status === 'half' || ($log->percentage >= 25 && $log->percentage <= 85));
                    
                    if ($isFull) {
                        $badgeStyle = 'bg-rose-500/10 text-rose-650 dark:text-rose-455 border border-rose-500/20';
                        $barColor = 'bg-rose-500';
                        $statusLabel = 'Penuh';
                    } elseif ($isNormal) {
                        $badgeStyle = 'bg-amber-500/10 text-amber-655 dark:text-amber-455 border border-amber-500/20';
                        $barColor = 'bg-amber-500';
                        $statusLabel = 'Normal';
                    } else {
                        $badgeStyle = 'bg-emerald-500/10 text-emerald-650 dark:text-emerald-455 border border-emerald-500/20';
                        $barColor = 'bg-emerald-500';
                        $statusLabel = 'Diambil';
                    }
                @endphp
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-all">
                    <td class="px-6 py-4 text-sm font-bold text-slate-800 dark:text-slate-100">{{ $log->trashBin->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $log->trashBin->location ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-24 bg-slate-100 dark:bg-slate-850 h-2 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700 {{ $barColor }}" style="width:{{ $log->percentage }}%"></div>
                            </div>
                            <small class="text-xs font-bold text-slate-700 dark:text-slate-305">{{ $log->percentage }}%</small>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-full whitespace-nowrap {{ $badgeStyle }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-400 dark:text-slate-550">
                        {{ $log->recorded_at ? \Carbon\Carbon::parse($log->recorded_at)->format('d M Y, H:i') : $log->created_at->format('d M Y, H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-450 dark:text-slate-500">
                        <i class="fas fa-history fa-2x mb-3 opacity-40"></i>
                        <div class="text-sm font-medium">Belum Ada Riwayat</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sistem belum mendeteksi adanya riwayat pengisian atau pengambilan tong sampah.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($histories->hasPages())
    <div class="mt-4">
        {{ $histories->links() }}
    </div>
    @endif
</div>

@endsection