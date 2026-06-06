@extends('layouts.main')

@section('content')

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <div class="label">Total Bins</div>
                <div id="stat-total-bins" class="value text-primary">{{ $totalBins }}</div>
            </div>
            <div class="stat-icon" style="background:#eff6ff">
                <i class="fas fa-trash" style="color:#2563eb"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <div class="label">Critical Bins</div>
                <div id="stat-critical-bins" class="value text-danger">{{ $criticalBins }}</div>
            </div>
            <div class="stat-icon" style="background:#fef2f2">
                <i class="fas fa-exclamation-triangle" style="color:#dc2626"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <div class="label">Average Fill</div>
                <div id="stat-avg-fill" class="value text-success">{{ $avgFill }}%</div>
            </div>
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="fas fa-chart-line" style="color:#16a34a"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <div class="label">Active Tracking</div>
                <div id="stat-active-bins" class="value" style="color:#7c3aed">{{ $activeBins }}</div>
            </div>
            <div class="stat-icon" style="background:#f5f3ff">
                <i class="fas fa-map-marker-alt" style="color:#7c3aed"></i>
            </div>
        </div>
    </div>
</div>

<!-- TAB NAV -->
<div class="card-custom">
    <div class="tab-nav px-3 pt-2">
        <button class="tab-btn active" data-tab="peta" onclick="switchTab('peta')">
            <i class="fas fa-map-marked-alt"></i> Peta Monitoring
        </button>
        <button class="tab-btn" data-tab="daftar" onclick="switchTab('daftar')">
            <i class="fas fa-trash-alt"></i> Monitor & Kelola
        </button>
        <button class="tab-btn" data-tab="analitik" onclick="switchTab('analitik')">
            <i class="fas fa-chart-line"></i> Analitik Tren
        </button>
    </div>

    <!-- TAB PETA -->
    <div id="tab-peta" class="tab-content-area active">
        <div class="row g-0">
            <!-- Map Container -->
            <div class="col-md-9 border-end" style="position:relative;height:550px">
                <div id="map" style="height:100%;width:100%"></div>
                
                <!-- Real-time Coordinates Overlay -->
                <div style="position:absolute;bottom:10px;left:10px;z-index:999;
                    background:var(--card-bg);color:var(--text);
                    border:1px solid var(--border);padding:5px 12px;border-radius:8px;
                    font-family:monospace;font-size:12px;pointer-events:none;
                    box-shadow:0 2px 8px rgba(0,0,0,0.1)">
                    X: <b id="cursor-lng">-</b> &nbsp; Y: <b id="cursor-lat">-</b>
                </div>
                
                <!-- Status Legend Overlay -->
                <div style="position:absolute;bottom:10px;right:10px;z-index:999;
                    background:var(--card-bg);border:1px solid var(--border);
                    border-radius:10px;padding:12px;font-size:11px;box-shadow: 0 4px 12px rgba(0,0,0,0.1)">
                    <div style="font-weight:700;margin-bottom:6px">Status Legend</div>
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px">
                        <div style="width:10px;height:10px;background:#16a34a;border-radius:50%"></div> Normal (&lt;70%)
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px">
                        <div style="width:10px;height:10px;background:#f59e0b;border-radius:50%"></div> Warning (70-89%)
                    </div>
                    <div style="display:flex;align-items:center;gap:6px">
                        <div style="width:10px;height:10px;background:#dc2626;border-radius:50%"></div> Kritis (&gt;=90%)
                    </div>
                </div>
            </div>
            
            <!-- Locations Sidebar Panel -->
            <div class="col-md-3 d-flex flex-column" style="height:550px; background:var(--card-bg)">
                <!-- Sidebar Header -->
                <div class="p-3 border-bottom">
                    <h6 class="fw-bold mb-2"><i class="fas fa-map-marker-alt text-danger"></i> Daftar Lokasi</h6>
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-sm ps-4" id="search-locations"
                               placeholder="Cari lokasi bin..." onkeyup="filterMapLocations(this.value)">
                        <i class="fas fa-search position-absolute top-50 translate-middle-y ms-2 text-muted" style="font-size:11px"></i>
                    </div>
                </div>
                
                <!-- Sidebar Scrollable List -->
                <div class="p-2 flex-grow-1" id="map-loc-list" style="overflow-y:auto; max-height:460px">
                    @foreach($bins as $bin)
                    @php
                        $color = $bin->percentage >= 90 ? 'danger' : ($bin->percentage >= 70 ? 'warning' : 'success');
                    @endphp
                    <div class="map-loc-item d-flex align-items-center gap-2 p-2 rounded mb-2"
                         id="map-loc-item-{{ $bin->id }}"
                         data-searchable="{{ strtolower($bin->name . ' ' . $bin->location) }}"
                         style="background:var(--bg);cursor:pointer;transition:all 0.2s;border: 1px solid var(--border)"
                         onclick="flyToMap({{ $bin->latitude ?? 0 }}, {{ $bin->longitude ?? 0 }}, {{ $bin->id }})"
                         onmouseover="this.style.opacity='0.85'"
                         onmouseout="this.style.opacity='1'">
                        
                        <span id="badge-maplist-{{ $bin->id }}" class="badge bg-{{ $color }} text-white" style="min-width:42px; font-weight:700">
                            {{ $bin->percentage }}%
                        </span>
                        
                        <div class="text-truncate">
                            <div class="fw-bold" style="font-size:12px; color:var(--text)">{{ $bin->name }}</div>
                            <div class="text-truncate" style="font-size:11px;color:var(--text-muted)">
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
    <div id="tab-daftar" class="tab-content-area p-3">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <!-- Search & View Mode Toggle -->
            <div class="d-flex gap-2 align-items-center flex-grow-1" style="max-width: 500px;">
                <div class="position-relative flex-grow-1">
                    <input type="text" class="form-control form-control-sm ps-4" id="search-bins"
                           placeholder="Cari tong sampah..." onkeyup="filterBins(this.value)">
                    <i class="fas fa-search position-absolute top-50 translate-middle-y ms-2 text-muted" style="font-size:12px"></i>
                </div>
                
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary" id="btn-view-grid" onclick="setViewMode('grid')" title="Tampilan Card Grid">
                        <i class="fas fa-th-large"></i> Grid
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="btn-view-table" onclick="setViewMode('table')" title="Tampilan Tabel Kelola">
                        <i class="fas fa-table"></i> Tabel
                    </button>
                </div>
            </div>
            
            <a href="{{ route('admin.trash-bins.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Bin
            </a>
        </div>

        <!-- 1. GRID VIEW (VOLUME CARDS) -->
        <div class="row g-3" id="bins-grid" style="display: none;">
            @forelse($bins as $bin)
            @php
                $c     = $bin->percentage >= 90 ? '#dc2626' : ($bin->percentage >= 70 ? '#f59e0b' : '#16a34a');
                $bg    = $bin->percentage >= 90 ? '#fef2f2' : ($bin->percentage >= 70 ? '#fffbeb' : '#f0fdf4');
                $label = $bin->percentage >= 90 ? 'Kritis' : ($bin->percentage >= 70 ? 'Warning' : 'Normal');
                $badge = $bin->percentage >= 90 ? 'danger' : ($bin->percentage >= 70 ? 'warning' : 'success');
                $circumference = 2 * 3.14159265 * 40; // r=40
                $dashoffset = $circumference - ($circumference * $bin->percentage) / 100;
            @endphp
            <div class="col-md-4 bin-grid-item" data-searchable="{{ strtolower($bin->name . ' ' . $bin->location) }}">
                <div class="card-custom p-3 h-100 d-flex flex-column" style="box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.3s ease;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="fw-bold fs-5 text-truncate" style="max-width: 170px;">{{ $bin->name }}</div>
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

                    <!-- Urgent Alert Container -->
                    <div id="status-container-{{ $bin->id }}" class="mt-2" style="display: {{ $bin->percentage >= 90 ? 'block' : 'none' }}">
                        <div class="p-2 rounded text-center" style="background:#fef2f2;color:#dc2626;font-size:11px;font-weight:600">
                            ⚠️ Segera kosongkan tong sampah ini!
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="d-flex gap-2 mt-3 pt-2 border-top">
                        <a href="{{ route('admin.trash-bins.edit', $bin->id) }}" class="btn btn-sm btn-outline-warning flex-grow-1" title="Edit Bin">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.trash-bins.destroy', $bin->id) }}" method="POST" class="d-inline flex-grow-1"
                              onsubmit="return confirmAction(event, 'Hapus bin ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger w-100" title="Hapus Bin">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="fas fa-trash fa-3x mb-3 d-block"></i>
                Belum ada data tong sampah
            </div>
            @endforelse
        </div>

        <!-- 2. TABLE VIEW (INTERACTIVE CRUD TABLE) -->
        <div id="bins-table-wrapper" style="display: none;">
            <table class="table table-hover align-middle" id="bins-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Lokasi</th>
                        <th>Volume</th>
                        <th>Jarak Sensor</th>
                        <th>Status</th>
                        <th>Koordinat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bins as $bin)
                    @php
                        $color = $bin->percentage >= 90 ? 'danger' : ($bin->percentage >= 70 ? 'warning' : 'success');
                        $label = $bin->percentage >= 90 ? 'Kritis' : ($bin->percentage >= 70 ? 'Warning' : 'Normal');
                    @endphp
                    <tr class="bin-table-row">
                        <td>{{ $bin->id }}</td>
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
                        <td id="dist-table-{{ $bin->id }}">{{ $bin->distance_cm ?? '-' }} cm</td>
                        <td><span id="badge-table-{{ $bin->id }}" class="badge bg-{{ $color }}">{{ $label }}</span></td>
                        <td>
                            <small id="coords-table-{{ $bin->id }}" style="font-family:monospace;font-size:11px">
                                {{ $bin->latitude ? number_format($bin->latitude,6).', '.number_format($bin->longitude,6) : '-' }}
                            </small>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.trash-bins.edit', $bin->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Bin">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.trash-bins.destroy', $bin->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirmAction(event, 'Hapus bin ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus Bin">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data tong sampah</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB ANALITIK -->
    <div id="tab-analitik" class="tab-content-area p-3">
        <div class="row g-3">
            <div class="col-md-8">
                <div class="card-custom p-3">
                    <h6 class="fw-bold mb-3">Tren Volume Mingguan</h6>
                    <canvas id="weeklyChart" height="100"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-custom p-3">
                    <h6 class="fw-bold mb-3">Tren Volume Bulanan</h6>
                    <canvas id="monthlyChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var map = L.map('map', { attributionControl: false }).setView([-7.9797, 112.6304], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    map.on('mousemove', function(e) {
        document.getElementById('cursor-lng').textContent = e.latlng.lng.toFixed(6);
        document.getElementById('cursor-lat').textContent = e.latlng.lat.toFixed(6);
    });
    map.on('mouseout', function() {
        document.getElementById('cursor-lng').textContent = '-';
        document.getElementById('cursor-lat').textContent = '-';
    });

    var bins = @json($bins->values());
    var markers = {};
    var userMarker = null;
    var directionLine = null;

    bins.forEach(function(b1, i) {
        bins.forEach(function(b2, j) {
            if (i < j && b1.latitude && b2.latitude) {
                L.polyline([[b1.latitude, b1.longitude],[b2.latitude, b2.longitude]], {
                    color: '#94a3b8', weight: 1, dashArray: '5,8', opacity: 0.4
                }).addTo(map);
            }
        });
    });

    bins.forEach(function(bin) {
        if (!bin.latitude || !bin.longitude) return;
        var color = bin.percentage >= 90 ? '#dc2626' : (bin.percentage >= 70 ? '#f59e0b' : '#16a34a');
        var label = bin.percentage >= 90 ? 'Kritis' : (bin.percentage >= 70 ? 'Warning' : 'Normal');
        
        var icon = L.divIcon({
            className: '',
            html: `<div style="position:relative">
                <div style="width:44px;height:44px;border-radius:50%;border:3px solid ${color};background:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:${color};box-shadow:0 2px 8px rgba(0,0,0,0.2)">${bin.percentage}%</div>
                ${bin.percentage >= 90 ? `<div style="position:absolute;top:-2px;right:-2px;width:12px;height:12px;background:#dc2626;border-radius:50%;border:2px solid white"></div>` : ''}
            </div>`,
            iconSize: [44, 44], iconAnchor: [22, 22],
        });
        
        markers[bin.id] = L.marker([bin.latitude, bin.longitude], {icon})
            .addTo(map)
            .bindPopup(`
                <div style="min-width:190px;font-family:sans-serif">
                    <div style="font-weight:700;font-size:14px">${bin.name}</div>
                    <div style="color:#6c757d;font-size:12px;margin-bottom:8px">${bin.location ?? '-'}</div>

                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span style="font-size:12px">Volume</span>
                        <b style="color:${color};font-size:13px">${bin.percentage}%</b>
                    </div>
                    <div style="height:6px;background:#e5e7eb;border-radius:3px;margin-bottom:8px">
                        <div style="height:100%;width:${bin.percentage}%;background:${color};border-radius:3px"></div>
                    </div>

                    <div style="display:flex;justify-content:space-between;font-size:11px;color:#6c757d;margin-bottom:10px">
                        <span>Jarak: <b>${bin.distance_cm ?? '-'} cm</b></span>
                        <span style="color:${color};font-weight:600">${label}</span>
                    </div>

                    <div id="dir-${bin.id}" style="display:none;padding:8px;background:#f0f9ff;border-radius:6px;font-size:12px;margin-bottom:8px;color:#1a1a2e">
                        <div style="display:flex;justify-content:space-between">
                            <span>Arah:</span>
                            <b id="compass-${bin.id}">-</b>
                        </div>
                        <div style="display:flex;justify-content:space-between">
                            <span>Jarak:</span>
                            <b id="dist-${bin.id}">-</b>
                        </div>
                    </div>

                    <button onclick="getDirectionTo(${bin.latitude}, ${bin.longitude}, ${bin.id})"
                        style="width:100%;padding:7px;background:#2563eb;color:white;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;margin-bottom:4px">
                        🧭 Cari Arah ke Sini
                    </button>
                    <button onclick="openGoogleMaps(${bin.latitude}, ${bin.longitude})"
                        style="width:100%;padding:7px;background:#16a34a;color:white;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600">
                        🗺️ Google Maps
                    </button>
                </div>
            `);
    });

    var validBins = bins.filter(b => b.latitude && b.longitude);
    if (validBins.length > 0) {
        var group = L.featureGroup(validBins.map(b => L.marker([b.latitude, b.longitude])));
        map.fitBounds(group.getBounds().pad(0.3));
    }

    var weeklyCtx = document.getElementById('weeklyChart')?.getContext('2d');
    if (weeklyCtx) {
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: @json($weeklyLabels),
                datasets: [{
                    label: 'Rata-rata Volume (%)',
                    data: @json($weeklyData),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.1)',
                    fill: true, tension: 0.4, pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
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
                    backgroundColor: 'rgba(37,99,235,0.7)',
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
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

    function getDirectionTo(binLat, binLng, binId) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            var myLat = pos.coords.latitude;
            var myLng = pos.coords.longitude;

            if (userMarker) map.removeLayer(userMarker);
            userMarker = L.marker([myLat, myLng], {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="width:16px;height:16px;background:#2563eb;border-radius:50%;border:3px solid white;box-shadow:0 0 0 6px rgba(37,99,235,0.25)"></div>`,
                    iconSize: [16,16], iconAnchor: [8,8]
                })
            }).addTo(map).bindPopup('📍 Lokasi Saya').openPopup();

            if (directionLine) map.removeLayer(directionLine);
            directionLine = L.polyline([[myLat, myLng],[binLat, binLng]], {
                color: '#2563eb', weight: 2, dashArray: '6,8', opacity: 0.8
            }).addTo(map);

            var bearing = getBearing(myLat, myLng, binLat, binLng);
            var dist    = getDistance(myLat, myLng, binLat, binLng);

            var dirEl = document.getElementById('dir-'+binId);
            if (dirEl) {
                dirEl.style.display = 'block';
                document.getElementById('compass-'+binId).textContent = bearingToCompass(bearing);
                document.getElementById('dist-'+binId).textContent = formatDist(dist);
            }

            map.fitBounds([[myLat, myLng],[binLat, binLng]], {padding: [50, 50]});

        }, function() {
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
        });
    }

    function openGoogleMaps(lat, lng) {
        window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`, '_blank');
    }

    function flyToMap(lat, lng, binId) {
        if (lat && lng) {
            map.flyTo([lat, lng], 17);
            if(markers[binId]) {
                markers[binId].openPopup();
            }
        }
    }

    function filterMapLocations(val) {
        let query = val.toLowerCase();
        document.querySelectorAll('.map-loc-item').forEach(item => {
            item.style.display = item.dataset.searchable.includes(query) ? '' : 'none';
        });
    }

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
                // 0. UPDATE STAT CARDS
                let totalBinsCount = result.data.length;
                let criticalBinsCount = result.data.filter(b => b.percentage >= 90).length;
                let activeBinsCount = result.data.filter(b => b.is_active).length;
                let avgFillVal = Math.round(result.data.reduce((acc, b) => acc + Number(b.percentage), 0) / (totalBinsCount || 1));

                let statTotal = document.getElementById('stat-total-bins');
                let statCritical = document.getElementById('stat-critical-bins');
                let statActive = document.getElementById('stat-active-bins');
                let statAvg = document.getElementById('stat-avg-fill');

                if(statTotal) statTotal.innerText = totalBinsCount;
                if(statCritical) statCritical.innerText = criticalBinsCount;
                if(statActive) statActive.innerText = activeBinsCount;
                if(statAvg) statAvg.innerText = avgFillVal + '%';

                result.data.forEach(bin => {
                    let color = bin.percentage >= 90 ? 'danger' : (bin.percentage >= 70 ? 'warning' : 'success');
                    let colorHex = bin.percentage >= 90 ? '#dc2626' : (bin.percentage >= 70 ? '#f59e0b' : '#16a34a');
                    let label = bin.percentage >= 90 ? 'Kritis' : (bin.percentage >= 70 ? 'Warning' : 'Normal');
                    
                    // 1. UPDATE TABLE VIEW
                    let textPercTable = document.getElementById('perc-table-' + bin.id);
                    let textDistTable = document.getElementById('dist-table-' + bin.id);
                    let barTable = document.getElementById('bar-table-' + bin.id);
                    let badgeTable = document.getElementById('badge-table-' + bin.id);
                    let coordsTable = document.getElementById('coords-table-' + bin.id);

                    if(textPercTable) textPercTable.innerText = bin.percentage + '%';
                    if(textDistTable) textDistTable.innerText = bin.distance_cm + ' cm';
                    if(barTable) {
                        barTable.style.width = bin.percentage + '%';
                        barTable.className = 'progress-bar bg-' + color; 
                    }
                    if(badgeTable) {
                        badgeTable.innerText = label;
                        badgeTable.className = 'badge bg-' + color; 
                    }
                    if(coordsTable) {
                        coordsTable.innerText = bin.latitude ? Number(bin.latitude).toFixed(6) + ', ' + Number(bin.longitude).toFixed(6) : '-';
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
                        textPercGrid.innerText = bin.percentage + '%';
                        textPercGrid.style.color = colorHex;
                    }
                    if(textDistGrid) textDistGrid.innerHTML = '<i class="fas fa-ruler-vertical"></i> Jarak: ' + bin.distance_cm + ' cm';
                    if(barGrid) {
                        barGrid.style.width = bin.percentage + '%';
                        barGrid.style.backgroundColor = colorHex;
                    }
                    if(badgeGrid) {
                        badgeGrid.innerText = label;
                        badgeGrid.className = 'badge bg-' + color;
                    }
                    if(circleGrid) {
                        let circum = 2 * 3.14159265 * 40;
                        circleGrid.style.strokeDashoffset = circum - (circum * bin.percentage) / 100;
                        circleGrid.setAttribute('stroke', colorHex);
                    }
                    if(statusContainer) {
                        statusContainer.style.display = bin.percentage >= 90 ? 'block' : 'none';
                    }
                    if(emptyBtn) {
                        emptyBtn.style.display = bin.percentage >= 70 ? 'block' : 'none';
                    }
                    if(literGrid) {
                        literGrid.innerText = Math.round(bin.percentage * bin.max_depth_cm / 100) + 'L terisi';
                    }
                    if(coordsGrid) {
                        coordsGrid.innerHTML = '<i class="fas fa-satellite"></i> ' + (bin.latitude ? Number(bin.latitude).toFixed(6) + ',' + Number(bin.longitude).toFixed(6) : 'Belum kalibrasi');
                    }

                    // 3. UPDATE MAPMARKERS POPUP & ICON & POSITION
                    if (markers[bin.id]) {
                        // Update marker position dynamically if coordinates changed
                        if (bin.latitude && bin.longitude) {
                            markers[bin.id].setLatLng([bin.latitude, bin.longitude]);
                        }

                        let icon = L.divIcon({
                            className: '',
                            html: `<div style="position:relative">
                                <div style="width:44px;height:44px;border-radius:50%;border:3px solid ${colorHex};background:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:${colorHex};box-shadow:0 2px 8px rgba(0,0,0,0.2)">${bin.percentage}%</div>
                                ${bin.percentage >= 90 ? `<div style="position:absolute;top:-2px;right:-2px;width:12px;height:12px;background:#dc2626;border-radius:50%;border:2px solid white"></div>` : ''}
                            </div>`,
                            iconSize: [44, 44], iconAnchor: [22, 22],
                        });
                        markers[bin.id].setIcon(icon);
                        markers[bin.id].setPopupContent(`
                            <div style="min-width:190px;font-family:sans-serif">
                                <div style="font-weight:700;font-size:14px">${bin.name}</div>
                                <div style="color:#6c757d;font-size:12px;margin-bottom:8px">${bin.location ?? '-'}</div>

                                <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                                    <span style="font-size:12px">Volume</span>
                                    <b style="color:${colorHex};font-size:13px">${bin.percentage}%</b>
                                </div>
                                <div style="height:6px;background:#e5e7eb;border-radius:3px;margin-bottom:8px">
                                    <div style="height:100%;width:${bin.percentage}%;background:${colorHex};border-radius:3px"></div>
                                </div>

                                <div style="display:flex;justify-content:space-between;font-size:11px;color:#6c757d;margin-bottom:10px">
                                    <span>Jarak: <b>${bin.distance_cm ?? '-'} cm</b></span>
                                    <span style="color:${colorHex};font-weight:600">${label}</span>
                                </div>

                                <div id="dir-${bin.id}" style="display:none;padding:8px;background:#f0f9ff;border-radius:6px;font-size:12px;margin-bottom:8px;color:#1a1a2e">
                                    <div style="display:flex;justify-content:space-between">
                                        <span>Arah:</span>
                                        <b id="compass-${bin.id}">-</b>
                                    </div>
                                    <div style="display:flex;justify-content:space-between">
                                        <span>Jarak:</span>
                                        <b id="dist-${bin.id}">-</b>
                                    </div>
                                </div>

                                <button onclick="getDirectionTo(${bin.latitude}, ${bin.longitude}, ${bin.id})"
                                    style="width:100%;padding:7px;background:#2563eb;color:white;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;margin-bottom:4px">
                                    🧭 Cari Arah ke Sini
                                </button>
                                <button onclick="openGoogleMaps(${bin.latitude}, ${bin.longitude})"
                                    style="width:100%;padding:7px;background:#16a34a;color:white;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600">
                                    🗺️ Google Maps
                                </button>
                            </div>
                        `);
                    }

                    // 4. UPDATE SIDEBAR MAP LIST
                    let badgeMapList = document.getElementById('badge-maplist-' + bin.id);
                    if(badgeMapList) {
                        badgeMapList.innerText = bin.percentage + '%';
                        badgeMapList.className = 'badge bg-' + color + ' text-white';
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