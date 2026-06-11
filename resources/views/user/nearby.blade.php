@extends('layouts.main')

@section('content')

<div class="mb-6">
    <h5 class="text-xl font-bold text-slate-800 dark:text-slate-100">Tong Sampah Terdekat</h5>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Cari tong sampah terdekat &amp; tandai sudah diambil</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Map Section -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 p-5 rounded-2xl shadow-sm">
        <div class="flex justify-between items-center mb-4 gap-2">
            <h6 class="font-bold text-slate-800 dark:text-slate-100 text-sm flex items-center gap-2">
                <i class="fas fa-map text-blue-500"></i> Peta Wilayah
            </h6>
            <button class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold bg-blue-600 dark:bg-emerald-600 hover:bg-blue-700 dark:hover:bg-emerald-700 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer" onclick="getMyLocation(true)">
                <i class="fas fa-location-arrow text-[10px]"></i> Gunakan Lokasi Saya
            </button>
        </div>
        <div class="relative h-[480px] rounded-xl overflow-hidden border border-slate-150 dark:border-slate-800">
            <div id="nearby-map" class="h-full w-full"></div>
            <!-- Coordinates Overlay -->
            <div class="absolute bottom-4 left-4 z-[999] bg-white/90 dark:bg-slate-900/90 text-slate-850 dark:text-slate-100 border border-slate-200 dark:border-slate-800 px-3 py-1.5 rounded-xl font-mono text-xs pointer-events-none shadow-lg backdrop-blur-md">
                X: <b id="cursor-lng">-</b> &nbsp; Y: <b id="cursor-lat">-</b>
            </div>
        </div>
    </div>

    <!-- Sidebar Section -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Radius Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
            <h6 class="font-bold text-slate-850 dark:text-slate-100 text-sm mb-4 flex items-center gap-2">
                <i class="fas fa-sliders-h text-blue-500"></i> Radius Pencarian
            </h6>
            <div class="flex items-center gap-3">
                <input type="range" class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-lg appearance-none cursor-pointer accent-blue-600 dark:accent-emerald-500" min="50" max="1000" step="50"
                       value="{{ auth()->user()->notification_radius }}"
                       oninput="updateRadius(this.value)" id="radius-slider">
                <span id="radius-display" class="font-black text-sm text-blue-600 dark:text-emerald-400 bg-blue-50 dark:bg-emerald-950/40 px-2.5 py-0.5 border border-blue-100 dark:border-emerald-500/20 rounded-lg shrink-0">
                    {{ auth()->user()->notification_radius }}m
                </span>
            </div>
        </div>

        <!-- All Bins List Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm">
            <h6 class="font-bold text-slate-850 dark:text-slate-100 text-sm mb-4 flex items-center gap-2">
                <i class="fas fa-trash text-rose-500"></i> Semua Tong Sampah
            </h6>
            <div class="space-y-3 max-h-[380px] overflow-y-auto pr-1">
                @forelse($bins as $bin)
                @php
                    $isConn = $bin->is_connected;
                    $perc = $isConn ? $bin->percentage : 0;
                    
                    if (!$isConn) {
                        $color = '#64748b'; // slate-500
                        $badgeStyle = 'bg-slate-500/10 text-slate-650 dark:text-slate-400 border border-slate-500/20';
                    } else {
                        if ($perc > 85) {
                            $color = '#f43f5e'; // rose-500
                            $badgeStyle = 'bg-rose-500/10 text-rose-650 dark:text-rose-455 border border-rose-500/20';
                        } elseif ($perc < 25) {
                            $color = '#10b981'; // emerald-500
                            $badgeStyle = 'bg-emerald-500/10 text-emerald-650 dark:text-emerald-455 border border-emerald-500/20';
                        } else {
                            $color = '#f59e0b'; // amber-500
                            $badgeStyle = 'bg-amber-500/10 text-amber-650 dark:text-amber-455 border border-amber-500/20';
                        }
                    }
                @endphp
                <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/60 shadow-sm">
                    <div class="flex justify-between items-center mb-2 gap-2">
                        <div class="min-w-0">
                            <div class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate">{{ $bin->name }}</div>
                            <div class="text-[10px] text-slate-450 dark:text-slate-500 truncate mt-0.5"><i class="fas fa-map-marker-alt"></i> {{ $bin->location ?? '-' }}</div>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md shrink-0 {{ $badgeStyle }}">{{ $bin->percentage }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden mb-3">
                        <div class="h-full rounded-full transition-all duration-700" style="width:{{ $perc }}%;background-color:{{ $color }}"></div>
                    </div>
                    @if($isConn && $bin->percentage > 85)
                    <button onclick="markEmpty({{ $bin->id }}, this)"
                        class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer">
                        <i class="fas fa-check text-[10px]"></i> Tandai Sudah Diambil
                    </button>
                    @else
                    <div class="text-[10px] text-slate-450 dark:text-slate-500 text-center font-bold bg-slate-100/50 dark:bg-slate-950 px-2 py-1 rounded-lg border border-slate-100 dark:border-slate-850">
                        ✅ Belum perlu diambil
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-slate-400 dark:text-slate-500 text-xs text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Floating Real-time Toast Notification -->
<div id="toast" class="fixed bottom-6 right-6 z-[9999] bg-white/90 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-100 px-5 py-4 rounded-2xl shadow-2xl backdrop-blur-md max-w-sm hidden transition-all duration-300">
    <div class="flex items-center gap-3">
        <i class="text-sm shrink-0" id="toast-icon"></i> 
        <span id="toast-msg" class="text-xs font-bold"></span>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var nearbyMap = new maplibregl.Map({
        container: 'nearby-map',
        attributionControl: false,
        style: document.documentElement.classList.contains('dark')
            ? 'https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json'
            : 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
        center: [113.704272, -8.166102],
        zoom: 14,
        pitch: 30
    });
    nearbyMap.addControl(new maplibregl.NavigationControl(), 'top-right');

    nearbyMap.on('mousemove', function(e) {
        document.getElementById('cursor-lng').textContent = e.lngLat.lng.toFixed(6);
        document.getElementById('cursor-lat').textContent = e.lngLat.lat.toFixed(6);
    });

    // Listen to theme changes from layouts/main
    window.addEventListener('theme-changed', function(e) {
        var mapStyle = e.detail.theme === 'dark' 
            ? 'https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json'
            : 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json';
        nearbyMap.setStyle(mapStyle);
    });

    var bins = @json($bins->values()).filter(b => b.is_connected);
    var userRadius = {{ auth()->user()->notification_radius }};
    var userMarker = null;
    var mapMarkers = {};

    function updateOrCreateBinMarker(bin) {
        if (!bin.latitude || !bin.longitude) return;
        var color = bin.percentage > 85 ? '#f43f5e' : (bin.percentage < 25 ? '#10b981' : '#f59e0b');
        var label = bin.percentage > 85 ? 'Full' : (bin.percentage < 25 ? 'Empty' : 'Normal');

        var popupContent = `
            <div style="min-width:180px;font-family:'Outfit',sans-serif">
                <b style="font-size:14px" class="text-slate-900 dark:text-white">${bin.name}</b><br>
                <span style="color:#64748b;font-size:11px"><i class="fas fa-map-marker-alt"></i> ${bin.location ?? '-'}</span>
                <hr style="margin:8px 0" class="border-slate-200 dark:border-slate-800">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-weight:600">
                    <span style="font-size:11px">Volume</span>
                    <b style="color:${color};font-size:12px">${bin.percentage}%</b>
                </div>
                <div style="height:6px;background:#e2e8f0;border-radius:3px;margin-bottom:10px;overflow:hidden" class="dark:bg-slate-800">
                    <div style="height:100%;width:${bin.percentage}%;background:${color};border-radius:3px"></div>
                </div>
                ${bin.percentage > 85 ? `
                <button onclick="markEmpty(${bin.id}, null)"
                    style="width:100%;padding:7px;background:#10b981;color:white;border:none;border-radius:8px;
                    cursor:pointer;font-size:11px;font-weight:600">
                    ✅ Tandai Sudah Diambil
                </button>` : `<div style="text-align:center;font-size:11px;color:#64748b;font-weight:600">✅ Belum perlu diambil</div>`}
            </div>
        `;

        if (mapMarkers[bin.id]) {
            mapMarkers[bin.id].setLngLat([bin.longitude, bin.latitude]);
            mapMarkers[bin.id].getPopup().setHTML(popupContent);
            
            var el = mapMarkers[bin.id].getElement();
            el.innerHTML = `
                <div class="relative">
                    <div class="w-10 h-10 rounded-full border-3 bg-white dark:bg-slate-900 flex items-center justify-center text-[10px] font-black shadow-lg transition-transform hover:scale-105 duration-200" style="border-color: ${color}; color: ${color}">${bin.percentage}%</div>
                    ${bin.percentage > 85 ? `<div class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900 animate-ping"></div>` : ''}
                </div>
            `;
        } else {
            var el = document.createElement('div');
            el.className = 'custom-trash-marker cursor-pointer group';
            el.innerHTML = `
                <div class="relative">
                    <div class="w-10 h-10 rounded-full border-3 bg-white dark:bg-slate-900 flex items-center justify-center text-[10px] font-black shadow-lg transition-transform hover:scale-105 duration-200" style="border-color: ${color}; color: ${color}">${bin.percentage}%</div>
                    ${bin.percentage > 85 ? `<div class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900 animate-ping"></div>` : ''}
                </div>
            `;

            el.addEventListener('click', function() {
                nearbyMap.easeTo({
                    center: [bin.longitude, bin.latitude],
                    zoom: 15,
                    duration: 1000
                });
            });

            var popup = new maplibregl.Popup({ offset: 25 }).setHTML(popupContent);

            mapMarkers[bin.id] = new maplibregl.Marker({ element: el })
                .setLngLat([bin.longitude, bin.latitude])
                .setPopup(popup)
                .addTo(nearbyMap);
        }
    }

    function createCircleGeoJSON(center, radiusInMeters) {
        var coords = {
            latitude: center[1],
            longitude: center[0]
        };
        var km = radiusInMeters / 1000;
        var ret = [];
        var distanceX = km / (111.32 * Math.cos(coords.latitude * Math.PI / 180));
        var distanceY = km / 110.574;

        var theta, x, y;
        for (var i = 0; i < 64; i++) {
            theta = (i / 64) * (2 * Math.PI);
            x = distanceX * Math.cos(theta);
            y = distanceY * Math.sin(theta);
            ret.push([coords.longitude + x, coords.latitude + y]);
        }
        ret.push(ret[0]);

        return {
            type: 'Feature',
            geometry: {
                type: 'Polygon',
                coordinates: [ret]
            }
        };
    }

    function drawRadiusCircle(lng, lat) {
        var geojson = createCircleGeoJSON([lng, lat], userRadius);
        var source = nearbyMap.getSource('radius-source');
        if (source) {
            source.setData(geojson);
        } else {
            if (nearbyMap.loaded()) {
                addRadiusSourceAndLayer(geojson);
            } else {
                nearbyMap.on('load', function() {
                    addRadiusSourceAndLayer(geojson);
                });
            }
        }
    }

    function addRadiusSourceAndLayer(geojson) {
        if (nearbyMap.getSource('radius-source')) return;
        nearbyMap.addSource('radius-source', {
            type: 'geojson',
            data: geojson
        });
        nearbyMap.addLayer({
            id: 'radius-fill',
            type: 'fill',
            source: 'radius-source',
            paint: {
                'fill-color': '#3b82f6',
                'fill-opacity': 0.04
            }
        });
        nearbyMap.addLayer({
            id: 'radius-outline',
            type: 'line',
            source: 'radius-source',
            paint: {
                'line-color': '#3b82f6',
                'line-width': 1.5,
                'line-dasharray': [4, 4]
            }
        });
    }

    function updateRadius(val) {
        userRadius = parseInt(val);
        document.getElementById('radius-display').textContent = val + 'm';
        if (userMarker) {
            var lngLat = userMarker.getLngLat();
            drawRadiusCircle(lngLat.lng, lngLat.lat);
            findNearby(lngLat.lat, lngLat.lng);
        }
    }

    function getMyLocation(isManual = false) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;

            if (userMarker) userMarker.remove();

            var el = document.createElement('div');
            el.innerHTML = `<div style="width:16px;height:16px;background:#3b82f6;border-radius:50%;
                border:3px solid white;box-shadow:0 0 0 6px rgba(59,130,246,0.25)"></div>`;

            var popup = new maplibregl.Popup({ offset: 15 }).setText('Lokasi Saya');

            userMarker = new maplibregl.Marker({ element: el })
                .setLngLat([lng, lat])
                .setPopup(popup)
                .addTo(nearbyMap);

            drawRadiusCircle(lng, lat);

            if (isManual) {
                userMarker.togglePopup();
                nearbyMap.flyTo({
                    center: [lng, lat],
                    zoom: 15,
                    duration: 1200
                });
            }
            findNearby(lat, lng);
        }, function(err) {
            if (isManual) {
                Swal.fire({
                    title: 'GPS Tidak Aktif',
                    text: 'Pastikan GPS perangkat Anda aktif dan berikan izin lokasi pada browser!',
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

    nearbyMap.on('load', function() {
        bins.forEach(updateOrCreateBinMarker);
        
        var validBins = bins.filter(b => b.latitude && b.longitude);
        if (validBins.length > 0) {
            var bounds = new maplibregl.LngLatBounds();
            validBins.forEach(function(b) {
                bounds.extend([b.longitude, b.latitude]);
            });
            nearbyMap.fitBounds(bounds, { padding: 50 });
        }
        
        getMyLocation(false);
    });

    function getDistance(lat1, lng1, lat2, lng2) {
        var R = 6371000;
        var dLat = (lat2-lat1)*Math.PI/180;
        var dLng = (lng2-lng1)*Math.PI/180;
        var a = Math.sin(dLat/2)*Math.sin(dLat/2)+
                Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*
                Math.sin(dLng/2)*Math.sin(dLng/2);
        return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
    }

    function findNearby(lat, lng) {
        var nearby = bins
            .filter(b => b.latitude && b.longitude)
            .map(b => ({...b, dist: getDistance(lat, lng, b.latitude, b.longitude)}))
            .filter(b => b.dist <= userRadius)
            .sort((a,b) => a.dist - b.dist);

        var critical = nearby.filter(b => b.percentage > 85);
        if (critical.length > 0) {
            showToast(`⚠️ ${critical.length} tong sampah PENUH dalam radius ${userRadius}m!`, '#f43f5e');
        }
    }

    function markEmpty(binId, btn) {
        Swal.fire({
            title: 'Tandai Sudah Diambil?',
            text: 'Apakah Anda yakin ingin menandai tong sampah ini sudah dikosongkan oleh petugas?',
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
                fetch(`/admin/trash-bins/${binId}/mark-empty`, { // laravel endpoints mapping helper
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        showToast(d.message, '#10b981');
                        if (btn) {
                            btn.outerHTML = `<div class="text-[10px] text-emerald-600 dark:text-emerald-400 text-center font-bold bg-emerald-500/10 px-2 py-1 rounded-lg border border-emerald-500/20">
                                ✅ Sudah diambil petugas
                            </div>`;
                        }
                        setTimeout(() => location.reload(), 1500);
                    }
                })
                .catch(() => {
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memperbaharui status tong sampah.',
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

    function showToast(msg, color) {
        var toast = document.getElementById('toast');
        var toastMsg = document.getElementById('toast-msg');
        var isDanger = color === '#f43f5e';
        
        toast.className = `fixed bottom-6 right-6 z-[9999] bg-white/95 dark:bg-slate-900/95 border px-5 py-4 rounded-2xl shadow-2xl backdrop-blur-md max-w-sm transition-all duration-300 ${isDanger ? 'border-rose-500/35 border-l-4 border-l-rose-500' : 'border-emerald-500/35 border-l-4 border-l-emerald-500'}`;
        
        var icon = document.getElementById('toast-icon');
        if (icon) {
            icon.className = isDanger ? 'fas fa-exclamation-triangle text-rose-500 animate-pulse text-sm' : 'fas fa-check-circle text-emerald-500 text-sm';
        }
        
        toastMsg.textContent = msg;
        toast.classList.remove('hidden');
        setTimeout(() => { toast.classList.add('hidden'); }, 4000);
    }
</script>
@endpush