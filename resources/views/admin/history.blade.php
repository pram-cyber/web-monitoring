@extends('layouts.main')

@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h5 class="text-xl font-bold text-slate-800 dark:text-slate-100">Riwayat Tong Sampah</h5>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kapan tong sampah penuh dan diambil</p>
    </div>
    <div class="flex gap-2 w-full sm:w-auto">
        <select class="w-full sm:w-44 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all text-slate-700 dark:text-slate-200" id="filter-bin" onchange="filterHistory()">
            <option value="">Semua Bin</option>
            @foreach($bins as $bin)
            <option value="{{ $bin->id }}">{{ $bin->name }}</option>
            @endforeach
        </select>
        <select class="w-full sm:w-44 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all text-slate-700 dark:text-slate-200" id="filter-status" onchange="filterHistory()">
            <option value="">Semua Status</option>
            <option value="full">Penuh</option>
            <option value="normal">Normal</option>
            <option value="diambil">Diambil</option>
        </select>
    </div>
</div>

<!-- Timeline & Sidebar Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Timeline List -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 p-5 rounded-2xl shadow-sm">
        <h6 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-5 flex items-center gap-2">
            <i class="fas fa-history text-blue-500 dark:text-emerald-400"></i> Timeline Riwayat
        </h6>
        
        <div id="history-list" class="space-y-4">
            @forelse($histories as $log)
            @php
                $isFull = ($log->status === 'full' || $log->percentage > 85);
                $isNormal = ($log->status === 'half' || ($log->percentage >= 25 && $log->percentage <= 85));
                
                if ($isFull) {
                    $itemColor = 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20';
                    $badgeClass = 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20';
                    $statusLabel = 'Penuh';
                } elseif ($isNormal) {
                    $itemColor = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
                    $badgeClass = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
                    $statusLabel = 'Normal';
                } else {
                    $itemColor = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
                    $badgeClass = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
                    $statusLabel = 'Diambil';
                }
            @endphp
            <div class="history-item flex gap-4" 
                 data-bin="{{ $log->trash_bin_id }}"
                 data-status="{{ $isFull ? 'full' : ($isNormal ? 'normal' : 'diambil') }}">
                
                <!-- Dot & Line -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $itemColor }}">
                        <i class="fas {{ $isFull ? 'fa-exclamation-triangle' : ($isNormal ? 'fa-info-circle' : 'fa-check-circle') }} text-sm"></i>
                    </div>
                    <div class="w-0.5 flex-grow bg-slate-200 dark:bg-slate-800/80 mt-2"></div>
                </div>

                <!-- Box Content -->
                <div class="bg-slate-50/50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800/80 p-4 rounded-xl flex-grow mb-1 shadow-sm">
                    <div class="flex justify-between items-start gap-2">
                        <div class="min-w-0">
                            <div class="font-bold text-slate-800 dark:text-slate-200 text-sm truncate">{{ $log->trashBin->name ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                <i class="fas fa-map-marker-alt text-[10px]"></i> {{ $log->trashBin->location ?? '-' }}
                            </div>
                        </div>
                        <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-full {{ $badgeClass }} whitespace-nowrap">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div class="flex gap-4 mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/60 text-[10px] text-slate-400 dark:text-slate-500">
                        <span class="font-semibold"><i class="far fa-clock"></i> {{ $log->recorded_at ? \Carbon\Carbon::parse($log->recorded_at)->format('d M Y, H:i') : $log->created_at->format('d M Y, H:i') }}</span>
                        <span class="font-semibold"><i class="fas fa-tachometer-alt"></i> {{ $log->percentage }}% penuh</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-slate-400 dark:text-slate-500 py-16">
                <i class="fas fa-history fa-3x mb-4 opacity-40"></i>
                <div class="text-sm font-medium">Belum Ada Riwayat</div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">Sistem belum mendeteksi adanya riwayat pengisian atau pengambilan tong sampah.</p>
            </div>
            @endforelse
        </div>
        
        <div class="mt-5">
            {{ $histories->links() }}
        </div>
    </div>

    <!-- Stats & Sidebar Panel -->
    <div class="space-y-6 lg:col-span-1">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 p-5 rounded-2xl shadow-sm">
            <h6 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-amber-500"></i> Ringkasan Statistik
            </h6>
            
            <div class="space-y-3 mb-5">
                <div class="flex justify-between items-center bg-slate-50 dark:bg-slate-950 p-3 rounded-xl border border-slate-100 dark:border-slate-800/60 shadow-sm">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Penuh Hari Ini</span>
                    <span class="px-2.5 py-1 text-xs font-extrabold bg-rose-500/10 text-rose-600 dark:text-rose-450 rounded-lg border border-rose-500/20">{{ $fullToday }}</span>
                </div>
                <div class="flex justify-between items-center bg-slate-50 dark:bg-slate-950 p-3 rounded-xl border border-slate-100 dark:border-slate-800/60 shadow-sm">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Diambil Hari Ini</span>
                    <span class="px-2.5 py-1 text-xs font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-450 rounded-lg border border-emerald-500/20">{{ $takenToday }}</span>
                </div>
                <div class="flex justify-between items-center bg-slate-50 dark:bg-slate-950 p-3 rounded-xl border border-slate-100 dark:border-slate-800/60 shadow-sm">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Penuh Minggu Ini</span>
                    <span class="px-2.5 py-1 text-xs font-extrabold bg-amber-500/10 text-amber-600 dark:text-amber-450 rounded-lg border border-amber-500/20">{{ $fullWeek }}</span>
                </div>
            </div>
            
            <div class="border-t border-slate-100 dark:border-slate-800/60 pt-4">
                <canvas id="historyChart" class="max-h-[180px] w-full"></canvas>
            </div>
        </div>

        <!-- Most Active Bins -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 p-5 rounded-2xl shadow-sm">
            <h6 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-4 flex items-center gap-2">
                <i class="fas fa-fire text-rose-500"></i> Paling Sering Penuh
            </h6>
            <div class="space-y-3">
                @foreach($mostFull as $item)
                <div class="flex justify-between items-center bg-slate-50 dark:bg-slate-950 p-3 rounded-xl border border-slate-100 dark:border-slate-800/60 shadow-sm gap-2">
                    <div class="min-w-0">
                        <div class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate">{{ $item->trashBin->name ?? '-' }}</div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ $item->trashBin->location ?? '-' }}</div>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-extrabold bg-rose-500/10 text-rose-600 dark:text-rose-455 rounded-lg border border-rose-500/20 shrink-0">{{ $item->total }}x</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function filterHistory() {
        var binId = document.getElementById('filter-bin').value;
        var status = document.getElementById('filter-status').value;
        document.querySelectorAll('.history-item').forEach(item => {
            var matchBin = !binId || item.dataset.bin == binId;
            var matchStatus = !status || item.dataset.status == status;
            item.style.display = (matchBin && matchStatus) ? 'flex' : 'none'; // changed from '' to flex
        });
    }

    // Chart
    var ctx = document.getElementById('historyChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Penuh', 'Diambil'],
            datasets: [{
                data: [{{ $fullToday }}, {{ $takenToday }}],
                backgroundColor: ['#f43f5e', '#10b981'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
@endpush