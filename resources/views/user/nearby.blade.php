@extends('layouts.main')

@section('content')

<div class="mb-4">
    <h5 class="fw-bold mb-1">Tong Sampah Terdekat</h5>
    <small style="color:var(--text-muted)">Cari tong sampah terdekat & tandai sudah diambil</small>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card-custom p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Peta</h6>
                <button class="btn btn-primary btn-sm" onclick="getMyLocation()">
                    <i class="fas fa-location-arrow"></i> Gunakan Lokasi Saya
                </button>
            </div>
            <div style="position:relative">
                <div id="nearby-map" style="height:450px;border-radius:10px"></div>
                <!-- Koordinat -->
                <div style="position:absolute;bottom:10px;left:10px;z-index:999;
                    background:var(--card-bg);color:var(--text);
                    border:1px solid var(--border);padding:5px 12px;border-radius:8px;
                    font-family:monospace;font-size:12px;pointer-events:none">
                    X: <b id="cursor-lng">-</b> &nbsp; Y: <b id="cursor-lat">-</b>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Radius -->
        <div class="card-custom p-3 mb-3">
            <h6 class="fw-bold mb-3"><i class="fas fa-sliders-h text-primary"></i> Radius Pencarian</h6>
            <div class="d-flex align-items-center gap-2">
                <input type="range" class="form-range" min="50" max="1000" step="50"
                       value="{{ auth()->user()->notification_radius }}"
                       oninput="updateRadius(this.value)" id="radius-slider">
                <span id="radius-display" class="fw-bold text-primary" style="min-width:60px">
                    {{ auth()->user()->notification_radius }}m
                </span>
            </div>
        </div>

        <div class="card-custom p-3">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-trash text-danger"></i> Semua Tong Sampah
            </h6>
            <div style="max-height:380px;overflow-y:auto">
                @forelse($bins as $bin)
                @php
                    $c = $bin->percentage >= 90 ? '#dc2626' : ($bin->percentage >= 70 ? '#f59e0b' : '#16a34a');
                    $badge = $bin->percentage >= 90 ? 'danger' : ($bin->percentage >= 70 ? 'warning' : 'success');
                    $label = $bin->percentage >= 90 ? 'Kritis' : ($bin->percentage >= 70 ? 'Warning' : 'Normal');
                @endphp
                <div class="mb-2 p-2 rounded" style="background:var(--bg);border:1px solid var(--border)">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <div style="font-size:13px;font-weight:600">{{ $bin->name }}</div>
                            <div style="font-size:11px;color:var(--text-muted)">{{ $bin->location ?? '-' }}</div>
                        </div>
                        <span class="badge bg-{{ $badge }}">{{ $bin->percentage }}%</span>
                    </div>
                    <div style="height:5px;background:#e5e7eb;border-radius:3px;margin-bottom:8px">
                        <div style="height:100%;width:{{ $bin->percentage }}%;background:{{ $c }};border-radius:3px"></div>
                    </div>
                    @if($bin->percentage >= 70)
                    <button onclick="markEmpty({{ $bin->id }}, this)"
                        class="btn btn-success btn-sm w-100">
                        <i class="fas fa-check"></i> Tandai Sudah Diambil
                    </button>
                    @else
                    <div style="font-size:11px;color:var(--text-muted);text-align:center;padding:4px">
                        ✅ Belum perlu diambil
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-muted text-center">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifikasi Premium -->
<div id="toast" style="
    display:none;position:fixed;bottom:30px;right:30px;z-index:9999;
    background:rgba(22, 163, 74, 0.15);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);
    border:1px solid rgba(22, 163, 74, 0.3);border-left:4px solid #16a34a;
    color:var(--text);padding:16px 24px;border-radius:12px;
    font-size:14px;font-weight:600;box-shadow:0 10px 30px rgba(0,0,0,0.15);
    animation:slideIn 0.4s ease;
    width:320px; transition: all 0.3s ease">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-check-circle text-success" id="toast-icon"></i> 
        <span id="toast-msg"></span>
    </div>
</div>

@endsection

@push('styles')
<style>
@keyframes slideIn {
    from { transform: translateX(100px); opacity: 0; }
    to   { transform: translateX(0); opacity: 1; }
}
</style>
@endpush

@push('scripts')
<script>
    var nearbyMap = L.map('nearby-map', { attributionControl: false }).setView([-7.9797, 112.6304], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(nearbyMap);

    // Koordinat realtime
    nearbyMap.on('mousemove', function(e) {
        document.getElementById('cursor-lng').textContent = e.latlng.lng.toFixed(6);
        document.getElementById('cursor-lat').textContent = e.latlng.lat.toFixed(6);
    });

    var bins = @json($bins->values());
    var userRadius = {{ auth()->user()->notification_radius }};
    var userMarker = null;
    var radiusCircle = null;
    var mapMarkers = {};

    // Tampilkan semua bin di peta
    bins.forEach(function(bin) {
        if (!bin.latitude || !bin.longitude) return;
        var color = bin.percentage >= 90 ? '#dc2626' : (bin.percentage >= 70 ? '#f59e0b' : '#16a34a');
        var icon = L.divIcon({
            className: '',
            html: `<div style="width:40px;height:40px;border-radius:50%;border:3px solid ${color};background:white;
                display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:${color};
                box-shadow:0 2px 8px rgba(0,0,0,0.2)">${bin.percentage}%</div>`,
            iconSize: [40, 40], iconAnchor: [20, 20],
        });
        mapMarkers[bin.id] = L.marker([bin.latitude, bin.longitude], {icon})
            .addTo(nearbyMap)
            .bindPopup(`
                <div style="min-width:180px;font-family:sans-serif">
                    <b style="font-size:14px">${bin.name}</b><br>
                    <span style="color:#6c757d;font-size:12px">${bin.location ?? '-'}</span>
                    <hr style="margin:8px 0">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span style="font-size:12px">Volume</span>
                        <b style="color:${color}">${bin.percentage}%</b>
                    </div>
                    <div style="height:6px;background:#e5e7eb;border-radius:3px;margin-bottom:10px">
                        <div style="height:100%;width:${bin.percentage}%;background:${color};border-radius:3px"></div>
                    </div>
                    ${bin.percentage >= 70 ? `
                    <button onclick="markEmpty(${bin.id}, null)"
                        style="width:100%;padding:8px;background:#16a34a;color:white;border:none;border-radius:8px;
                        cursor:pointer;font-size:12px;font-weight:600">
                        ✅ Tandai Sudah Diambil
                    </button>` : `<div style="text-align:center;font-size:12px;color:#6c757d">✅ Belum perlu diambil</div>`}
                </div>
            `);
    });

    // Auto fit
    var validBins = bins.filter(b => b.latitude && b.longitude);
    if (validBins.length > 0) {
        var group = L.featureGroup(validBins.map(b => L.marker([b.latitude, b.longitude])));
        nearbyMap.fitBounds(group.getBounds().pad(0.3));
    }

    function updateRadius(val) {
        userRadius = parseInt(val);
        document.getElementById('radius-display').textContent = val + 'm';
        if (userMarker) {
            var pos = userMarker.getLatLng();
            if (radiusCircle) nearbyMap.removeLayer(radiusCircle);
            radiusCircle = L.circle([pos.lat, pos.lng], {
                radius: userRadius,
                color: '#2563eb', fillOpacity: 0.05, weight: 1, dashArray: '5,5'
            }).addTo(nearbyMap);
            findNearby(pos.lat, pos.lng);
        }
    }

    function getMyLocation(isManual = false) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;

            if (userMarker) nearbyMap.removeLayer(userMarker);
            if (radiusCircle) nearbyMap.removeLayer(radiusCircle);

            userMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="width:16px;height:16px;background:#2563eb;border-radius:50%;
                        border:3px solid white;box-shadow:0 0 0 6px rgba(37,99,235,0.25)"></div>`,
                    iconSize: [16,16], iconAnchor: [8,8]
                })
            }).addTo(nearbyMap).bindPopup('📍 Lokasi Saya');

            radiusCircle = L.circle([lat, lng], {
                radius: userRadius,
                color: '#2563eb', fillOpacity: 0.05, weight: 1, dashArray: '5,5'
            }).addTo(nearbyMap);

            if (isManual) {
                userMarker.openPopup();
                nearbyMap.flyTo([lat, lng], 15);
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

        // Notif jika ada yang kritis
        var critical = nearby.filter(b => b.percentage >= 90);
        if (critical.length > 0) {
            showToast(`⚠️ ${critical.length} tong sampah PENUH dalam radius ${userRadius}m!`, '#dc2626');
        }
    }

    function markEmpty(binId, btn) {
        Swal.fire({
            title: 'Tandai Sudah Diambil?',
            text: 'Apakah Anda yakin ingin menandai tong sampah ini sudah dikosongkan oleh petugas?',
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
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        showToast(d.message, '#16a34a');
                        if (btn) {
                            btn.outerHTML = `<div style="font-size:11px;color:#16a34a;text-align:center;padding:4px;font-weight:600">
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
                        background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a2e' : '#ffffff',
                        color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8f9fa' : '#1a1a2e',
                        borderRadius: '16px'
                    });
                });
            }
        });
    }

    function showToast(msg, color) {
        var toast = document.getElementById('toast');
        var toastMsg = document.getElementById('toast-msg');
        var isDanger = color === '#dc2626';
        
        toast.style.borderLeft = isDanger ? '4px solid #dc2626' : '4px solid #16a34a';
        toast.style.background = isDanger ? 'rgba(220, 38, 38, 0.15)' : 'rgba(22, 163, 74, 0.15)';
        toast.style.border = isDanger ? '1px solid rgba(220, 38, 38, 0.3)' : '1px solid rgba(22, 163, 74, 0.3)';
        toast.style.borderLeftWidth = '4px';
        
        var icon = document.getElementById('toast-icon');
        if (icon) {
            icon.className = isDanger ? 'fas fa-exclamation-triangle text-danger animate__animated animate__pulse animate__infinite' : 'fas fa-check-circle text-success';
        }
        
        toastMsg.textContent = msg;
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 4000);
    }
</script>
@endpush