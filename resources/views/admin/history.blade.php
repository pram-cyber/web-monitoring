@extends('layouts.main')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Riwayat Tong Sampah</h5>
        <small style="color:var(--text-muted)">Kapan tong sampah penuh dan diambil</small>
    </div>
    <div class="d-flex gap-2">
        <select class="form-select form-select-sm" id="filter-bin" onchange="filterHistory()">
            <option value="">Semua Bin</option>
            @foreach($bins as $bin)
            <option value="{{ $bin->id }}">{{ $bin->name }}</option>
            @endforeach
        </select>
        <select class="form-select form-select-sm" id="filter-status" onchange="filterHistory()">
            <option value="">Semua Status</option>
            <option value="full">Penuh</option>
            <option value="diambil">Diambil</option>
        </select>
    </div>
</div>

<!-- Timeline -->
<div class="row g-3">
    <div class="col-md-8">
        <div class="card-custom p-3">
            <h6 class="fw-bold mb-3"><i class="fas fa-history text-primary"></i> Timeline Riwayat</h6>
            <div id="history-list">
                @forelse($histories as $log)
                <div class="history-item d-flex gap-3 mb-3" 
                     data-bin="{{ $log->trash_bin_id }}"
                     data-status="{{ ($log->status === 'full' || $log->percentage >= 90) ? 'full' : 'diambil' }}">
                    <!-- Icon -->
                    <div style="display:flex; flex-direction:column; align-items:center">
                        <div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;
                            background:{{ ($log->status === 'full' || $log->percentage >= 90) ? '#fef2f2' : '#f0fdf4' }}">
                            <i class="fas {{ ($log->status === 'full' || $log->percentage >= 90) ? 'fa-exclamation-triangle text-danger' : 'fa-check-circle text-success' }}"></i>
                        </div>
                        <div style="width:2px;flex:1;background:var(--border);margin-top:4px"></div>
                    </div>
                    <!-- Content -->
                    <div class="card-custom p-3 flex-grow-1 mb-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold">{{ $log->trashBin->name ?? '-' }}</div>
                                <div style="color:var(--text-muted);font-size:13px">
                                    {{ $log->trashBin->location ?? '-' }}
                                </div>
                            </div>
                            <span class="badge {{ ($log->status === 'full' || $log->percentage >= 90) ? 'bg-danger' : 'bg-success' }}">
                                {{ ($log->status === 'full' || $log->percentage >= 90) ? 'Penuh' : 'Diambil' }}
                            </span>
                        </div>
                        <div class="d-flex gap-3 mt-2">
                            <small style="color:var(--text-muted)">
                                <i class="fas fa-clock"></i>
                                {{ $log->recorded_at ? \Carbon\Carbon::parse($log->recorded_at)->format('d M Y, H:i') : $log->created_at->format('d M Y, H:i') }}
                            </small>
                            <small style="color:var(--text-muted)">
                                <i class="fas fa-tachometer-alt"></i> {{ $log->percentage }}% penuh
                            </small>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <span>Belum Ada Riwayat</span>
                    <p>Sistem belum mendeteksi adanya riwayat pengisian atau pengambilan tong sampah.</p>
                </div>
                @endforelse
            </div>
            <div class="mt-3">{{ $histories->links() }}</div>
        </div>
    </div>

    <!-- Stats Sidebar -->
    <div class="col-md-4">
        <div class="card-custom p-3 mb-3">
            <h6 class="fw-bold mb-3"><i class="fas fa-chart-pie text-warning"></i> Statistik</h6>
            <div class="d-flex justify-content-between mb-2">
                <span style="font-size:13px">Total Penuh Hari Ini</span>
                <span class="badge bg-danger">{{ $fullToday }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span style="font-size:13px">Total Diambil Hari Ini</span>
                <span class="badge bg-success">{{ $takenToday }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span style="font-size:13px">Total Penuh Minggu Ini</span>
                <span class="badge bg-warning">{{ $fullWeek }}</span>
            </div>
            <hr>
            <canvas id="historyChart" height="180"></canvas>
        </div>

        <!-- Most Active Bins -->
        <div class="card-custom p-3">
            <h6 class="fw-bold mb-3"><i class="fas fa-fire text-danger"></i> Paling Sering Penuh</h6>
            @foreach($mostFull as $item)
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <div style="font-size:13px;font-weight:600">{{ $item->trashBin->name ?? '-' }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">{{ $item->trashBin->location ?? '-' }}</div>
                </div>
                <span class="badge bg-danger">{{ $item->total }}x</span>
            </div>
            @endforeach
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
            item.style.display = (matchBin && matchStatus) ? '' : 'none';
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
                backgroundColor: ['#dc2626', '#16a34a'],
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