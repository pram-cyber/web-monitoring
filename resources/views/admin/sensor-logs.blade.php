@extends('layouts.main')

@section('content')

<!-- FLASH ALERTS -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius: 10px; box-shadow: 0 4px 12px rgba(22,163,74,0.08)">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2 fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">Riwayat Log Sensor</h5>
        <small style="color:var(--text-muted)">Data pembacaan sensor ultrasonik & GPS</small>
    </div>
    
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <!-- Date Filter Form -->
        <form method="GET" action="{{ route('admin.sensor-logs') }}" class="d-flex gap-2 align-items-center flex-wrap">
            <div class="input-group input-group-sm" style="width: 160px">
                <span class="input-group-text bg-white" style="border-color: var(--border)"><i class="fas fa-calendar-alt text-muted"></i></span>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" title="Tanggal Mulai" style="border-color: var(--border)">
            </div>
            <div class="text-muted" style="font-size:11px; font-weight:600">s/d</div>
            <div class="input-group input-group-sm" style="width: 160px">
                <span class="input-group-text bg-white" style="border-color: var(--border)"><i class="fas fa-calendar-alt text-muted"></i></span>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" title="Tanggal Akhir" style="border-color: var(--border)">
            </div>
            <button type="submit" class="btn btn-primary btn-sm px-3" style="border-radius:6px">
                <i class="fas fa-filter"></i> Filter
            </button>
            @if(request()->filled('start_date') || request()->filled('end_date'))
                <a href="{{ route('admin.sensor-logs') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:6px">
                    <i class="fas fa-sync-alt"></i> Reset
                </a>
            @endif
        </form>

        <!-- Clear All Button -->
        @if($logs->total() > 0)
            <form method="POST" action="{{ route('admin.sensor-logs.clear') }}" onsubmit="return confirmAction(event, 'Apakah Anda yakin ingin menghapus semua riwayat log sensor? Tindakan ini tidak dapat dibatalkan.')" class="d-inline ms-md-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm px-3" style="border-radius:6px">
                    <i class="fas fa-trash-alt me-1"></i> Kosongkan Log
                </button>
            </form>
        @endif
    </div>
</div>

<div class="card-custom p-3" style="box-shadow: 0 4px 16px rgba(0,0,0,0.02)">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Trash Bin</th>
                    <th>Jarak (cm)</th>
                    <th>Volume</th>
                    <th>Status</th>
                    <th>Koordinat GPS</th>
                    <th>Waktu</th>
                    <th class="text-center" style="width: 100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php $color = $log->percentage >= 90 ? 'danger' : ($log->percentage >= 70 ? 'warning' : 'success'); @endphp
                <tr>
                    <td>{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
                    <td><b>{{ $log->trashBin->name ?? '-' }}</b></td>
                    <td>{{ $log->distance_cm }} cm</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress" style="height:8px;width:80px;border-radius:4px">
                                <div class="progress-bar bg-{{ $color }}" style="width:{{ $log->percentage }}%;border-radius:4px"></div>
                            </div>
                            <small class="fw-bold" style="font-size:11px">{{ $log->percentage }}%</small>
                        </div>
                    </td>
                    <td><span class="badge bg-{{ $color }}" style="padding: 5px 10px">{{ ucfirst($log->status) }}</span></td>
                    <td>
                        <small style="font-family:monospace;font-size:11px">{{ $log->latitude }}, {{ $log->longitude }}</small>
                    </td>
                    <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('admin.sensor-logs.destroy', $log->id) }}" onsubmit="return confirmAction(event, 'Hapus log sensor ini?')" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" style="border-radius: 6px" title="Hapus Log">
                                <i class="fas fa-trash-alt" style="font-size:13px"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-microchip"></i>
                        <span>Tidak Ditemukan Log Sensor</span>
                        <p>Belum ada data log pembacaan sensor atau filter tidak cocok.</p>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>

@endsection