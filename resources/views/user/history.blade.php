@extends('layouts.main')

@section('content')

<div class="mb-4">
    <h5 class="fw-bold mb-1">Riwayat Tong Sampah</h5>
    <small style="color:var(--text-muted)">History kapan tong penuh dan diambil</small>
</div>

<div class="card-custom p-3">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Tong Sampah</th>
                <th>Lokasi</th>
                <th>Volume</th>
                <th>Status</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $log)
            <tr>
                <td><b>{{ $log->trashBin->name ?? '-' }}</b></td>
                <td>{{ $log->trashBin->location ?? '-' }}</td>
                <td>
                    <div class="progress" style="height:8px;width:100px">
                        <div class="progress-bar {{ $log->percentage >= 90 ? 'bg-danger' : ($log->percentage >= 70 ? 'bg-warning' : 'bg-success') }}"
                             style="width:{{ $log->percentage }}%"></div>
                    </div>
                    <small>{{ $log->percentage }}%</small>
                </td>
                <td>
                    <span class="badge {{ $log->percentage >= 90 ? 'bg-danger' : 'bg-success' }}">
                        {{ $log->percentage >= 90 ? 'Penuh' : 'Normal' }}
                    </span>
                </td>
                <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-history"></i>
                        <span>Belum Ada Riwayat</span>
                        <p>Sistem belum mendeteksi adanya riwayat pengisian atau pengambilan tong sampah.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $histories->links() }}
</div>

@endsection