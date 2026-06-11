@extends('layouts.main')

@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h5 class="text-xl font-bold text-slate-800 dark:text-slate-100">Manajemen User</h5>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kelola siapa saja yang bisa akses sistem</p>
    </div>
    <button onclick="toggleAddUserModal(true)" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold bg-blue-600 dark:bg-emerald-600 hover:bg-blue-700 dark:hover:bg-emerald-700 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer">
        <i class="fas fa-plus"></i> Tambah User
    </button>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
        <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total User</div>
            <div class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ $users->count() }}</div>
        </div>
        <div class="w-12 h-12 bg-blue-50 dark:bg-blue-950/45 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-inner">
            <i class="fas fa-users text-lg"></i>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
        <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">User Aktif</div>
            <div class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $users->where('is_active', true)->count() }}</div>
        </div>
        <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-950/45 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-inner">
            <i class="fas fa-user-check text-lg"></i>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
        <div>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">User Nonaktif</div>
            <div class="text-3xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">{{ $users->where('is_active', false)->count() }}</div>
        </div>
        <div class="w-12 h-12 bg-rose-50 dark:bg-rose-950/45 rounded-xl flex items-center justify-center text-rose-600 dark:text-rose-400 shadow-inner">
            <i class="fas fa-user-times text-lg"></i>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden p-5">
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <input type="text" class="w-full sm:max-w-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all text-slate-800 dark:text-slate-100"
               placeholder="Cari user..." onkeyup="filterUsers(this.value)">
        <select class="w-full sm:max-w-[150px] bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all text-slate-800 dark:text-slate-100" onchange="filterRole(this.value)">
            <option value="">Semua Role</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-150 dark:border-slate-800">
        <table class="w-full border-collapse text-left" id="users-table">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Radius Notif</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-150 dark:divide-slate-800/60">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-all" data-role="{{ $user->role }}">
                    <td class="px-6 py-4 text-xs font-mono text-slate-450 dark:text-slate-500">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600/10 dark:bg-emerald-600/10 text-blue-600 dark:text-emerald-400 border border-blue-600/20 dark:border-emerald-600/20 flex items-center justify-center text-xs font-black shrink-0 shadow-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $user->name }}</div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">
                                    Bergabung {{ $user->created_at->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-650 dark:text-slate-350">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md {{ $user->role === 'admin' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" 
                                   {{ $user->is_active ? 'checked' : '' }}
                                   {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                   onchange="toggleUser({{ $user->id }}, this.checked)"
                                   class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none dark:bg-slate-800 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500 {{ $user->id === auth()->id() ? 'opacity-50 cursor-not-allowed' : '' }}"></div>
                        </label>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-350 font-semibold">{{ $user->notification_radius }}m</td>
                    <td class="px-6 py-4 text-right">
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                              onsubmit="return confirmAction(event, 'Apakah Anda yakin ingin menghapus user ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:bg-red-500/10 rounded-lg border border-red-500/20 transition-all cursor-pointer" title="Hapus User">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-450 dark:text-slate-500">
                        <i class="fas fa-users fa-2x mb-3 opacity-40"></i>
                        <div class="text-sm font-medium">Belum ada user</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Custom Modal Tambah User -->
<div id="addUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-250 dark:border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
        <button onclick="toggleAddUserModal(false)" class="absolute top-4 right-4 text-slate-400 hover:text-slate-650 dark:hover:text-slate-250 cursor-pointer">
            <i class="fas fa-times text-lg"></i>
        </button>
        
        <h6 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-5">Tambah User Baru</h6>
        
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1.5">Nama</label>
                    <input type="text" name="name" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-250 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1.5">Email</label>
                    <input type="email" name="email" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-250 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1.5">Password</label>
                    <input type="password" name="password" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-250 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-450 uppercase mb-1.5">Role</label>
                    <select name="role" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-250 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                <button type="button" onclick="toggleAddUserModal(false)" class="px-4 py-2 text-xs font-semibold border border-slate-300 dark:border-slate-805 text-slate-650 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-blue-600 dark:bg-emerald-600 hover:bg-blue-700 dark:hover:bg-emerald-700 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function toggleAddUserModal(show) {
        const modal = document.getElementById('addUserModal');
        if (show) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }

    // Close modal on clicking outside
    document.getElementById('addUserModal').addEventListener('click', function(e) {
        if (e.target === this) {
            toggleAddUserModal(false);
        }
    });

    function filterUsers(val) {
        document.querySelectorAll('#users-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(val.toLowerCase()) ? '' : 'none';
        });
    }

    function filterRole(role) {
        document.querySelectorAll('#users-table tbody tr').forEach(row => {
            if (row.dataset.role) {
                row.style.display = !role || row.dataset.role === role ? '' : 'none';
            }
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
                    background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#1e293b'
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