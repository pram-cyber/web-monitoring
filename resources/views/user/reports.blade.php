@extends('layouts.main')

@section('content')

<div class="mb-4">
    <h5 class="fw-bold mb-1">Laporan Masalah</h5>
    <small style="color:var(--text-muted)">Laporkan tong sampah rusak, hilang, atau penumpukan</small>
</div>

<div class="row g-3">
    <!-- Form Laporan -->
    <div class="col-md-5">
        <div class="card-custom p-3">
            <h6 class="fw-bold mb-3"><i class="fas fa-plus text-primary"></i> Buat Laporan Baru</h6>
            @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            <form action="{{ route('user.reports.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-size:13px">Pilih Tong Sampah</label>
                    <select name="trash_bin_id" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Tong Sampah --</option>
                        @foreach($bins as $bin)
                        <option value="{{ $bin->id }}">{{ $bin->name }} - {{ $bin->location }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:13px">Jenis Masalah</label>
                    <select name="type" class="form-select form-select-sm" required>
                        <option value="rusak">🔧 Tong Sampah Rusak</option>
                        <option value="hilang">❌ Tong Sampah Hilang</option>
                        <option value="penumpukan">⚠️ Penumpukan Sampah</option>
                        <option value="sensor">📡 Sensor Tidak Akurat</option>
                        <option value="lainnya">📝 Lainnya</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:13px">Deskripsi</label>
                    <textarea name="description" class="form-control form-control-sm" rows="3"
                              required placeholder="Jelaskan masalah secara detail..."></textarea>
                </div>
                <button type="submit" class="btn btn-danger btn-sm w-100">
                    <i class="fas fa-flag"></i> Kirim Laporan
                </button>
            </form>
        </div>
    </div>

    <!-- Riwayat Laporan -->
    <div class="col-md-7">
        <div class="card-custom p-3">
            <h6 class="fw-bold mb-3"><i class="fas fa-list text-warning"></i> Laporan Saya</h6>
            @forelse($myReports as $report)
            @php
                $types = ['rusak'=>['Rusak','warning'],'hilang'=>['Hilang','danger'],'penumpukan'=>['Penumpukan','info'],'sensor'=>['Sensor','secondary'],'lainnya'=>['Lainnya','dark']];
                $t = $types[$report->type] ?? [$report->type,'secondary'];
            @endphp
            <div class="card-custom p-3 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold" style="font-size:13px">
                            {{ $report->trashBin->name ?? '-' }}
                        </div>
                        <div style="font-size:12px;color:var(--text-muted)">
                            {{ $report->description }}
                        </div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px">
                            <i class="fas fa-clock"></i>
                            {{ $report->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <span class="badge bg-{{ $t[1] }}">{{ $t[0] }}</span>
                        <span class="badge {{ $report->status === 'resolved' ? 'bg-success' : 'bg-warning' }}">
                            {{ $report->status === 'resolved' ? '✅ Selesai' : '⏳ Pending' }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-flag"></i>
                <span>Belum Ada Laporan</span>
                <p>Anda belum mengirimkan laporan masalah tong sampah saat ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection