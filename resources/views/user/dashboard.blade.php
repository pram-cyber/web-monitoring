@extends('layouts.main')

@section('content')

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="label">Total Bins</div>
                <div class="value text-primary">{{ $totalBins }}</div>
            </div>
            <div class="stat-icon" style="background:#eff6ff">
                <i class="fas fa-trash" style="color:#2563eb"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="label">Critical Bins</div>
                <div class="value text-danger">{{ $criticalBins }}</div>
            </div>
            <div class="stat-icon" style="background:#fef2f2">
                <i class="fas fa-exclamation-triangle" style="color:#dc2626"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="label">Laporan Saya</div>
                <div class="value text-warning">{{ $myReports }}</div>
            </div>
            <div class="stat-icon" style="background:#fffbeb">
                <i class="fas fa-flag" style="color:#f59e0b"></i>
            </div>
        </div>
    </div>
</div>

<div class="card-custom p-4 mb-4">
        <div class="row g-4">
            <!-- Left: Table/Grid Monitoring List -->
            <div class="col-lg-8">
                <div class="card-custom p-3" style="box-shadow: none; border: none; background: transparent; padding: 0 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <h6 class="fw-bold mb-0" style="color:var(--text)"><i class="fas fa-list text-primary"></i> Daftar Pemantauan</h6>
                            
                            <!-- Grid / Table View Mode Selector -->
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary" id="btn-view-grid" onclick="setViewMode('grid')" title="Tampilan Card Grid">
                                    <i class="fas fa-th-large"></i> Grid
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btn-view-table" onclick="setViewMode('table')" title="Tampilan Tabel Kelola">
                                    <i class="fas fa-table"></i> Tabel
                                </button>
                            </div>
                        </div>
                        <input type="text" class="form-control form-control-sm" id="search-bins"
                               style="max-width:220px" placeholder="Cari bin..." onkeyup="filterBins(this.value)">
                    </div>

                    <!-- 1. GRID VIEW -->
                    <div class="row g-3 mb-4" id="bins-grid" style="display: none;">
                        @forelse($bins as $bin)
                        @php
                            $c     = $bin->percentage >= 90 ? '#dc2626' : ($bin->percentage >= 70 ? '#f59e0b' : '#16a34a');
                            $badge = $bin->percentage >= 90 ? 'danger' : ($bin->percentage >= 70 ? 'warning' : 'success');
                            $label = $bin->percentage >= 90 ? 'Kritis' : ($bin->percentage >= 70 ? 'Warning' : 'Normal');
                            $circumference = 2 * 3.14159265 * 40; // r=40
                            $dashoffset = $circumference - ($circumference * $bin->percentage) / 100;
                        @endphp
                        <div class="col-md-6 bin-grid-item" data-searchable="{{ strtolower($bin->name . ' ' . $bin->location) }}">
                            <div class="card-custom p-3 h-100 d-flex flex-column" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.3s ease;">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <div class="fw-bold fs-6 text-truncate" style="max-width: 170px; color: var(--text);">{{ $bin->name }}</div>
                                        <div style="font-size:12px;color:var(--text-muted)">
                                            <i class="fas fa-map-marker-alt"></i> {{ $bin->location ?? '-' }}
                                        </div>
                                    </div>
                                    <span id="badge-grid-{{ $bin->id }}" class="badge bg-{{ $badge }}">{{ $label }}</span>
                                </div>

                                <!-- Circular Progress Gauge (SVG) -->
                                <div class="text-center mb-3">
                                    <div class="position-relative d-inline-block">
                                        <svg width="100" height="100">
                                            <circle cx="50" cy="50" r="40" stroke="var(--border)" stroke-width="8" fill="transparent" />
                                            <circle id="circle-grid-{{ $bin->id }}" cx="50" cy="50" r="40" stroke="{{ $c }}" stroke-width="8" fill="transparent"
                                                    stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $dashoffset }}"
                                                    stroke-linecap="round" style="transition: stroke-dashoffset 0.8s ease-in-out, stroke 0.3s; transform: rotate(-90deg); transform-origin: 50px 50px;" />
                                        </svg>
                                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                                            <div id="perc-grid-{{ $bin->id }}" style="font-size: 20px; font-weight: 800; color: {{ $c }}">{{ $bin->percentage }}%</div>
                                            <div style="font-size: 9px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Penuh</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="progress mb-2" style="height:8px;border-radius:4px">
                                    <div id="bar-grid-{{ $bin->id }}" class="progress-bar" style="width:{{ $bin->percentage }}%;background:{{ $c }};border-radius:4px;transition: width 0.8s, background-color 0.3s;"></div>
                                </div>

                                <!-- Info -->
                                <div class="d-flex justify-content-between mb-2">
                                    <small id="liter-grid-{{ $bin->id }}" style="color:var(--text-muted)">
                                        {{ round($bin->percentage * $bin->max_depth_cm / 100) }}L terisi
                                    </small>
                                    <small style="color:var(--text-muted)">
                                        {{ $bin->max_depth_cm }}L kapasitas
                                    </small>
                                </div>

                                <div class="d-flex justify-content-between border-top pt-2 mt-auto" style="font-size: 11px; color:var(--text-muted)">
                                    <span id="dist-grid-{{ $bin->id }}"><i class="fas fa-ruler-vertical"></i> Jarak: {{ $bin->distance_cm }} cm</span>
                                    <span id="coords-grid-{{ $bin->id }}">
                                        <i class="fas fa-satellite"></i> 
                                        {{ $bin->latitude ? number_format($bin->latitude,6).','.number_format($bin->longitude,6) : 'Belum kalibrasi' }}
                                    </span>
                                </div>

                                <!-- Action buttons -->
                                <div class="mt-3 pt-2 border-top">
                                    <button class="btn btn-sm btn-success w-100" onclick="markEmpty({{ $bin->id }})">
                                        <i class="fas fa-check"></i> Tandai Diambil
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-muted py-4">
                            Belum ada data tong sampah
                        </div>
                        @endforelse
                    </div>

                    <!-- 2. TABLE VIEW -->
                    <div id="bins-table-wrapper" style="display: block;">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="bins-table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Lokasi</th>
                                        <th>Volume</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bins as $bin)
                                    @php $color = $bin->percentage >= 90 ? 'danger' : ($bin->percentage >= 70 ? 'warning' : 'success'); @endphp
                                    <tr class="bin-table-row" data-searchable="{{ strtolower($bin->name . ' ' . $bin->location) }}">
                                        <td><b>{{ $bin->name }}</b></td>
                                        <td>{{ $bin->location ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height:8px; min-width: 100px;">
                                                    <div id="bar-table-{{ $bin->id }}" class="progress-bar bg-{{ $color }}" style="width:{{ $bin->percentage }}%"></div>
                                                </div>
                                                <small id="perc-table-{{ $bin->id }}">{{ $bin->percentage }}%</small>
                                            </div>
                                        </td>
                                        <td><span id="badge-table-{{ $bin->id }}" class="badge bg-{{ $color }}">
                                            {{ $bin->percentage >= 90 ? 'Kritis' : ($bin->percentage >= 70 ? 'Warning' : 'Normal') }}
                                        </span></td>
                                        <td>
                                            <button class="btn btn-sm btn-success" onclick="markEmpty({{ $bin->id }})">
                                                <i class="fas fa-check"></i> Tandai Diambil
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center text-muted">Belum ada data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Dynamic Radius Settings & Profile -->
            <div class="col-lg-4">
                <!-- Radius Notifikasi Card -->
                <div class="card-custom p-4 mb-4" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px;">
                    <h6 class="fw-bold mb-3" style="color:var(--text)"><i class="fas fa-bell text-warning animate__animated animate__swing animate__infinite" style="display:inline-block;"></i> Radius Notifikasi</h6>
                    <form id="radius-form" onsubmit="updateSettingsRadius(event)">
                        @csrf
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label mb-0 fw-semibold" style="font-size:13px; color:var(--text)">Jangkauan Sensor</label>
                                <span id="radius-val" class="fw-bold text-primary" style="font-size:14px">
                                    {{ auth()->user()->notification_radius }}m
                                </span>
                            </div>
                            <input type="range" name="notification_radius" class="form-range"
                                   min="50" max="1000" step="50" 
                                   value="{{ auth()->user()->notification_radius }}"
                                   oninput="updateRadiusSlider(this.value)" id="radius-settings-slider">
                            <small style="color:var(--text-muted); font-size:11px; display:block; margin-top:5px; line-height: 1.4;">
                                Peringatan akan otomatis berbunyi dan muncul jika ada tong sampah penuh (≥90%) dalam radius jangkauan ini dari lokasi Anda.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 py-2 fw-semibold" style="border-radius: 8px;">
                            <i class="fas fa-save"></i> Simpan Konfigurasi
                        </button>
                    </form>
                </div>

                <!-- Profil Saya Card -->
                <div class="card-custom p-4" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px;">
                    <h6 class="fw-bold mb-3" style="color:var(--text)"><i class="fas fa-user-circle text-primary"></i> Profil Saya</h6>
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg, #2563eb, #16a34a);color:white;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;box-shadow: 0 4px 12px rgba(37,99,235,0.2)">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div style="min-width: 0; flex-grow: 1;">
                            <div class="fw-bold text-truncate" style="font-size:14px; color:var(--text)">{{ auth()->user()->name }}</div>
                            <div class="text-truncate" style="color:var(--text-muted);font-size:12px;margin-bottom:3px">{{ auth()->user()->email }}</div>
                            <span class="badge bg-success-subtle text-success fw-bold" style="font-size:9.5px;padding:3.5px 8px;border-radius: 20px;">{{ ucfirst(auth()->user()->role) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Real-Time Notifications Container -->
<div id="realtime-notifications" style="position:fixed; top:85px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:10px; max-width:350px; pointer-events:none;"></div>

<style>
.realtime-notif-card {
    background: rgba(220, 38, 38, 0.08);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(220, 38, 38, 0.25);
    border-left: 4px solid #dc2626;
    border-radius: 12px;
    padding: 14px;
    box-shadow: 0 10px 30px rgba(220, 38, 38, 0.08), 0 1px 8px rgba(0,0,0,0.15);
    width: 320px;
    pointer-events: auto;
    animation: slideInNotif 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    transition: all 0.3s ease;
}
[data-theme="dark"] .realtime-notif-card {
    background: rgba(26, 26, 46, 0.7);
    border: 1px solid rgba(239, 68, 68, 0.35);
    border-left: 4px solid #ef4444;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3), 0 0 15px rgba(239, 68, 68, 0.1);
}
.realtime-notif-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(220, 38, 38, 0.12), 0 2px 10px rgba(0,0,0,0.2);
}
.notif-alert-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(220, 38, 38, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.notif-alert-icon i {
    font-size: 14px;
}
.btn-xs {
    font-size: 10.5px;
    padding: 3px 6px;
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
            // Elegant premium digital chime
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
                    background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a2e' : '#ffffff',
                    color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8f9fa' : '#1a1a2e',
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
                background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a2e' : '#ffffff',
                color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8f9fa' : '#1a1a2e'
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
                    <div class="d-flex align-items-start gap-3">
                        <div class="notif-alert-icon">
                            <i class="fas fa-exclamation-triangle text-danger animate__animated animate__pulse animate__infinite"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width:0;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <b class="notif-title" style="font-size:12px;font-weight:700;color:#dc2626">⚠️ TONG PENUH!</b>
                                <span class="notif-percentage badge bg-danger" style="font-size:10px;font-weight:800">${bin.percentage}%</span>
                            </div>
                            <div class="notif-bin-name fw-bold text-truncate" style="font-size:13px;color:var(--text)">${bin.name}</div>
                            <div class="notif-meta" style="font-size:11px;color:var(--text-muted);margin-bottom:8px">
                                <i class="fas fa-location-arrow"></i> Jarak: <span class="notif-distance fw-semibold" style="color:var(--text)">${distanceText}</span>
                            </div>
                            <div class="progress" style="height:4px;background:rgba(0,0,0,0.1);border-radius:2px;margin-bottom:10px">
                                <div class="notif-meter-filled progress-bar bg-danger" style="width:${bin.percentage}%"></div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-success btn-xs w-100" onclick="markEmpty(${bin.id})" style="font-size:10px;padding:4.5px;border-radius:6px">
                                    <i class="fas fa-check"></i> Tandai Sudah Diambil
                                </button>
                            </div>
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

        // Tampilkan floating notifications yang super elegan di pojok kanan untuk tong kritis
        var critical = nearby.filter(b => b.percentage >= 90);
        updateRealtimeNotifications(critical);
    }

    // Dynamic list table rendering
    function updateDaftarTable(binsList) {
        const tbody = document.querySelector('#bins-table tbody');
        if (!tbody) return;

        if (binsList.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Belum ada data</td></tr>';
            return;
        }

        tbody.innerHTML = binsList.map(bin => {
            var color = bin.percentage >= 90 ? 'danger' : (bin.percentage >= 70 ? 'warning' : 'success');
            var status = bin.percentage >= 90 ? 'Kritis' : (bin.percentage >= 70 ? 'Warning' : 'Normal');
            return `<tr class="bin-table-row" data-searchable="${bin.name.toLowerCase()} ${bin.location ? bin.location.toLowerCase() : ''}">
                <td><b>${bin.name}</b></td>
                <td>${bin.location ?? '-'}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height:8px; min-width: 100px;">
                            <div id="bar-table-${bin.id}" class="progress-bar bg-${color}" style="width:${bin.percentage}%"></div>
                        </div>
                        <small id="perc-table-${bin.id}">${bin.percentage}%</small>
                    </div>
                </td>
                <td><span id="badge-table-${bin.id}" class="badge bg-${color}">${status}</span></td>
                <td>
                    <button class="btn btn-sm btn-success" onclick="markEmpty(${bin.id})">
                        <i class="fas fa-check"></i> Tandai Diambil
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
            gridContainer.innerHTML = '<div class="col-12 text-center text-muted py-4">Belum ada data tong sampah</div>';
            return;
        }

        gridContainer.innerHTML = binsList.map(bin => {
            var color = bin.percentage >= 90 ? 'danger' : (bin.percentage >= 70 ? 'warning' : 'success');
            var colorHex = bin.percentage >= 90 ? '#dc2626' : (bin.percentage >= 70 ? '#f59e0b' : '#16a34a');
            var label = bin.percentage >= 90 ? 'Kritis' : (bin.percentage >= 70 ? 'Warning' : 'Normal');
            var circumference = 2 * 3.14159265 * 40;
            var dashoffset = circumference - (circumference * bin.percentage) / 100;
            
            return `
            <div class="col-md-6 bin-grid-item" data-searchable="${bin.name.toLowerCase()} ${bin.location ? bin.location.toLowerCase() : ''}">
                <div class="card-custom p-3 h-100 d-flex flex-column" style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.3s ease;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="fw-bold fs-6 text-truncate" style="max-width: 170px; color: var(--text);">${bin.name}</div>
                            <div style="font-size:12px;color:var(--text-muted)">
                                <i class="fas fa-map-marker-alt"></i> ${bin.location ?? '-'}
                            </div>
                        </div>
                        <span id="badge-grid-${bin.id}" class="badge bg-${color}">${label}</span>
                    </div>

                    <!-- Circular Progress Gauge (SVG) -->
                    <div class="text-center mb-3">
                        <div class="position-relative d-inline-block">
                            <svg width="100" height="100">
                                <circle cx="50" cy="50" r="40" stroke="var(--border)" stroke-width="8" fill="transparent" />
                                <circle id="circle-grid-${bin.id}" cx="50" cy="50" r="40" stroke="${colorHex}" stroke-width="8" fill="transparent"
                                        stroke-dasharray="${circumference}" stroke-dashoffset="${dashoffset}"
                                        stroke-linecap="round" style="transition: stroke-dashoffset 0.8s ease-in-out, stroke 0.3s; transform: rotate(-90deg); transform-origin: 50px 50px;" />
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <div id="perc-grid-${bin.id}" style="font-size: 20px; font-weight: 800; color: ${colorHex}">${bin.percentage}%</div>
                                <div style="font-size: 9px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Penuh</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="progress mb-2" style="height:8px;border-radius:4px">
                        <div id="bar-grid-${bin.id}" class="progress-bar" style="width:${bin.percentage}%;background:${colorHex};border-radius:4px;transition: width 0.8s, background-color 0.3s;"></div>
                    </div>

                    <!-- Info -->
                    <div class="d-flex justify-content-between mb-2">
                        <small id="liter-grid-${bin.id}" style="color:var(--text-muted)">
                            ${Math.round(bin.percentage * bin.max_depth_cm / 100)}L terisi
                        </small>
                        <small style="color:var(--text-muted)">
                            ${bin.max_depth_cm}L kapasitas
                        </small>
                    </div>

                    <div class="d-flex justify-content-between border-top pt-2 mt-auto" style="font-size: 11px; color:var(--text-muted)">
                        <span id="dist-grid-${bin.id}"><i class="fas fa-ruler-vertical"></i> Jarak: ${bin.distance_cm} cm</span>
                        <span id="coords-grid-${bin.id}">
                            <i class="fas fa-satellite"></i> 
                            ${bin.latitude ? Number(bin.latitude).toFixed(6) + ',' + Number(bin.longitude).toFixed(6) : 'Belum kalibrasi'}
                        </span>
                    </div>

                    <!-- Action buttons -->
                    <div class="mt-3 pt-2 border-top">
                        <button class="btn btn-sm btn-success w-100" onclick="markEmpty(${bin.id})">
                            <i class="fas fa-check"></i> Tandai Diambil
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
        if (mode === 'grid') {
            document.getElementById('bins-grid').style.display = 'flex';
            document.getElementById('bins-table-wrapper').style.display = 'none';
            
            document.getElementById('btn-view-grid').classList.add('btn-primary');
            document.getElementById('btn-view-grid').classList.remove('btn-outline-primary');
            document.getElementById('btn-view-table').classList.add('btn-outline-primary');
            document.getElementById('btn-view-table').classList.remove('btn-primary');
        } else {
            document.getElementById('bins-grid').style.display = 'none';
            document.getElementById('bins-table-wrapper').style.display = 'block';
            
            document.getElementById('btn-view-grid').classList.remove('btn-primary');
            document.getElementById('btn-view-grid').classList.add('btn-outline-primary');
            document.getElementById('btn-view-table').classList.remove('btn-outline-primary');
            document.getElementById('btn-view-table').classList.add('btn-primary');
        }
    }

    // SweetAlert2 Confirmation for emptying trash bin
    function markEmpty(binId) {
        Swal.fire({
            title: 'Tandai Sudah Diambil?',
            text: 'Apakah Anda yakin ingin menandai tong sampah ini sudah dikosongkan/diambil petugas?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tandai!',
            cancelButtonText: 'Batal',
            background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a2e' : '#ffffff',
            color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8f9fa' : '#1a1a2e',
            borderRadius: '16px'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/user/trash-bins/${binId}/mark-empty`, {
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
                        background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a2e' : '#ffffff',
                        color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8f9fa' : '#1a1a2e'
                    });
                    Toast.fire({
                        icon: 'success',
                        title: d.message || 'Status tong sampah berhasil diubah.'
                    });
                    // Refresh data smoothly via ajax
                    pollDashboardData();
                })
                .catch(() => {
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat mengubah status tong sampah.',
                        icon: 'error',
                        confirmButtonColor: '#dc2626',
                        background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a2e' : '#ffffff',
                        color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8f9fa' : '#1a1a2e',
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
                const totalBinsVal = document.querySelector('.stat-card .value.text-primary');
                if (totalBinsVal) totalBinsVal.textContent = res.totalBins;

                const critBinsVal = document.querySelector('.stat-card .value.text-danger');
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