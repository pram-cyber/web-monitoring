@extends('layouts.main')

@section('content')

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
        <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Bins</div>
            <div class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ $totalBins }}</div>
        </div>
        <div class="w-12 h-12 bg-blue-50 dark:bg-blue-950/45 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-inner">
            <i class="fas fa-trash text-lg"></i>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
        <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Critical Bins</div>
            <div class="text-3xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">{{ $criticalBins }}</div>
        </div>
        <div class="w-12 h-12 bg-rose-50 dark:bg-rose-950/45 rounded-xl flex items-center justify-center text-rose-600 dark:text-rose-400 shadow-inner">
            <i class="fas fa-exclamation-triangle text-lg animate-pulse"></i>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
        <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Laporan Saya</div>
            <div class="text-3xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ $myReports }}</div>
        </div>
        <div class="w-12 h-12 bg-amber-50 dark:bg-amber-950/45 rounded-xl flex items-center justify-center text-amber-600 dark:text-amber-400 shadow-inner">
            <i class="fas fa-flag text-lg"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left: Table/Grid Monitoring List -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mb-5 gap-3">
            <div class="flex items-center gap-3">
                <h6 class="font-bold text-slate-800 dark:text-slate-100 text-sm flex items-center gap-2">
                    <i class="fas fa-list text-blue-500"></i> Daftar Pemantauan
                </h6>
                
                <!-- View Mode Button Group -->
                <div class="inline-flex bg-slate-100 dark:bg-slate-950 p-1 rounded-xl border border-slate-200/50 dark:border-slate-800/80 gap-1 select-none">
                    <button type="button" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg flex items-center gap-1.5 cursor-pointer transition-all duration-200 focus:outline-none" id="btn-view-grid" onclick="setViewMode('grid')" title="Tampilan Card Grid">
                        <i class="fas fa-th-large"></i> Grid
                    </button>
                    <button type="button" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg flex items-center gap-1.5 cursor-pointer transition-all duration-200 focus:outline-none" id="btn-view-table" onclick="setViewMode('table')" title="Tampilan Tabel Kelola">
                        <i class="fas fa-table"></i> Tabel
                    </button>
                </div>
            </div>
            
            <input type="text" class="w-full sm:max-w-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all text-slate-800 dark:text-slate-100" id="search-bins"
                   placeholder="Cari bin..." onkeyup="filterBins(this.value)">
        </div>

        <!-- 1. GRID VIEW -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4" id="bins-grid" style="display: none;">
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
                $circumference = 276.46; // r=44
                $dashoffset = $circumference - ($circumference * $perc) / 100;
            @endphp
            <div class="bin-grid-item" data-searchable="{{ strtolower($bin->name . ' ' . $bin->location) }}">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-4 flex flex-col h-full shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex justify-between items-start mb-3 gap-2">
                        <div class="min-w-0">
                            <div class="font-bold text-slate-800 dark:text-slate-100 text-sm truncate" title="{{ $bin->name }}">{{ $bin->name }}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5 truncate">
                                <i class="fas fa-map-marker-alt"></i> {{ $bin->location ?? '-' }}
                            </div>
                        </div>
                        <span id="badge-grid-{{ $bin->id }}" class="px-2 py-0.5 text-[10px] font-bold rounded-full whitespace-nowrap {{ $badgeStyle }}">{{ $label }}</span>
                    </div>

                    <!-- Circular Progress Gauge (SVG) -->
                    <div class="flex justify-center items-center my-3">
                        <div class="relative">
                            <svg width="100" height="100" class="transform -rotate-90">
                                <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="6" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                                <circle id="circle-grid-{{ $bin->id }}" cx="50" cy="50" r="40" stroke="{{ $c }}" stroke-width="6" fill="transparent"
                                        stroke-dasharray="251.2" stroke-dashoffset="{{ 251.2 - (251.2 * $perc) / 100 }}"
                                        stroke-linecap="round" class="transition-all duration-700" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                <div id="perc-grid-{{ $bin->id }}" class="text-xl font-extrabold" style="color: {{ $c }}">{{ $isConn ? $bin->percentage . '%' : '-' }}</div>
                                <div class="text-[8px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">{{ $isConn ? 'Penuh' : 'Offline' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden mb-2">
                        <div id="bar-grid-{{ $bin->id }}" class="h-full rounded-full transition-all duration-700" style="width:{{ $perc }}%;background-color:{{ $c }}"></div>
                    </div>

                    <!-- Info -->
                    <div class="flex justify-between items-center text-[10px] text-slate-500 dark:text-slate-400 font-semibold mb-2">
                        <span id="liter-grid-{{ $bin->id }}">
                            {{ $isConn ? round($bin->percentage * $bin->max_depth_cm / 100) . ' cm terisi' : '- cm terisi' }}
                        </span>
                        <span>
                            Cap: {{ $bin->max_depth_cm }} cm
                        </span>
                    </div>

                    <!-- Tinggi Sampah & Sisa Ruang -->
                    <div class="grid grid-cols-2 gap-2 text-[10px] border-t border-slate-100 dark:border-slate-800/60 py-2.5 my-1">
                        <div class="text-slate-500 dark:text-slate-400 font-medium">Tinggi Sampah: <b id="tinggi-grid-{{ $bin->id }}" class="text-blue-500 dark:text-emerald-450 block font-semibold text-xs mt-0.5">{{ $isConn && $bin->tinggi_sampah !== null ? $bin->tinggi_sampah . ' cm' : '-' }}</b></div>
                        <div class="text-slate-500 dark:text-slate-400 font-medium">Sisa Ruang: <b id="sisa-grid-{{ $bin->id }}" class="text-emerald-600 dark:text-emerald-450 block font-semibold text-xs mt-0.5">{{ $isConn && $bin->sisa_ruang !== null ? $bin->sisa_ruang . ' cm' : '-' }}</b></div>
                    </div>

                    <div class="flex justify-between items-center text-[9px] text-slate-450 dark:text-slate-500 border-t border-slate-100 dark:border-slate-800/60 pt-2.5">
                        <span id="dist-grid-{{ $bin->id }}"><i class="fas fa-ruler-vertical"></i> Jarak: {{ $isConn ? $bin->distance_cm . ' cm' : '-' }}</span>
                        <span id="coords-grid-{{ $bin->id }}" class="font-mono">
                            <i class="fas fa-satellite"></i> 
                            {{ $isConn && $bin->latitude ? number_format($bin->latitude,6).','.number_format($bin->longitude,6) : 'Menunggu IoT...' }}
                        </span>
                    </div>

                    <!-- Action buttons -->
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/60">
                        <button class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer" onclick="markEmpty({{ $bin->id }})">
                            <i class="fas fa-check"></i> Tandai Diambil
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center text-slate-400 dark:text-slate-500 py-12">
                Belum ada data tong sampah
            </div>
            @endforelse
        </div>

        <!-- 2. TABLE VIEW -->
        <div id="bins-table-wrapper" style="display: block;">
            <div class="overflow-x-auto rounded-xl border border-slate-150 dark:border-slate-800">
                <table class="w-full border-collapse text-left" id="bins-table">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Lokasi</th>
                            <th class="px-6 py-4">Volume</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150 dark:divide-slate-800/60">
                        @forelse($bins as $bin)
                        @php
                            $isConn = $bin->is_connected;
                            $perc = $isConn ? $bin->percentage : 0;
                            if (!$isConn) {
                                $badgeStyle = 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20';
                                $labelTable = 'Offline';
                            } else {
                                if ($perc > 85) {
                                    $badgeStyle = 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20';
                                    $labelTable = 'Full';
                                } elseif ($perc < 25) {
                                    $badgeStyle = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
                                    $labelTable = 'Empty';
                                } else {
                                    $badgeStyle = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
                                    $labelTable = 'Normal';
                                }
                            }
                        @endphp
                        <tr class="bin-table-row hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-all" data-searchable="{{ strtolower($bin->name . ' ' . $bin->location) }}">
                            <td class="px-6 py-4 text-sm font-bold text-slate-800 dark:text-slate-100">{{ $bin->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-550 dark:text-slate-400">{{ $bin->location ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                        <div id="bar-table-{{ $bin->id }}" class="h-full rounded-full transition-all duration-700" style="width:{{ $perc }}%; background-color: {{ !$isConn ? '#64748b' : ($perc > 85 ? '#f43f5e' : ($perc < 25 ? '#10b981' : '#f59e0b')) }}"></div>
                                    </div>
                                    <small id="perc-table-{{ $bin->id }}" class="text-xs font-bold text-slate-755 dark:text-slate-300">{{ $isConn ? $bin->percentage . '%' : '-' }}</small>
                                </div>
                            </td>
                            <td class="px-6 py-4"><span id="badge-table-{{ $bin->id }}" class="px-2.5 py-1 text-[10px] font-extrabold rounded-full whitespace-nowrap {{ $badgeStyle }}">
                                {{ $labelTable }}
                            </span></td>
                            <td class="px-6 py-4 text-right">
                                <button class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer" onclick="markEmpty({{ $bin->id }})">
                                    <i class="fas fa-check"></i> Tandai Diambil
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-450 dark:text-slate-500">Belum ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Dynamic Radius Settings & Profile -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Radius Notifikasi Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
            <h6 class="font-bold text-slate-850 dark:text-slate-100 text-sm mb-4 flex items-center gap-2">
                <i class="fas fa-bell text-amber-500 animate-swing"></i> Radius Notifikasi
            </h6>
            <form id="radius-form" onsubmit="updateSettingsRadius(event)">
                @csrf
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-slate-550 dark:text-slate-400">Jangkauan Notifikasi</label>
                        <span id="radius-val" class="font-black text-sm text-blue-600 dark:text-emerald-400 bg-blue-50 dark:bg-emerald-950/40 px-2 py-0.5 border border-blue-100 dark:border-emerald-500/20 rounded-lg">
                            {{ auth()->user()->notification_radius }}m
                        </span>
                    </div>

                    <input type="range" name="notification_radius" class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-lg appearance-none cursor-pointer accent-blue-600 dark:accent-emerald-500"
                           min="50" max="1000" step="50" 
                           value="{{ auth()->user()->notification_radius }}"
                           oninput="updateRadiusSlider(this.value)" id="radius-settings-slider">
                    
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-3.5 leading-relaxed">
                        Peringatan alarm dan notifikasi visual akan otomatis muncul jika ada tong sampah penuh (&gt;85%) di sekitar koordinat lokasi Anda dalam radius ini.
                    </p>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-xs font-bold bg-blue-600 dark:bg-emerald-600 hover:bg-blue-700 dark:hover:bg-emerald-700 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer">
                    <i class="fas fa-save text-[10px]"></i> Simpan Konfigurasi
                </button>
            </form>
        </div>

        <!-- Profil Saya Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
            <h6 class="font-bold text-slate-850 dark:text-slate-100 text-sm mb-4 flex items-center gap-2">
                <i class="fas fa-user-circle text-blue-500"></i> Profil Saya
            </h6>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 text-white flex items-center justify-center text-lg font-black shadow-md shadow-blue-500/10">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-grow">
                    <div class="font-bold text-slate-800 dark:text-slate-200 text-sm truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ auth()->user()->email }}</div>
                    <span class="inline-block px-2 py-0.5 text-[9px] font-extrabold bg-emerald-500/10 text-emerald-650 dark:text-emerald-450 border border-emerald-500/20 rounded-md mt-1.5">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Real-Time Notifications Container -->
<div id="realtime-notifications" class="fixed top-[85px] right-5 z-[9999] flex flex-col gap-2.5 max-w-[350px] pointer-events-none"></div>

<style>
.realtime-notif-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(244, 63, 94, 0.2);
    border-left: 4px solid #f43f5e;
    border-radius: 16px;
    padding: 14px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    width: 320px;
    pointer-events: auto;
    animation: slideInNotif 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    transition: all 0.3s ease;
}
.dark .realtime-notif-card {
    background: rgba(15, 23, 42, 0.85);
    border: 1px solid rgba(244, 63, 94, 0.3);
    border-left: 4px solid #f43f5e;
}
.realtime-notif-card:hover {
    transform: translateY(-2px);
}
@keyframes slideInNotif {
    from { transform: translateX(120%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
@keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(120%); opacity: 0; }
}
</style>

@endsection

@push('scripts')
<script>
    var bins = @json($bins->values());
    var userRadius = {{ auth()->user()->notification_radius ?? 100 }};
    var warnedBinIds = new Set(); // Melacak bin yang sudah bersuara / notif agar tidak spam
    var userLat = null;
    var userLng = null;

    // Web Audio API Synthesized Premium Chime Sound
    function playNotificationChime() {
        try {
            var AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            var ctx = new AudioContext();
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();
            
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            osc.type = 'sine';
            var now = ctx.currentTime;
            osc.frequency.setValueAtTime(523.25, now); // C5
            osc.frequency.setValueAtTime(659.25, now + 0.12); // E5
            osc.frequency.setValueAtTime(783.99, now + 0.24); // G5
            
            gain.gain.setValueAtTime(0, now);
            gain.gain.linearRampToValueAtTime(0.15, now + 0.05);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.6);
            
            osc.start(now);
            osc.stop(now + 0.6);
        } catch(e) {
            console.log("Audio play blocked or unsupported:", e);
        }
    }

    // Get My Location (isManual false for autoload silent logic)
    function getMyLocation(isManual = false) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;

            findNearby(userLat, userLng);
        }, function(err) {
            if (isManual) {
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
            } else {
                console.log("Silent location fetch failed (autoload):", err.message);
            }
        });
    }

    // Autoload Geolocation silently on page load
    document.addEventListener("DOMContentLoaded", function() {
        getMyLocation(false);
        
        let savedMode = localStorage.getItem('user_bin_view_mode') || 'table';
        setViewMode(savedMode);
    });

    var saveRadiusTimeout = null;
    function saveRadiusDatabaseSilently(val) {
        if (saveRadiusTimeout) clearTimeout(saveRadiusTimeout);
        saveRadiusTimeout = setTimeout(function() {
            fetch('{{ route("user.settings.update") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ notification_radius: val })
            })
            .catch(err => console.error("Error silently saving radius:", err));
        }, 1000); // 1s debounce
    }

    // Settings tab slider synchronizer
    function updateRadiusSlider(val) {
        document.getElementById('radius-val').textContent = val + 'm';
        userRadius = parseInt(val);
        
        if (userLat !== null && userLng !== null) {
            findNearby(userLat, userLng);
        }
        saveRadiusDatabaseSilently(val);
    }

    // Ajax Save Settings
    function updateSettingsRadius(event) {
        event.preventDefault();
        const radius = document.getElementById('radius-settings-slider').value;
        
        fetch('{{ route("user.settings.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ notification_radius: radius })
        })
        .then(r => r.json())
        .then(d => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#1e293b'
            });
            Toast.fire({
                icon: 'success',
                title: 'Konfigurasi jangkauan radius berhasil disimpan!'
            });
        })
        .catch(err => {
            console.error("Gagal menyimpan radius:", err);
        });
    }

    function getDistance(lat1, lng1, lat2, lng2) {
        var R = 6371000;
        var dLat = (lat2-lat1)*Math.PI/180;
        var dLng = (lng2-lng1)*Math.PI/180;
        var a = Math.sin(dLat/2)*Math.sin(dLat/2)+Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)*Math.sin(dLng/2);
        return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
    }

    function formatDist(m) {
        return m >= 1000 ? (m/1000).toFixed(2)+' km' : Math.round(m)+' m';
    }

    // Real-Time Glassmorphic Notifications Updater
    function updateRealtimeNotifications(critical) {
        const container = document.getElementById('realtime-notifications');
        if (!container) return;

        const currentCards = container.querySelectorAll('.realtime-notif-card');
        const currentIds = Array.from(currentCards).map(card => parseInt(card.dataset.binId));
        const newIds = critical.map(b => b.id);

        // Hapus card yang tidak lagi kritis atau keluar radius
        currentCards.forEach(card => {
            const id = parseInt(card.dataset.binId);
            if (!newIds.includes(id)) {
                card.style.animation = 'slideOut 0.4s ease forwards';
                setTimeout(() => card.remove(), 400);
                warnedBinIds.delete(id);
            }
        });

        // Tambah atau update card kritis
        critical.forEach(bin => {
            let card = container.querySelector(`.realtime-notif-card[data-bin-id="${bin.id}"]`);
            const distanceText = formatDist(bin.dist);

            if (card) {
                card.querySelector('.notif-percentage').textContent = `${bin.percentage}%`;
                card.querySelector('.notif-distance').textContent = distanceText;
                const meterFilled = card.querySelector('.notif-meter-filled');
                if (meterFilled) meterFilled.style.width = `${bin.percentage}%`;
            } else {
                if (!warnedBinIds.has(bin.id)) {
                    playNotificationChime();
                    warnedBinIds.add(bin.id);
                }

                card = document.createElement('div');
                card.className = 'realtime-notif-card';
                card.dataset.binId = bin.id;
                card.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-rose-500/15 flex items-center justify-center shrink-0">
                            <i class="fas fa-exclamation-triangle text-rose-600 animate-pulse text-sm"></i>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex justify-between items-center mb-1">
                                <b class="text-xs font-bold text-rose-600 dark:text-rose-450 uppercase tracking-wider">TONG PENUH!</b>
                                <span class="notif-percentage px-2 py-0.5 text-[10px] font-black bg-rose-500 text-white rounded-md">${bin.percentage}%</span>
                            </div>
                            <div class="font-bold text-slate-800 dark:text-slate-100 text-xs truncate mb-1">${bin.name}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 mb-2">
                                <i class="fas fa-location-arrow"></i> Jarak: <span class="notif-distance font-semibold text-slate-700 dark:text-slate-200">${distanceText}</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 h-1 rounded-full overflow-hidden mb-3">
                                <div class="notif-meter-filled h-full bg-rose-500 rounded-full" style="width:${bin.percentage}%"></div>
                            </div>
                            <button class="w-full inline-flex items-center justify-center gap-1 py-1.5 text-[10px] font-extrabold bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-all" onclick="markEmpty(${bin.id})">
                                <i class="fas fa-check"></i> Tandai Sudah Diambil
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            }
        });
    }

    function findNearby(lat, lng) {
        var nearby = bins
            .filter(b => b.latitude && b.longitude)
            .map(b => ({...b, dist: getDistance(lat, lng, b.latitude, b.longitude)}))
            .filter(b => b.dist <= userRadius)
            .sort((a,b) => a.dist - b.dist);

        var critical = nearby.filter(b => b.percentage > 85);
        updateRealtimeNotifications(critical);
    }

    // Dynamic Tailwind class definitions for status mapping
    var twColors = {
        'secondary': {
            bg: 'bg-slate-500/10',
            text: 'text-slate-600 dark:text-slate-400',
            border: 'border-slate-500/20',
            hex: '#64748b'
        },
        'danger': {
            bg: 'bg-rose-500/10',
            text: 'text-rose-600 dark:text-rose-400',
            border: 'border-rose-500/20',
            hex: '#f43f5e'
        },
        'success': {
            bg: 'bg-emerald-500/10',
            text: 'text-emerald-600 dark:text-emerald-400',
            border: 'border-emerald-500/20',
            hex: '#10b981'
        },
        'warning': {
            bg: 'bg-amber-500/10',
            text: 'text-amber-600 dark:text-amber-400',
            border: 'border-amber-500/20',
            hex: '#f59e0b'
        }
    };

    // Dynamic list table rendering
    function updateDaftarTable(binsList) {
        const tbody = document.querySelector('#bins-table tbody');
        if (!tbody) return;

        if (binsList.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-slate-450 dark:text-slate-500">Belum ada data</td></tr>';
            return;
        }

        tbody.innerHTML = binsList.map(bin => {
            var isConn = bin.is_connected;
            var perc = isConn ? bin.percentage : 0;
            var key = !isConn ? 'secondary' : (perc > 85 ? 'danger' : (perc < 25 ? 'success' : 'warning'));
            var style = twColors[key];
            var label = !isConn ? 'Offline' : (perc > 85 ? 'Full' : (perc < 25 ? 'Empty' : 'Normal'));
            
            return `<tr class="bin-table-row hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-all" data-searchable="${bin.name.toLowerCase()} ${bin.location ? bin.location.toLowerCase() : ''}">
                <td class="px-6 py-4 text-sm font-bold text-slate-800 dark:text-slate-100">${bin.name}</td>
                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">${bin.location ?? '-'}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-24 bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div id="bar-table-${bin.id}" class="h-full rounded-full transition-all duration-700" style="width:${perc}%; background-color: ${style.hex}"></div>
                        </div>
                        <small id="perc-table-${bin.id}" class="text-xs font-bold text-slate-700 dark:text-slate-300">${isConn ? bin.percentage + '%' : '-'}</small>
                    </div>
                </td>
                <td class="px-6 py-4"><span id="badge-table-${bin.id}" class="px-2.5 py-1 text-[10px] font-extrabold rounded-full whitespace-nowrap ${style.bg} ${style.text} ${style.border}">${label}</span></td>
                <td class="px-6 py-4 text-right">
                    <button class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer" onclick="markEmpty(${bin.id})">
                        <i class="fas fa-check text-[10px]"></i> Tandai Diambil
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    // Dynamic list grid rendering
    function updateDaftarGrid(binsList) {
        const gridContainer = document.getElementById('bins-grid');
        if (!gridContainer) return;

        if (binsList.length === 0) {
            gridContainer.innerHTML = '<div class="col-span-full text-center text-slate-400 dark:text-slate-500 py-12">Belum ada data tong sampah</div>';
            return;
        }

        gridContainer.innerHTML = binsList.map(bin => {
            var isConn = bin.is_connected;
            var perc = isConn ? bin.percentage : 0;
            var key = !isConn ? 'secondary' : (perc > 85 ? 'danger' : (perc < 25 ? 'success' : 'warning'));
            var style = twColors[key];
            var label = !isConn ? 'Offline' : (perc > 85 ? 'Full' : (perc < 25 ? 'Empty' : 'Normal'));
            
            var circumference = 251.2;
            var dashoffset = circumference - (circumference * perc) / 100;
            
            return `
            <div class="bin-grid-item" data-searchable="${bin.name.toLowerCase()} ${bin.location ? bin.location.toLowerCase() : ''}">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-4 flex flex-col h-full shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex justify-between items-start mb-3 gap-2">
                        <div class="min-w-0">
                            <div class="font-bold text-slate-800 dark:text-slate-100 text-sm truncate" title="${bin.name}">${bin.name}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5 truncate">
                                <i class="fas fa-map-marker-alt"></i> ${bin.location ?? '-'}
                            </div>
                        </div>
                        <span id="badge-grid-${bin.id}" class="px-2.5 py-1 text-[10px] font-bold rounded-full whitespace-nowrap ${style.bg} ${style.text} ${style.border}">${label}</span>
                    </div>

                    <!-- Circular Progress Gauge (SVG) -->
                    <div class="flex justify-center items-center my-3">
                        <div class="relative">
                            <svg width="100" height="100" class="transform -rotate-90">
                                <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="6" fill="transparent" class="text-slate-105 dark:text-slate-800" />
                                <circle id="circle-grid-${bin.id}" cx="50" cy="50" r="40" stroke="${style.hex}" stroke-width="6" fill="transparent"
                                        stroke-dasharray="${circumference}" stroke-dashoffset="${dashoffset}"
                                        stroke-linecap="round" class="transition-all duration-700" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                <div id="perc-grid-${bin.id}" class="text-xl font-extrabold" style="color: ${style.hex}">${isConn ? bin.percentage + '%' : '-'}</div>
                                <div class="text-[8px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">${isConn ? 'Penuh' : 'Offline'}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-150 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden mb-2">
                        <div id="bar-grid-${bin.id}" class="h-full rounded-full transition-all duration-700" style="width:${perc}%;background-color:${style.hex}"></div>
                    </div>

                    <!-- Info -->
                    <div class="flex justify-between items-center text-[10px] text-slate-500 dark:text-slate-400 font-semibold mb-2">
                        <span id="liter-grid-${bin.id}">
                            ${isConn ? Math.round(bin.percentage * bin.max_depth_cm / 100) + ' cm terisi' : '- cm terisi'}
                        </span>
                        <span>
                            Cap: ${bin.max_depth_cm} cm
                        </span>
                    </div>

                    <!-- Tinggi Sampah & Sisa Ruang -->
                    <div class="grid grid-cols-2 gap-2 text-[10px] border-t border-slate-100 dark:border-slate-800/60 py-2.5 my-1">
                        <div class="text-slate-500 dark:text-slate-400 font-medium">Tinggi Sampah: <b id="tinggi-grid-${bin.id}" class="text-blue-500 dark:text-emerald-455 block font-semibold text-xs mt-0.5">${isConn && bin.tinggi_sampah !== null && bin.tinggi_sampah !== undefined ? bin.tinggi_sampah + ' cm' : '-'}</b></div>
                        <div class="text-slate-500 dark:text-slate-400 font-medium">Sisa Ruang: <b id="sisa-grid-${bin.id}" class="text-emerald-600 dark:text-emerald-455 block font-semibold text-xs mt-0.5">${isConn && bin.sisa_ruang !== null && bin.sisa_ruang !== undefined ? bin.sisa_ruang + ' cm' : '-'}</b></div>
                    </div>

                    <div class="flex justify-between items-center text-[9px] text-slate-400 dark:text-slate-500 border-t border-slate-100 dark:border-slate-800/60 pt-2.5">
                        <span id="dist-grid-${bin.id}"><i class="fas fa-ruler-vertical"></i> Jarak: ${isConn ? bin.distance_cm + ' cm' : '-'}</span>
                        <span id="coords-grid-${bin.id}" class="font-mono">
                            <i class="fas fa-satellite"></i> 
                            ${isConn && bin.latitude ? Number(bin.latitude).toFixed(6) + ',' + Number(bin.longitude).toFixed(6) : 'Menunggu data...'}
                        </span>
                    </div>

                    <!-- Action buttons -->
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/60">
                        <button class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer" onclick="markEmpty(${bin.id})">
                            <i class="fas fa-check text-[10px]"></i> Tandai Diambil
                        </button>
                    </div>
                </div>
            </div>
            `;
        }).join('');
    }

    // Toggle Grid / Table View Mode
    function setViewMode(mode) {
        localStorage.setItem('user_bin_view_mode', mode);
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

    // SweetAlert2 Confirmation for emptying trash bin
    function markEmpty(binId) {
        Swal.fire({
            title: 'Tandai Sudah Diambil?',
            text: 'Apakah Anda yakin ingin menandai tong sampah ini sudah dikosongkan/diambil petugas?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Tandai!',
            cancelButtonText: 'Batal',
            background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#1e293b',
            borderRadius: '16px'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/trash-bins/${binId}/mark-empty`, { // Laravel routes is common under admin role actions but accessible
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => {
                    if (!r.ok) throw new Error('Response not OK');
                    return r.json();
                })
                .then(d => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#1e293b'
                    });
                    Toast.fire({
                        icon: 'success',
                        title: d.message || 'Status tong sampah berhasil diubah.'
                    });
                    pollDashboardData();
                })
                .catch(() => {
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat mengubah status tong sampah.',
                        icon: 'error',
                        confirmButtonColor: '#dc2626',
                        background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#0f172a' : '#ffffff',
                        color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8fafc' : '#1e293b',
                        borderRadius: '16px'
                    });
                });
            }
        });
    }

    function filterBins(val) {
        let query = val.toLowerCase();
        document.querySelectorAll('.bin-table-row').forEach(row => {
            row.style.display = row.dataset.searchable.includes(query) ? '' : 'none';
        });
        document.querySelectorAll('.bin-grid-item').forEach(item => {
            item.style.display = item.dataset.searchable.includes(query) ? '' : 'none';
        });
    }

    // Real-time SPA Polling (Every 5 seconds)
    function pollDashboardData() {
        fetch('{{ route("user.dashboard") }}?api=1&_t=' + new Date().getTime(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                // Update stats counter values smoothly
                const totalBinsVal = document.querySelector('.grid > div:nth-child(1) .text-3xl');
                if (totalBinsVal) totalBinsVal.textContent = res.totalBins;

                const critBinsVal = document.querySelector('.grid > div:nth-child(2) .text-3xl');
                if (critBinsVal) critBinsVal.textContent = res.criticalBins;

                // Update List Tab table rows and grid cards
                updateDaftarTable(res.bins);
                updateDaftarGrid(res.bins);

                // Re-apply search filters if any
                const searchInput = document.getElementById('search-bins');
                if (searchInput && searchInput.value) {
                    filterBins(searchInput.value);
                }

                // Update global bins array for distance recalculations
                bins = res.bins;

                // Recalculate nearby list and update notifications in real-time
                if (userLat !== null && userLng !== null) {
                    findNearby(userLat, userLng);
                }
            }
        })
        .catch(err => console.error("Error polling real-time trash bin data:", err));
    }

    // Launch background interval polling
    setInterval(pollDashboardData, 5000);
</script>
@endpush