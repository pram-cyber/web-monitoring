@extends('layouts.main')

@section('content')

<!-- STAT CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
        <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Bins</div>
            <div id="stat-total-bins" class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ $totalBins }}</div>
        </div>
        <div class="w-12 h-12 bg-blue-50 dark:bg-blue-950/45 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-inner">
            <i class="fas fa-trash text-lg"></i>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
        <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Critical Bins</div>
            <div id="stat-critical-bins" class="text-3xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">{{ $criticalBins }}</div>
        </div>
        <div class="w-12 h-12 bg-rose-50 dark:bg-rose-950/45 rounded-xl flex items-center justify-center text-rose-600 dark:text-rose-400 shadow-inner">
            <i class="fas fa-exclamation-triangle text-lg animate-pulse"></i>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
        <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Average Fill</div>
            <div id="stat-avg-fill" class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $avgFill }}%</div>
        </div>
        <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-950/45 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-inner">
            <i class="fas fa-chart-line text-lg"></i>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
        <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Tracking</div>
            <div id="stat-active-bins" class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">{{ $activeBins }}</div>
        </div>
        <div class="w-12 h-12 bg-purple-50 dark:bg-purple-950/45 rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400 shadow-inner">
            <i class="fas fa-map-marker-alt text-lg"></i>
        </div>
    </div>
</div>

<!-- TAB NAV -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/85 rounded-2xl shadow-sm overflow-hidden flex flex-col mb-6">
    <div class="flex border-b border-slate-200 dark:border-slate-800/80 px-4 py-2 gap-2 overflow-x-auto bg-slate-50/50 dark:bg-slate-900/40">
        <button class="tab-btn active border-b-2 border-transparent px-4 py-3 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 focus:outline-none transition-all duration-200 flex items-center gap-2" data-tab="peta" onclick="switchTab('peta')">
            <i class="fas fa-map-marked-alt"></i> Peta Monitoring
        </button>
        <button class="tab-btn border-b-2 border-transparent px-4 py-3 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 focus:outline-none transition-all duration-200 flex items-center gap-2" data-tab="daftar" onclick="switchTab('daftar')">
            <i class="fas fa-trash-alt"></i> Monitor & Kelola
        </button>
        <button class="tab-btn border-b-2 border-transparent px-4 py-3 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 focus:outline-none transition-all duration-200 flex items-center gap-2" data-tab="analitik" onclick="switchTab('analitik')">
            <i class="fas fa-chart-bar"></i> Analitik Tren
        </button>
    </div>

    <!-- TAB PETA -->
    <div id="tab-peta" class="tab-content-area active">
        <div class="grid grid-cols-1 lg:grid-cols-4 divide-y lg:divide-y-0 lg:divide-x divide-slate-200 dark:divide-slate-800">
            <!-- Map Container -->
            <div class="lg:col-span-3 relative h-[550px]">
                <div id="map" class="h-full w-full"></div>
                
                <!-- Real-time Coordinates Overlay -->
                <div class="absolute bottom-4 left-4 z-[999] bg-white/90 dark:bg-slate-900/90 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-800 px-3 py-1.5 rounded-xl font-mono text-xs pointer-events-none shadow-lg backdrop-blur-md flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span>
                    <span>X: <b id="cursor-lng">-</b></span>
                    <span class="text-slate-300 dark:text-slate-700">|</span>
                    <span>Y: <b id="cursor-lat">-</b></span>
                </div>
                
                <!-- Status Legend Overlay -->
                <div class="absolute bottom-4 right-4 z-[999] bg-white/90 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800/80 rounded-xl p-3.5 text-xs shadow-lg backdrop-blur-md max-w-[170px] space-y-2">
                    <div class="font-bold text-slate-950 dark:text-slate-50 flex items-center gap-1">
                        <i class="fas fa-info-circle text-blue-500 dark:text-emerald-400"></i> Legenda Status
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400 font-medium">
                            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span> Empty (&lt;25%)
                        </div>
                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400 font-medium">
                            <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span> Normal (25%-85%)
                        </div>
                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400 font-medium">
                            <span class="w-2.5 h-2.5 bg-rose-500 rounded-full"></span> Full (&gt;85%)
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Locations Sidebar Panel -->
            <div class="lg:col-span-1 flex flex-col h-[550px] bg-slate-50/30 dark:bg-slate-900/20">
                <!-- Sidebar Header -->
                <div class="p-4 border-b border-slate-200 dark:border-slate-800">
                    <h6 class="font-bold text-sm text-slate-800 dark:text-slate-200 flex items-center gap-2 mb-3">
                        <i class="fas fa-map-marker-alt text-rose-500"></i> Daftar Lokasi
                    </h6>
                    <div class="relative">
                        <input type="text" class="w-full bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-850 rounded-xl ps-9 pe-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all text-slate-800 dark:text-slate-100" id="search-locations"
                               placeholder="Cari lokasi bin..." onkeyup="filterMapLocations(this.value)">
                        <i class="fas fa-search absolute top-1/2 left-3 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs"></i>
                    </div>
                </div>
                
                <!-- Sidebar Scrollable List -->
                <div class="p-3 flex-grow overflow-y-auto space-y-2" id="map-loc-list">
                    @foreach($bins as $bin)
                    @php
                        $isConn = $bin->is_connected;
                        $perc = $isConn ? $bin->percentage : 0;
                        if (!$isConn) {
                            $badgeStyle = 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20';
                        } else {
                            if ($perc > 85) {
                                $badgeStyle = 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20';
                            } elseif ($perc < 25) {
                                $badgeStyle = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
                            } else {
                                $badgeStyle = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
                            }
                        }
                    @endphp
                    <div class="map-loc-item flex items-center gap-3 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900/60 shadow-sm cursor-pointer transition-all duration-200 hover:border-blue-500/30 dark:hover:border-emerald-500/30 hover:shadow"
                         id="map-loc-item-{{ $bin->id }}"
                         data-searchable="{{ strtolower($bin->name . ' ' . $bin->location) }}"
                         onclick="flyToMap({{ $bin->latitude ?? 0 }}, {{ $bin->longitude ?? 0 }}, {{ $bin->id }})">
                        
                        <span id="badge-maplist-{{ $bin->id }}" class="px-2 py-1 text-xs font-extrabold rounded-lg min-w-[42px] text-center {{ $badgeStyle }}">
                            {{ $bin->percentage }}%
                        </span>
                        
                        <div class="text-truncate min-w-0">
                            <div class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate">{{ $bin->name }}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                {{ $bin->location ?? '-' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- TAB DAFTAR (Monitor & Kelola) -->
    <div id="tab-daftar" class="tab-content-area hidden p-5">
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mb-5 gap-3">
            <!-- Search & View Mode Toggle -->
            <div class="flex gap-3 items-center flex-grow max-w-lg">
                <div class="relative flex-grow">
                    <input type="text" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl ps-9 pe-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all text-slate-800 dark:text-slate-100" id="search-bins"
                           placeholder="Cari tong sampah..." onkeyup="filterBins(this.value)">
                    <i class="fas fa-search absolute top-1/2 left-3 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-sm"></i>
                </div>
                
                <div class="inline-flex bg-slate-100 dark:bg-slate-950 p-1 rounded-xl border border-slate-200/50 dark:border-slate-800/80 gap-1 select-none">
                    <button type="button" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg flex items-center gap-1.5 cursor-pointer transition-all duration-200 focus:outline-none" id="btn-view-grid" onclick="setViewMode('grid')" title="Tampilan Card Grid">
                        <i class="fas fa-th-large"></i> Grid
                    </button>
                    <button type="button" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg flex items-center gap-1.5 cursor-pointer transition-all duration-200 focus:outline-none" id="btn-view-table" onclick="setViewMode('table')" title="Tampilan Tabel Kelola">
                        <i class="fas fa-table"></i> Tabel
                    </button>
                </div>
            </div>
            
            <a href="{{ route('admin.trash-bins.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold bg-blue-600 dark:bg-emerald-600 hover:bg-blue-700 dark:hover:bg-emerald-700 text-white rounded-xl shadow-md shadow-blue-500/10 dark:shadow-emerald-500/10 transition-all active:scale-95 text-center justify-center">
                <i class="fas fa-plus"></i> Tambah Bin
            </a>
        </div>

        <!-- 1. GRID VIEW (VOLUME CARDS) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="bins-grid" style="display: none;">
            @forelse($bins as $bin)
            @php
                $isConn = $bin->is_connected;
                $perc = $isConn ? $bin->percentage : 0;
                
                if (!$isConn) {
                    $c = '#64748b'; // slate-500
                    $badgeStyle = 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20';
                    $label = 'Offline';
                } else {
                    if ($perc > 85) {
                        $c = '#f43f5e'; // rose-500
                        $badgeStyle = 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20';
                        $label = 'Full';
                    } elseif ($perc < 25) {
                        $c = '#10b981'; // emerald-500
                        $badgeStyle = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
                        $label = 'Empty';
                    } else {
                        $c = '#f59e0b'; // amber-500
                        $badgeStyle = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
                        $label = 'Normal';
                    }
                }
                $circumference = 2 * 3.14159265 * 40; // r=40
                $dashoffset = $circumference - ($circumference * $perc) / 100;
            @endphp
            <div class="bin-grid-item" data-searchable="{{ strtolower($bin->name . ' ' . $bin->location) }}">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 flex flex-col h-full shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex justify-between items-start mb-4 gap-2">
                        <div class="min-w-0">
                            <div class="font-bold text-slate-800 dark:text-slate-100 text-lg truncate" title="{{ $bin->name }}">{{ $bin->name }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-0.5 truncate">
                                <i class="fas fa-map-marker-alt text-slate-400 dark:text-slate-500"></i> {{ $bin->location ?? '-' }}
                            </div>
                        </div>
                        <span id="badge-grid-{{ $bin->id }}" class="px-2.5 py-1 text-xs font-bold rounded-full whitespace-nowrap {{ $badgeStyle }}">{{ $label }}</span>
                    </div>

                    <!-- Circular Progress Gauge (SVG) -->
                    <div class="flex justify-center items-center my-4">
                        <div class="relative">
                            <svg width="110" height="110" class="transform -rotate-90">
                                <circle cx="55" cy="55" r="44" stroke="currentColor" stroke-width="7" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                                <circle id="circle-grid-{{ $bin->id }}" cx="55" cy="55" r="44" stroke="{{ $c }}" stroke-width="7" fill="transparent"
                                        stroke-dasharray="276.46" stroke-dashoffset="{{ 276.46 - (276.46 * $perc) / 100 }}"
                                        stroke-linecap="round" class="transition-all duration-700" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                <div id="perc-grid-{{ $bin->id }}" class="text-2xl font-extrabold text-slate-800 dark:text-slate-100" style="color: {{ $c }}">{{ $isConn ? $bin->percentage . '%' : '-' }}</div>
                                <div class="text-[8px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mt-0.5">{{ $isConn ? 'Penuh' : 'Offline' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mb-2">
                        <div id="bar-grid-{{ $bin->id }}" class="h-full rounded-full transition-all duration-700" style="width:{{ $perc }}%;background-color:{{ $c }}"></div>
                    </div>

                    <!-- Info -->
                    <div class="flex justify-between items-center text-xs text-slate-500 dark:text-slate-400 font-semibold mb-3">
                        <span id="liter-grid-{{ $bin->id }}">
                            {{ $isConn ? round($bin->percentage * $bin->max_depth_cm / 100) . ' cm terisi' : '- cm terisi' }}
                        </span>
                        <span>
                            Cap: {{ $bin->max_depth_cm }} cm
                        </span>
                    </div>

                    <!-- Tinggi Sampah & Sisa Ruang -->
                    <div class="grid grid-cols-2 gap-2 text-xs border-t border-slate-100 dark:border-slate-800/60 py-3 my-1">
                        <div class="text-slate-500 dark:text-slate-400 font-medium">Tinggi Sampah: <b id="tinggi-grid-{{ $bin->id }}" class="text-blue-500 dark:text-emerald-400 block font-semibold text-sm mt-0.5">{{ $isConn && $bin->tinggi_sampah !== null ? $bin->tinggi_sampah . ' cm' : '-' }}</b></div>
                        <div class="text-slate-500 dark:text-slate-400 font-medium">Sisa Ruang: <b id="sisa-grid-{{ $bin->id }}" class="text-emerald-600 dark:text-emerald-400 block font-semibold text-sm mt-0.5">{{ $isConn && $bin->sisa_ruang !== null ? $bin->sisa_ruang . ' cm' : '-' }}</b></div>
                    </div>

                    <div class="flex justify-between items-center text-[10px] text-slate-400 dark:text-slate-500 border-t border-slate-100 dark:border-slate-800/60 pt-3">
                        <span id="dist-grid-{{ $bin->id }}" class="font-medium"><i class="fas fa-ruler-vertical"></i> Jarak: {{ $isConn ? $bin->distance_cm . ' cm' : '-' }}</span>
                        <span id="coords-grid-{{ $bin->id }}" class="font-mono">
                            <i class="fas fa-satellite"></i> 
                            {{ $isConn && $bin->latitude ? number_format($bin->latitude,6).','.number_format($bin->longitude,6) : 'Menunggu IoT...' }}
                        </span>
                    </div>

                    <!-- Urgent Alert Container -->
                    <div id="status-container-{{ $bin->id }}" class="mt-4" style="display: {{ $isConn && $bin->percentage > 85 ? 'block' : 'none' }}">
                        <div class="p-2.5 rounded-xl text-center bg-rose-500/10 text-rose-600 dark:text-rose-400 text-xs font-semibold border border-rose-500/20 animate-pulse">
                            ⚠️ Segera kosongkan tong sampah ini!
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="grid grid-cols-2 gap-3 mt-5 pt-3 border-t border-slate-100 dark:border-slate-800/60">
                        <a href="{{ route('admin.trash-bins.edit', $bin->id) }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold border border-amber-500/35 text-amber-600 dark:text-amber-400 hover:bg-amber-500/10 dark:hover:bg-amber-500/20 rounded-xl transition-all text-center" title="Edit Bin">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.trash-bins.destroy', $bin->id) }}" method="POST" class="w-full"
                              onsubmit="return confirmAction(event, 'Hapus bin ini?')">
                            @csrf @method('DELETE')
                            <button class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold border border-red-500/35 text-red-600 dark:text-red-400 hover:bg-red-500/10 dark:hover:bg-red-500/20 rounded-xl transition-all cursor-pointer" title="Hapus Bin">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center text-slate-400 dark:text-slate-500 py-16">
                <i class="fas fa-trash fa-3x mb-4 opacity-40"></i>
                <div class="text-sm font-medium">Belum ada data tong sampah</div>
            </div>
            @endforelse
        </div>

        <!-- 2. TABLE VIEW (INTERACTIVE CRUD TABLE) -->
        <div id="bins-table-wrapper" style="display: none;">
            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <table class="w-full border-collapse text-left" id="bins-table">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Lokasi</th>
                            <th class="px-6 py-4">Volume</th>
                            <th class="px-6 py-4">Jarak Sensor</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Koordinat</th>
                            <th class="px-6 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150 dark:divide-slate-800/60">
                        @forelse($bins as $bin)
                        @php
                            $isConn = $bin->is_connected;
                            $perc = $isConn ? $bin->percentage : 0;
                            if (!$isConn) {
                                $badgeStyle = 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20';
                                $label = 'Offline';
                            } else {
                                if ($perc > 85) {
                                    $badgeStyle = 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20';
                                    $label = 'Full';
                                } elseif ($perc < 25) {
                                    $badgeStyle = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
                                    $label = 'Empty';
                                } else {
                                    $badgeStyle = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
                                    $label = 'Normal';
                                }
                            }
                        @endphp
                        <tr class="bin-table-row hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-all">
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 font-mono">{{ $bin->id }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-800 dark:text-slate-100">{{ $bin->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $bin->location ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                        <div id="bar-table-{{ $bin->id }}" class="h-full rounded-full transition-all duration-700" style="width:{{ $perc }}%; background-color: {{ !$isConn ? '#64748b' : ($perc > 85 ? '#f43f5e' : ($perc < 25 ? '#10b981' : '#f59e0b')) }}"></div>
                                    </div>
                                    <small id="perc-table-{{ $bin->id }}" class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $isConn ? $bin->percentage . '%' : '-' }}</small>
                                </div>
                            </td>
                            <td id="dist-table-{{ $bin->id }}" class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">{{ $isConn ? ($bin->distance_cm ?? '-') . ' cm' : '-' }}</td>
                            <td class="px-6 py-4"><span id="badge-table-{{ $bin->id }}" class="px-2.5 py-1 text-xs font-bold rounded-full whitespace-nowrap {{ $badgeStyle }}">{{ $label }}</span></td>
                            <td class="px-6 py-4">
                                <small id="coords-table-{{ $bin->id }}" class="font-mono text-slate-500 dark:text-slate-400 text-xs">
                                    {{ $isConn && $bin->latitude ? number_format($bin->latitude,6).', '.number_format($bin->longitude,6) : 'Menunggu data ...' }}
                                </small>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.trash-bins.edit', $bin->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-amber-500 hover:bg-amber-500/10 rounded-lg border border-amber-500/20 transition-all" title="Edit Bin">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.trash-bins.destroy', $bin->id) }}" method="POST" class="inline"
                                          onsubmit="return confirmAction(event, 'Hapus bin ini?')">
                                        @csrf @method('DELETE')
                                        <button class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:bg-red-500/10 rounded-lg border border-red-500/20 cursor-pointer transition-all" title="Hapus Bin">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                <i class="fas fa-trash fa-2x mb-3 opacity-40"></i>
                                <div class="text-sm font-medium">Belum ada data tong sampah</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB ANALITIK -->
    <div id="tab-analitik" class="tab-content-area hidden p-5">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-2xl p-5">
                <h6 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-500"></i> Tren Volume Mingguan
                </h6>
                <div class="relative h-[280px] w-full">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
            <div class="lg:col-span-1 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-2xl p-5">
                <h6 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-purple-500"></i> Tren Volume Bulanan
                </h6>
                <div class="relative h-[280px] w-full">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var map = new maplibregl.Map({
        container: 'map',
        attributionControl: false,
        style: document.documentElement.classList.contains('dark')
            ? 'https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json'
            : 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
        center: [113.704272, -8.166102],
        zoom: 13,
        pitch: 30
    });
    map.addControl(new maplibregl.NavigationControl(), 'top-right');

    map.on('mousemove', function(e) {
        document.getElementById('cursor-lng').textContent = e.lngLat.lng.toFixed(6);
        document.getElementById('cursor-lat').textContent = e.lngLat.lat.toFixed(6);
    });
    map.on('mouseout', function() {
        document.getElementById('cursor-lng').textContent = '-';
        document.getElementById('cursor-lat').textContent = '-';
    });

    var bins = @json($bins->values());
    var markers = {};
    var userMarker = null;
    var routingActive = false;

    // Listen to theme changes from layouts/main
    window.addEventListener('theme-changed', function(e) {
        var mapStyle = e.detail.theme === 'dark' 
            ? 'https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json'
            : 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json';
        map.setStyle(mapStyle);
    });

    function drawPolylines(binsList) {
        var features = [];
        binsList.forEach(function(b1, i) {
            binsList.forEach(function(b2, j) {
                if (i < j && b1.is_connected && b2.is_connected && b1.latitude && b2.latitude) {
                    features.push({
                        type: 'Feature',
                        properties: {},
                        geometry: {
                            type: 'LineString',
                            coordinates: [
                                [b1.longitude, b1.latitude],
                                [b2.longitude, b2.latitude]
                            ]
                        }
                    });
                }
            });
        });

        var geojson = {
            type: 'FeatureCollection',
            features: features
        };

        var source = map.getSource('connections');
        if (source) {
            source.setData(geojson);
        } else {
            if (map.loaded()) {
                addConnectionsLayer(geojson);
            } else {
                map.on('load', function() {
                    addConnectionsLayer(geojson);
                });
            }
        }
    }

    function addConnectionsLayer(geojson) {
        if (map.getSource('connections')) return;
        map.addSource('connections', {
            type: 'geojson',
            data: geojson
        });
        map.addLayer({
            id: 'connections',
            type: 'line',
            source: 'connections',
            paint: {
                'line-color': '#94a3b8',
                'line-width': 1,
                'line-dasharray': [5, 8],
                'line-opacity': 0.4
            }
        });
    }

    function updateOrCreateMarker(bin) {
        if (!bin.is_connected || !bin.latitude || !bin.longitude) {
            if (markers[bin.id]) {
                markers[bin.id].remove();
                delete markers[bin.id];
            }
            return;
        }

        var percentage = bin.percentage;
        var color = percentage > 85 ? '#f43f5e' : (percentage < 25 ? '#10b981' : '#f59e0b');
        var label = percentage > 85 ? 'Full' : (percentage < 25 ? 'Empty' : 'Normal');

        var popupContent = `
            <div style="min-width:190px;font-family:'Outfit',sans-serif">
                <div style="font-weight:700;font-size:14px;margin-bottom:2px" class="text-slate-900 dark:text-white">${bin.name}</div>
                <div style="font-size:11px;margin-bottom:8px" class="text-slate-500 dark:text-slate-400"><i class="fas fa-map-marker-alt"></i> ${bin.location ?? '-'}</div>

                <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-weight:600">
                    <span style="font-size:11px">Volume</span>
                    <b style="color:${color};font-size:12px">${percentage}%</b>
                </div>
                <div style="height:6px;background:#e2e8f0;border-radius:3px;margin-bottom:8px;overflow:hidden" class="dark:bg-slate-800">
                    <div style="height:100%;width:${percentage}%;background:${color};border-radius:3px"></div>
                </div>

                <div style="display:flex;justify-content:space-between;font-size:10px;margin-bottom:6px" class="text-slate-500 dark:text-slate-400">
                    <span>Jarak: <b>${bin.distance_cm ?? '-'} cm</b></span>
                    <span style="color:${color};font-weight:700">${label}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:10px;margin-bottom:10px" class="text-slate-500 dark:text-slate-400">
                    <span>Tinggi: <b>${bin.tinggi_sampah ?? '-'} cm</b></span>
                    <span>Sisa: <b>${bin.sisa_ruang ?? '-'} cm</b></span>
                </div>

                <div id="dir-${bin.id}" style="display:none;padding:8px;background:#f0f9ff;border-radius:6px;font-size:11px;margin-bottom:8px;color:#1e3a8a" class="dark:bg-blue-950/40 dark:text-blue-200">
                    <div style="display:flex;justify-content:space-between;margin-bottom:2px">
                        <span>Arah:</span>
                        <b id="compass-${bin.id}">-</b>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <span>Jarak:</span>
                        <b id="dist-${bin.id}">-</b>
                    </div>
                </div>

                <button onclick="getDirectionTo(${bin.latitude}, ${bin.longitude}, ${bin.id})"
                    style="width:100%;padding:7px;background:#2563eb;color:white;border:none;border-radius:8px;cursor:pointer;font-size:11px;font-weight:600;margin-bottom:4px">
                    🧭 Cari Arah ke Sini
                </button>
                <button onclick="openGoogleMaps(${bin.latitude}, ${bin.longitude})"
                    style="width:100%;padding:7px;background:#10b981;color:white;border:none;border-radius:8px;cursor:pointer;font-size:11px;font-weight:600">
                    🗺️ Google Maps
                </button>
            </div>
        `;

        if (markers[bin.id]) {
            var marker = markers[bin.id];
            marker.setLngLat([bin.longitude, bin.latitude]);
            marker.getPopup().setHTML(popupContent);
            
            var el = marker.getElement();
            el.innerHTML = `
                <div class="relative">
                    <div class="w-11 h-11 rounded-full border-3 bg-white dark:bg-slate-900 flex items-center justify-center text-[10px] font-black shadow-lg transition-transform hover:scale-105 duration-200" style="border-color: ${color}; color: ${color}">${percentage}%</div>
                    ${percentage > 85 ? `<div class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900 animate-ping"></div>` : ''}
                </div>
            `;
        } else {
            var el = document.createElement('div');
            el.className = 'custom-trash-marker cursor-pointer group';
            el.innerHTML = `
                <div class="relative">
                    <div class="w-11 h-11 rounded-full border-3 bg-white dark:bg-slate-900 flex items-center justify-center text-[10px] font-black shadow-lg transition-transform hover:scale-105 duration-200" style="border-color: ${color}; color: ${color}">${percentage}%</div>
                    ${percentage > 85 ? `<div class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900 animate-ping"></div>` : ''}
                </div>
            `;

            el.addEventListener('click', function() {
                map.easeTo({
                    center: [bin.longitude, bin.latitude],
                    zoom: 15,
                    duration: 1000
                });
            });

            var popup = new maplibregl.Popup({ offset: 25 })
                .setHTML(popupContent);

            markers[bin.id] = new maplibregl.Marker({ element: el })
                .setLngLat([bin.longitude, bin.latitude])
                .setPopup(popup)
                .addTo(map);
        }
    }

    map.on('load', function() {
        drawPolylines(bins);
        bins.forEach(updateOrCreateMarker);
        
        var validBins = bins.filter(b => b.is_connected && b.latitude && b.longitude);
        if (validBins.length > 0) {
            var bounds = new maplibregl.LngLatBounds();
            validBins.forEach(function(b) {
                bounds.extend([b.longitude, b.latitude]);
            });
            map.fitBounds(bounds, { padding: 50 });
        }
    });

    var weeklyCtx = document.getElementById('weeklyChart')?.getContext('2d');
    if (weeklyCtx) {
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: @json($weeklyLabels),
                datasets: [{
                    label: 'Rata-rata Volume (%)',
                    data: @json($weeklyData),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    fill: true, tension: 0.4, pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 100 } }
            }
        });
    }

    var monthlyCtx = document.getElementById('monthlyChart')?.getContext('2d');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: @json($monthlyLabels),
                datasets: [{
                    data: @json($monthlyData),
                    backgroundColor: 'rgba(124,58,237,0.7)',
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
    }

    // Filter Bins (Search)
    function filterBins(val) {
        let query = val.toLowerCase();
        
        // Filter Grid Items
        document.querySelectorAll('.bin-grid-item').forEach(item => {
            item.style.display = item.dataset.searchable.includes(query) ? '' : 'none';
        });
        
        // Filter Table Rows
        document.querySelectorAll('#bins-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    }

    // Toggle View Mode (Grid / Table)
    function setViewMode(mode) {
        localStorage.setItem('admin_bin_view_mode', mode);
        const gridBtn = document.getElementById('btn-view-grid');
        const tableBtn = document.getElementById('btn-view-table');
        
        const activeClasses = ['bg-white', 'dark:bg-slate-800', 'text-slate-800', 'dark:text-white', 'shadow-sm'];
        const inactiveClasses = ['text-slate-500', 'hover:text-slate-800', 'dark:hover:text-slate-300'];

        if (mode === 'grid') {
            document.getElementById('bins-grid').style.display = 'grid';
            document.getElementById('bins-table-wrapper').style.display = 'none';
            
            gridBtn.classList.add(...activeClasses);
            gridBtn.classList.remove(...inactiveClasses);
            
            tableBtn.classList.remove(...activeClasses);
            tableBtn.classList.add(...inactiveClasses);
        } else {
            document.getElementById('bins-grid').style.display = 'none';
            document.getElementById('bins-table-wrapper').style.display = 'block';
            
            tableBtn.classList.add(...activeClasses);
            tableBtn.classList.remove(...inactiveClasses);
            
            gridBtn.classList.remove(...activeClasses);
            gridBtn.classList.add(...inactiveClasses);
        }
    }

    // Hitung jarak (Haversine formula)
    function getDistance(lat1, lng1, lat2, lng2) {
        var R = 6371000; // Radius bumi dalam meter
        var dLat = (lat2-lat1)*Math.PI/180;
        var dLng = (lng2-lng1)*Math.PI/180;
        var a = Math.sin(dLat/2)*Math.sin(dLat/2)+
                Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*
                Math.sin(dLng/2)*Math.sin(dLng/2);
        return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
    }

    // Hitung bearing sudut kompas
    function getBearing(lat1, lng1, lat2, lng2) {
        var dLng = (lng2-lng1)*Math.PI/180;
        lat1 = lat1*Math.PI/180;
        lat2 = lat2*Math.PI/180;
        var y = Math.sin(dLng)*Math.cos(lat2);
        var x = Math.cos(lat1)*Math.sin(lat2)-Math.sin(lat1)*Math.cos(lat2)*Math.cos(dLng);
        return ((Math.atan2(y,x)*180/Math.PI)+360)%360;
    }

    function bearingToCompass(b) {
        var d = ['Utara','Timur Laut','Timur','Tenggara','Selatan','Barat Daya','Barat','Barat Laut'];
        return d[Math.round(b/45)%8] + ' (' + Math.round(b) + '°)';
    }

    function formatDist(m) {
        return m >= 1000 ? (m/1000).toFixed(2)+' km' : Math.round(m)+' m';
    }

    function clearRoute() {
        if (map.getLayer('route')) map.removeLayer('route');
        if (map.getSource('route')) map.removeSource('route');
        routingActive = false;
    }

    function getDirectionTo(binLat, binLng, binId) {
        navigator.geolocation.getCurrentPosition(async function(pos) {
            var myLat = pos.coords.latitude;
            var myLng = pos.coords.longitude;

            if (userMarker) userMarker.remove();
            
            var el = document.createElement('div');
            el.innerHTML = `<div style="width:16px;height:16px;background:#2563eb;border-radius:50%;border:3px solid white;box-shadow:0 0 0 6px rgba(37,99,235,0.25)"></div>`;
            
            userMarker = new maplibregl.Marker({ element: el })
                .setLngLat([myLng, myLat])
                .addTo(map);

            var start = `${myLng},${myLat}`;
            var end = `${binLng},${binLat}`;
            
            try {
                var response = await fetch(`https://router.project-osrm.org/route/v1/driving/${start};${end}?overview=full&geometries=geojson`);
                var data = await response.json();
                
                if (data.code !== 'Ok') {
                    showOSRMBackupLine(myLat, myLng, binLat, binLng, binId);
                    return;
                }

                var route = data.routes[0];
                var coordinates = route.geometry.coordinates;
                var distanceKm = (route.distance / 1000).toFixed(1);
                var durationMin = Math.round(route.duration / 60);

                var routeData = {
                    type: 'Feature',
                    properties: {},
                    geometry: {
                        type: 'LineString',
                        coordinates: coordinates
                    }
                };

                clearRoute();

                map.addSource('route', {
                    type: 'geojson',
                    data: routeData
                });

                map.addLayer({
                    id: 'route',
                    type: 'line',
                    source: 'route',
                    layout: {
                        'line-join': 'round',
                        'line-cap': 'round'
                    },
                    paint: {
                        'line-color': '#0ea5e9',
                        'line-width': 5,
                        'line-opacity': 0.8
                    }
                });

                routingActive = true;

                var dirEl = document.getElementById('dir-' + binId);
                if (dirEl) {
                    dirEl.style.display = 'block';
                    document.getElementById('compass-' + binId).textContent = durationMin + ' mnt';
                    document.getElementById('dist-' + binId).textContent = distanceKm + ' km';
                }

                var bounds = coordinates.reduce(function(bounds, coord) {
                    return bounds.extend(coord);
                }, new maplibregl.LngLatBounds(coordinates[0], coordinates[0]));
                
                map.fitBounds(bounds, { padding: 50 });

            } catch (e) {
                console.error("OSRM failed, using backup line:", e);
                showOSRMBackupLine(myLat, myLng, binLat, binLng, binId);
            }

        }, function() {
            Swal.fire({
                title: 'GPS Tidak Aktif',
                text: 'Gagal mendapatkan koordinat lokasi. Pastikan GPS aktif dan berikan izin lokasi!',
                icon: 'error',
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Tutup',
                background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#1e293b',
                borderRadius: '16px'
            });
        });
    }

    function showOSRMBackupLine(myLat, myLng, binLat, binLng, binId) {
        clearRoute();
        var routeData = {
            type: 'Feature',
            properties: {},
            geometry: {
                type: 'LineString',
                coordinates: [
                    [myLng, myLat],
                    [binLng, binLat]
                ]
            }
        };
        map.addSource('route', {
            type: 'geojson',
            data: routeData
        });
        map.addLayer({
            id: 'route',
            type: 'line',
            source: 'route',
            paint: {
                'line-color': '#f43f5e',
                'line-width': 3,
                'line-dasharray': [4, 4]
            }
        });

        var bearing = getBearing(myLat, myLng, binLat, binLng);
        var dist = getDistance(myLat, myLng, binLat, binLng);

        var dirEl = document.getElementById('dir-' + binId);
        if (dirEl) {
            dirEl.style.display = 'block';
            document.getElementById('compass-' + binId).textContent = bearingToCompass(bearing);
            document.getElementById('dist-' + binId).textContent = formatDist(dist);
        }

        var bounds = new maplibregl.LngLatBounds([myLng, myLat], [binLng, binLat]);
        map.fitBounds(bounds, { padding: 50 });
    }

    function openGoogleMaps(lat, lng) {
        window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`, '_blank');
    }

    function flyToMap(lat, lng, binId) {
        if (lat && lng) {
            map.flyTo({
                center: [lng, lat],
                zoom: 17,
                duration: 1200
            });
            if(markers[binId]) {
                markers[binId].togglePopup();
            }
        }
    }

    function filterMapLocations(val) {
        let query = val.toLowerCase();
        document.querySelectorAll('.map-loc-item').forEach(item => {
            item.style.display = item.dataset.searchable.includes(query) ? '' : 'none';
        });
    }

    // Dynamic Tailwind class definitions for status mapping
    var twColors = {
        'secondary': {
            bg: 'bg-slate-500/10',
            text: 'text-slate-600 dark:text-slate-400',
            border: 'border-slate-500/20',
            barBg: 'bg-slate-400 dark:bg-slate-600',
            hex: '#64748b'
        },
        'danger': {
            bg: 'bg-rose-500/10',
            text: 'text-rose-600 dark:text-rose-400',
            border: 'border-rose-500/20',
            barBg: 'bg-rose-500',
            hex: '#f43f5e'
        },
        'success': {
            bg: 'bg-emerald-500/10',
            text: 'text-emerald-600 dark:text-emerald-400',
            border: 'border-emerald-500/20',
            barBg: 'bg-emerald-500',
            hex: '#10b981'
        },
        'warning': {
            bg: 'bg-amber-500/10',
            text: 'text-amber-600 dark:text-amber-400',
            border: 'border-amber-500/20',
            barBg: 'bg-amber-500',
            hex: '#f59e0b'
        }
    };

    // Real-Time Live Auto Refresh (Anti-Cache)
    function triggerAutoRefresh() {
        let url = '{{ route("admin.trash-bins.index") }}?api=1&_t=' + new Date().getTime();
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Respon server bukan OK');
            return response.json();
        })
        .then(result => {
            if(result.status === 'success') {
                // Update polylines dynamically
                drawPolylines(result.data);

                // 0. UPDATE STAT CARDS
                let totalBinsCount = result.data.length;
                let connectedBins = result.data.filter(b => b.is_connected);
                let criticalBinsCount = connectedBins.filter(b => b.percentage > 85).length;
                let activeBinsCount = result.data.filter(b => b.is_active).length;
                let avgFillVal = Math.round(connectedBins.reduce((acc, b) => acc + Number(b.percentage), 0) / (connectedBins.length || 1));

                let statTotal = document.getElementById('stat-total-bins');
                let statCritical = document.getElementById('stat-critical-bins');
                let statActive = document.getElementById('stat-active-bins');
                let statAvg = document.getElementById('stat-avg-fill');

                if(statTotal) statTotal.innerText = totalBinsCount;
                if(statCritical) statCritical.innerText = criticalBinsCount;
                if(statActive) statActive.innerText = activeBinsCount;
                if(statAvg) statAvg.innerText = avgFillVal + '%';

                result.data.forEach(bin => {
                    let isConn = bin.is_connected;
                    let perc = isConn ? bin.percentage : 0;
                    let key = !isConn ? 'secondary' : (perc > 85 ? 'danger' : (perc < 25 ? 'success' : 'warning'));
                    let style = twColors[key];
                    let label = !isConn ? 'Offline' : (perc > 85 ? 'Full' : (perc < 25 ? 'Empty' : 'Normal'));
                    
                    // 1. UPDATE TABLE VIEW
                    let textPercTable = document.getElementById('perc-table-' + bin.id);
                    let textDistTable = document.getElementById('dist-table-' + bin.id);
                    let barTable = document.getElementById('bar-table-' + bin.id);
                    let badgeTable = document.getElementById('badge-table-' + bin.id);
                    let coordsTable = document.getElementById('coords-table-' + bin.id);

                    if(textPercTable) textPercTable.innerText = isConn ? bin.percentage + '%' : '-';
                    if(textDistTable) textDistTable.innerText = isConn ? bin.distance_cm + ' cm' : '-';
                    if(barTable) {
                        barTable.style.width = perc + '%';
                        barTable.style.backgroundColor = style.hex;
                    }
                    if(badgeTable) {
                        badgeTable.innerText = label;
                        badgeTable.className = `px-2.5 py-1 text-xs font-bold rounded-full whitespace-nowrap ${style.bg} ${style.text} ${style.border}`; 
                    }
                    if(coordsTable) {
                        coordsTable.innerText = isConn && bin.latitude ? Number(bin.latitude).toFixed(6) + ', ' + Number(bin.longitude).toFixed(6) : 'Menunggu data ...';
                    }

                    // 2. UPDATE GRID VIEW
                    let textPercGrid = document.getElementById('perc-grid-' + bin.id);
                    let textDistGrid = document.getElementById('dist-grid-' + bin.id);
                    let barGrid = document.getElementById('bar-grid-' + bin.id);
                    let badgeGrid = document.getElementById('badge-grid-' + bin.id);
                    let circleGrid = document.getElementById('circle-grid-' + bin.id);
                    let statusContainer = document.getElementById('status-container-' + bin.id);
                    let emptyBtn = document.getElementById('empty-btn-' + bin.id);
                    let literGrid = document.getElementById('liter-grid-' + bin.id);
                    let coordsGrid = document.getElementById('coords-grid-' + bin.id);

                    if(textPercGrid) {
                        textPercGrid.innerText = isConn ? bin.percentage + '%' : '-';
                        textPercGrid.style.color = style.hex;
                    }
                    if(textDistGrid) textDistGrid.innerHTML = '<i class="fas fa-ruler-vertical"></i> Jarak: ' + (isConn ? bin.distance_cm + ' cm' : '-');
                    if(barGrid) {
                        barGrid.style.width = perc + '%';
                        barGrid.style.backgroundColor = style.hex;
                    }
                    if(badgeGrid) {
                        badgeGrid.innerText = label;
                        badgeGrid.className = `px-2.5 py-1 text-xs font-bold rounded-full whitespace-nowrap ${style.bg} ${style.text} ${style.border}`;
                    }
                    if(circleGrid) {
                        let circum = 276.46;
                        circleGrid.style.strokeDashoffset = circum - (circum * perc) / 100;
                        circleGrid.setAttribute('stroke', style.hex);
                    }
                    if(statusContainer) {
                        statusContainer.style.display = isConn && bin.percentage > 85 ? 'block' : 'none';
                    }
                    if(emptyBtn) {
                        emptyBtn.style.display = isConn && bin.percentage > 85 ? 'block' : 'none';
                    }
                    if(literGrid) {
                        literGrid.innerText = isConn ? Math.round(bin.percentage * bin.max_depth_cm / 100) + ' cm terisi' : '- cm terisi';
                    }
                    let tinggiGrid = document.getElementById('tinggi-grid-' + bin.id);
                    let sisaGrid = document.getElementById('sisa-grid-' + bin.id);
                    if(tinggiGrid) {
                        tinggiGrid.innerHTML = isConn && bin.tinggi_sampah !== null && bin.tinggi_sampah !== undefined ? bin.tinggi_sampah + ' cm' : '-';
                    }
                    if(sisaGrid) {
                        sisaGrid.innerHTML = isConn && bin.sisa_ruang !== null && bin.sisa_ruang !== undefined ? bin.sisa_ruang + ' cm' : '-';
                    }
                    if(coordsGrid) {
                        coordsGrid.innerHTML = '<i class="fas fa-satellite"></i> ' + (isConn && bin.latitude ? Number(bin.latitude).toFixed(6) + ',' + Number(bin.longitude).toFixed(6) : 'Menunggu data ...');
                    }

                    // 3. UPDATE MAPMARKERS POPUP & ICON & POSITION
                    updateOrCreateMarker(bin);

                    // 4. UPDATE SIDEBAR MAP LIST
                    let badgeMapList = document.getElementById('badge-maplist-' + bin.id);
                    if(badgeMapList) {
                        badgeMapList.innerText = bin.percentage + '%';
                        badgeMapList.className = `px-2 py-1 text-xs font-extrabold rounded-lg min-w-[42px] text-center ${style.bg} ${style.text} ${style.border}`;
                    }

                    let mapLocItem = document.getElementById('map-loc-item-' + bin.id);
                    if (mapLocItem && bin.latitude && bin.longitude) {
                        mapLocItem.setAttribute('onclick', `flyToMap(${bin.latitude}, ${bin.longitude}, ${bin.id})`);
                    }
                });

                // Update Sidebar Reports Badge in Real-time
                let reportsBadge = document.getElementById('sidebar-reports-badge');
                if (reportsBadge) {
                    if (typeof result.pendingReports !== 'undefined') {
                        if (result.pendingReports > 0) {
                            reportsBadge.innerText = result.pendingReports;
                            reportsBadge.style.display = 'flex';
                        } else {
                            reportsBadge.style.display = 'none';
                        }
                    }
                }
            }
        })
        .catch(error => console.error('Gagal mengambil data:', error));
    }

    // Initialize Default View Mode and Start Auto Refresh
    document.addEventListener("DOMContentLoaded", function() {
        let savedMode = localStorage.getItem('admin_bin_view_mode') || 'grid';
        setViewMode(savedMode);
        
        // Start live polling every 1 second (1000ms) for snappy real-time update
        setInterval(triggerAutoRefresh, 1000);
    });
</script>
@endpush