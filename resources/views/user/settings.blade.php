@extends('layouts.main')

@section('content')

<div class="mb-4">
    <h5 class="fw-bold mb-1">Pengaturan Notifikasi</h5>
    <small style="color:var(--text-muted)">Atur radius notifikasi tong sampah penuh</small>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card-custom p-4 h-100 position-relative">
            <h6 class="fw-bold mb-4"><i class="fas fa-bell text-warning"></i> Radius Notifikasi</h6>
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form action="{{ route('user.settings.update') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Radius Notifikasi</label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="range" name="notification_radius" class="form-range flex-grow-1"
                               min="50" max="1000" step="50" 
                               value="{{ auth()->user()->notification_radius }}"
                               oninput="document.getElementById('radius-val').textContent = this.value + 'm'">
                        <span id="radius-val" class="fw-bold text-primary" style="min-width:60px">
                            {{ auth()->user()->notification_radius }}m
                        </span>
                    </div>
                    <small style="color:var(--text-muted)">
                        Beri tahu jika ada tong sampah penuh (≥90%) dalam radius ini
                    </small>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-custom p-4 h-100 position-relative">
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
                <form action="{{ route('user.settings.destroy') }}" method="POST"
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