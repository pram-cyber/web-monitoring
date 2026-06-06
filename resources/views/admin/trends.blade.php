@extends('layouts.main')

@section('content')

<div class="mb-4">
    <h5 class="fw-bold mb-1">Tren Volume Sampah</h5>
    <small style="color:var(--text-muted)">Analisis tren mingguan dan bulanan</small>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card-custom p-3">
            <h6 class="fw-bold mb-3"><i class="fas fa-chart-line text-primary"></i> Tren Mingguan</h6>
            <canvas id="weeklyChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-3">
            <h6 class="fw-bold mb-3"><i class="fas fa-chart-bar text-success"></i> Tren Bulanan</h6>
            <canvas id="monthlyChart" height="240"></canvas>
        </div>
    </div>

    <!-- Per Bin Chart -->
    <div class="col-12">
        <div class="card-custom p-3">
            <h6 class="fw-bold mb-3"><i class="fas fa-trash text-warning"></i> Volume Per Tong Sampah</h6>
            <div class="row g-3">
                @foreach($bins as $bin)
                @php $color = $bin->percentage >= 90 ? '#dc2626' : ($bin->percentage >= 70 ? '#f59e0b' : '#16a34a'); @endphp
                <div class="col-md-3">
                    <div class="card-custom p-3 text-center">
                        <div style="font-size:13px;font-weight:600">{{ $bin->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);margin-bottom:10px">{{ $bin->location ?? '-' }}</div>
                        <div style="width:70px;height:70px;border-radius:50%;border:5px solid {{ $color }};margin:0 auto;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:{{ $color }}">
                            {{ $bin->percentage }}%
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
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
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.1)',
                fill: true, tension: 0.4, pointRadius: 5,
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });

    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: @json($monthlyLabels),
            datasets: [{
                data: @json($monthlyData),
                backgroundColor: 'rgba(22,163,74,0.7)',
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });
</script>
@endpush