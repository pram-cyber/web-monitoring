@extends('layouts.main')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Laporan dari User</h5>
        <small style="color:var(--text-muted)">Kelola laporan masalah tong sampah</small>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-danger p-2">{{ $pending }} Pending</span>
        <span class="badge bg-success p-2">{{ $resolved }} Selesai</span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-3">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Filter -->
<div class="d-flex gap-2 mb-3">
    <select class="form-select form-select-sm" style="max-width:150px" onchange="filterStatus(this.value)">
        <option value="">Semua Status</option>
        <option value="pending">Pending</option>
        <option value="resolved">Selesai</option>
    </select>
    <select class="form-select form-select-sm" style="max-width:150px" onchange="filterType(this.value)">
        <option value="">Semua Jenis</option>
        <option value="rusak">Rusak</option>
        <option value="hilang">Hilang</option>
        <option value="penumpukan">Penumpukan</option>
        <option value="sensor">Sensor</option>
        <option value="lainnya">Lainnya</option>
    </select>
</div>

<div class="card-custom p-3">
    <table class="table table-hover" id="reports-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Pelapor</th>
                <th>Tong Sampah</th>
                <th>Jenis</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Waktu</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
            @php
                $types = [
                    'rusak'      => ['Rusak','warning'],
                    'hilang'     => ['Hilang','danger'],
                    'penumpukan' => ['Penumpukan','info'],
                    'sensor'     => ['Sensor','secondary'],
                    'lainnya'    => ['Lainnya','dark']
                ];
                $t = $types[$report->type] ?? [$report->type,'secondary'];
            @endphp
            <tr data-status="{{ $report->status }}" data-type="{{ $report->type }}">
                <td>{{ $loop->iteration + ($reports->currentPage() - 1) * $reports->perPage() }}</td>
                <td>
                    <div style="font-size:13px;font-weight:600">{{ $report->user->name ?? '-' }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">{{ $report->user->email ?? '-' }}</div>
                </td>
                <td>
                    <div style="font-size:13px;font-weight:600">{{ $report->trashBin->name ?? '-' }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">{{ $report->trashBin->location ?? '-' }}</div>
                </td>
                <td><span class="badge bg-{{ $t[1] }}">{{ $t[0] }}</span></td>
                <td style="max-width:200px;font-size:13px">{{ $report->description }}</td>
                <td>
                    <span class="badge {{ $report->status === 'resolved' ? 'bg-success' : 'bg-warning' }}">
                        {{ $report->status === 'resolved' ? '✅ Selesai' : '⏳ Pending' }}
                    </span>
                </td>
                <td style="font-size:12px">{{ $report->created_at->format('d M Y, H:i') }}</td>
                <td>
                    @if($report->status === 'pending')
                    <form action="{{ route('admin.reports.resolve', $report->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-success" title="Tandai Selesai">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    @else
                    <span style="color:#16a34a;font-size:12px">
                        <i class="fas fa-check-circle"></i> Selesai
                    </span>
                    @endif
                    <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirmAction(event, 'Hapus laporan ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-flag"></i>
                        <span>Belum Ada Laporan</span>
                        <p>Tidak ada laporan masalah dari pengguna saat ini.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $reports->links() }}
</div>

@endsection

@push('scripts')
<script>
    function filterStatus(val) {
        document.querySelectorAll('#reports-table tbody tr').forEach(row => {
            row.style.display = !val || row.dataset.status === val ? '' : 'none';
        });
    }
    function filterType(val) {
        document.querySelectorAll('#reports-table tbody tr').forEach(row => {
            row.style.display = !val || row.dataset.type === val ? '' : 'none';
        });
    }
</script>
@endpush