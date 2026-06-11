@extends('layouts.main')

@section('content')

<div class="mb-6">
    <h5 class="text-xl font-bold text-slate-800 dark:text-slate-100">Tren Volume Sampah</h5>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Analisis tren mingguan dan bulanan</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
        <h6 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-4 flex items-center gap-2">
            <i class="fas fa-chart-line text-blue-500"></i> Tren Mingguan
        </h6>
        <div class="relative h-[250px] w-full">
            <canvas id="weeklyChart"></canvas>
        </div>
    </div>
    <div class="lg:col-span-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
        <h6 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-4 flex items-center gap-2">
            <i class="fas fa-chart-bar text-emerald-500"></i> Tren Bulanan
        </h6>
        <div class="relative h-[250px] w-full">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>
</div>

<!-- Per Bin Chart -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
    <h6 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-5 flex items-center gap-2">
        <i class="fas fa-trash text-amber-500"></i> Volume Per Tong Sampah
    </h6>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
        @foreach($bins as $bin)
        @php
            $isConn = $bin->is_connected;
            $perc = $isConn ? $bin->percentage : 0;
            
            if (!$isConn) {
                $color = '#64748b'; // slate-500
            } else {
                $color = $perc > 85 ? '#f43f5e' : ($perc < 25 ? '#10b981' : '#f59e0b');
            }
        @endphp
        <div class="bg-slate-50/50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800/80 p-4 rounded-2xl text-center shadow-sm hover:shadow-md transition-all duration-200">
            <div class="font-bold text-slate-800 dark:text-slate-200 text-sm truncate" title="{{ $bin->name }}">{{ $bin->name }}</div>
            <div class="text-[10px] text-slate-400 dark:text-slate-500 truncate mt-0.5 mb-4"><i class="fas fa-map-marker-alt"></i> {{ $bin->location ?? '-' }}</div>
            <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                <svg width="80" height="80" class="transform -rotate-90">
                    <circle cx="40" cy="40" r="32" stroke="currentColor" stroke-width="5" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                    <circle cx="40" cy="40" r="32" stroke="{{ $color }}" stroke-width="5" fill="transparent"
                            stroke-dasharray="201" stroke-dashoffset="{{ 201 - (201 * $perc) / 100 }}"
                            stroke-linecap="round" class="transition-all duration-700" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center font-extrabold text-sm text-slate-800 dark:text-slate-100" style="color: {{ $color }}">
                    {{ $isConn ? $bin->percentage . '%' : '-' }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('weeklyChart'), {
        type: 'line',
        data: {
            labels: @json($weeklyLabels),
            datasets: [{
                label: 'Rata-rata Volume (%)',
                data: @json($weeklyData),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.1)',
                fill: true, tension: 0.4, pointRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });

    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: @json($monthlyLabels),
            datasets: [{
                data: @json($monthlyData),
                backgroundColor: 'rgba(16,185,129,0.7)',
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });
</script>
@endpush