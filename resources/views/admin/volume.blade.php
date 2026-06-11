@extends('layouts.main')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Volume Tong Sampah</h5>
        <small style="color:var(--text-muted)">Tingkat pengisian tiap tong sampah secara realtime</small>
    </div>
    <div style="font-size:13px;color:var(--text-muted)">
        <i class="fas fa-sync-alt"></i> Update otomatis setiap 30 detik
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <div class="label">Rata-rata Volume</div>
                <div class="value text-primary">{{ round($bins->avg('percentage')) }}%</div>
            </div>
            <div class="stat-icon" style="background:#eff6ff">
                <i class="fas fa-percentage" style="color:#2563eb"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <div class="label">Full (&gt;85%)</div>
                <div class="value text-danger">{{ $bins->where('percentage','>',85)->count() }}</div>
            </div>
            <div class="stat-icon" style="background:#fef2f2">
                <i class="fas fa-exclamation-circle" style="color:#dc2626"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <div class="label">Normal (25% - 85%)</div>
                <div class="value text-warning">{{ $bins->whereBetween('percentage',[25,85])->count() }}</div>
            </div>
            <div class="stat-icon" style="background:#fffbeb">
                <i class="fas fa-exclamation-triangle" style="color:#f59e0b"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <div class="label">Empty (&lt;25%)</div>
                <div class="value text-success">{{ $bins->where('percentage','<',25)->count() }}</div>
            </div>
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="fas fa-check-circle" style="color:#16a34a"></i>
            </div>
        </div>
    </div>
</div>

<!-- Volume Cards -->
<div class="row g-3">
    @forelse($bins as $bin)
    @php
        $c     = $bin->percentage > 85 ? '#dc2626' : ($bin->percentage < 25 ? '#16a34a' : '#f59e0b');
        $bg    = $bin->percentage > 85 ? '#fef2f2' : ($bin->percentage < 25 ? '#f0fdf4' : '#fffbeb');
        $label = $bin->percentage > 85 ? 'Full' : ($bin->percentage < 25 ? 'Empty' : 'Normal');
        $badge = $bin->percentage > 85 ? 'danger' : ($bin->percentage < 25 ? 'success' : 'warning');
    @endphp
    <div class="col-md-4">
        <div class="card-custom p-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="fw-bold">{{ $bin->name }}</div>
                    <div style="font-size:12px;color:var(--text-muted)">{{ $bin->location ?? '-' }}</div>
                </div>
                <span class="badge bg-{{ $badge }}">{{ $label }}</span>
            </div>

            <!-- Circle Progress -->
            <div class="text-center mb-3">
                <div style="width:100px;height:100px;border-radius:50%;border:10px solid {{ $c }};margin:0 auto;display:flex;align-items:center;justify-content:center;background:{{ $bg }}">
                    <div>
                        <div style="font-size:22px;font-weight:700;color:{{ $c }}">{{ $bin->percentage }}%</div>
                        <div style="font-size:10px;color:var(--text-muted)">penuh</div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress mb-2" style="height:10px;border-radius:5px">
                <div class="progress-bar" style="width:{{ $bin->percentage }}%;background:{{ $c }};border-radius:5px"></div>
            </div>

            <!-- Info -->
            <div class="d-flex justify-content-between mb-2">
                <small style="color:var(--text-muted)">
                    {{ round($bin->percentage * $bin->max_depth_cm / 100) }} cm terisi
                </small>
                <small style="color:var(--text-muted)">
                    {{ $bin->max_depth_cm }} cm kapasitas
                </small>
            </div>

            <div class="d-flex justify-content-between">
                <small style="color:var(--text-muted)">
                    <i class="fas fa-ruler"></i> Jarak: {{ $bin->distance_cm }} cm
                </small>
                <small style="color:var(--text-muted)">
                    <i class="fas fa-map-marker-alt"></i>
                    {{ $bin->latitude ? number_format($bin->latitude,4).','.number_format($bin->longitude,4) : 'Belum dikalibrasi' }}
                </small>
            </div>

            @if($bin->percentage > 85)
            <div class="mt-2 p-2 rounded text-center" style="background:#fef2f2;color:#dc2626;font-size:12px;font-weight:600">
                ⚠️ Segera kosongkan tong sampah ini!
            </div>
            @endif
        </div>
    </div>
    <div class="col-12">
        <div class="empty-state">
            <i class="fas fa-trash"></i>
            <span>Belum Ada Data Tong Sampah</span>
            <p>Sistem belum mendeteksi atau merekam adanya data unit tong sampah.</p>
        </div>
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
    // Auto refresh setiap 30 detik
    setTimeout(function() { location.reload(); }, 30000);
</script>
@endpush