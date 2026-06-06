@extends('layouts.main')

@section('content')

<div class="mb-4">
    <h5 class="fw-bold mb-1">Pengaturan Akun</h5>
    <small style="color:var(--text-muted)">Kelola informasi profil dan keamanan akun Anda</small>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-custom p-4 position-relative" style="min-height: 220px;">
            <h6 class="fw-bold mb-4"><i class="fas fa-user text-primary"></i> Profil Saya</h6>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:60px;height:60px;border-radius:50%;background:#2563eb;color:white;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                    <div style="color:var(--text-muted);font-size:13px">{{ auth()->user()->email }}</div>
                    <span class="badge bg-secondary">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>

            <!-- Tombol Hapus Akun di pojok kanan bawah -->
            <div class="position-absolute" style="bottom: 1.5rem; right: 1.5rem;">
                <form action="{{ route('settings.destroy') }}" method="POST"
                      onsubmit="return confirmAction(event, 'Apakah Anda yakin ingin menghapus akun Anda secara permanen? Semua data laporan dan profil Anda akan terhapus selamanya.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1.5 d-flex align-items-center gap-1 btn-delete-account"
                            style="font-size: 11px; font-weight: 600; border-width: 1.5px; transition: all 0.2s ease;">
                        <i class="fas fa-trash-alt" style="font-size: 11px;"></i> Hapus Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
