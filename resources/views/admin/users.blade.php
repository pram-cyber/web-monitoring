@extends('layouts.main')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Manajemen User</h5>
        <small style="color:var(--text-muted)">Kelola siapa saja yang bisa akses sistem</small>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fas fa-plus"></i> Tambah User
    </button>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="label">Total User</div>
                <div class="value text-primary">{{ $users->count() }}</div>
            </div>
            <div class="stat-icon" style="background:#eff6ff">
                <i class="fas fa-users" style="color:#2563eb"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="label">User Aktif</div>
                <div class="value text-success">{{ $users->where('is_active', true)->count() }}</div>
            </div>
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="fas fa-user-check" style="color:#16a34a"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="label">User Nonaktif</div>
                <div class="value text-danger">{{ $users->where('is_active', false)->count() }}</div>
            </div>
            <div class="stat-icon" style="background:#fef2f2">
                <i class="fas fa-user-times" style="color:#dc2626"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabel User -->
<div class="card-custom p-3">
    <div class="d-flex gap-2 mb-3">
        <input type="text" class="form-control form-control-sm" style="max-width:300px"
               placeholder="Cari user..." onkeyup="filterUsers(this.value)">
        <select class="form-select form-select-sm" style="max-width:150px" onchange="filterRole(this.value)">
            <option value="">Semua Role</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>
    </div>

    <table class="table table-hover" id="users-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Radius Notif</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr data-role="{{ $user->role }}">
                <td>{{ $loop->iteration }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;border-radius:50%;background:#2563eb;color:white;
                                    display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600">{{ $user->name }}</div>
                            <div style="font-size:11px;color:var(--text-muted)">
                                Bergabung {{ $user->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                </td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="badge {{ $user->role === 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" 
                               {{ $user->is_active ? 'checked' : '' }}
                               {{ $user->id === auth()->id() ? 'disabled' : '' }}
                               onchange="toggleUser({{ $user->id }}, this.checked)">
                    </div>
                </td>
                <td>{{ $user->notification_radius }}m</td>
                <td>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirmAction(event, 'Apakah Anda yakin ingin menghapus user ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted">Belum ada user</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--card-bg);color:var(--text)">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah User Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterUsers(val) {
        document.querySelectorAll('#users-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(val.toLowerCase()) ? '' : 'none';
        });
    }

    function filterRole(role) {
        document.querySelectorAll('#users-table tbody tr').forEach(row => {
            row.style.display = !role || row.dataset.role === role ? '' : 'none';
        });
    }

    function toggleUser(id, active) {
        fetch(`/admin/users/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ is_active: active })
        })
        .then(r => {
            if(!r.ok) throw new Error('Network response not OK');
            return r.json();
        })
        .then(d => {
            if (d.success) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
                Toast.fire({
                    icon: 'success',
                    title: active ? 'User berhasil diaktifkan!' : 'User berhasil dinonaktifkan!',
                    background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1a2e' : '#ffffff',
                    color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f8f9fa' : '#1a1a2e'
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat mengubah status user.',
                confirmButtonColor: '#dc2626'
            });
        });
    }
</script>
@endpush