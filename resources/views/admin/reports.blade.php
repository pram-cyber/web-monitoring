@extends('layouts.main')

@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h5 class="text-xl font-bold text-slate-800 dark:text-slate-100">Laporan dari User</h5>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kelola laporan masalah tong sampah</p>
    </div>
    <div class="flex gap-2 shrink-0">
        <span class="px-2.5 py-1 text-xs font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 rounded-full">{{ $pending }} Pending</span>
        <span class="px-2.5 py-1 text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-full">{{ $resolved }} Selesai</span>
    </div>
</div>

<!-- Filter Dropdowns -->
<div class="flex gap-2.5 mb-4 max-w-md">
    <select class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all text-slate-700 dark:text-slate-200" onchange="filterStatus(this.value)">
        <option value="">Semua Status</option>
        <option value="pending">Pending</option>
        <option value="resolved">Selesai</option>
    </select>
    <select class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all text-slate-700 dark:text-slate-200" onchange="filterType(this.value)">
        <option value="">Semua Jenis</option>
        <option value="rusak">Rusak</option>
        <option value="hilang">Hilang</option>
        <option value="penumpukan">Penumpukan</option>
        <option value="sensor">Sensor</option>
        <option value="lainnya">Lainnya</option>
    </select>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left" id="reports-table">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Pelapor</th>
                    <th class="px-6 py-4">Tong Sampah</th>
                    <th class="px-6 py-4">Jenis</th>
                    <th class="px-6 py-4">Deskripsi</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Waktu</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-150 dark:divide-slate-800/60">
                @forelse($reports as $report)
                @php
                    $types = [
                        'rusak'      => ['Rusak','bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20'],
                        'hilang'     => ['Hilang','bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20'],
                        'penumpukan' => ['Penumpukan','bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20'],
                        'sensor'     => ['Sensor','bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20'],
                        'lainnya'    => ['Lainnya','bg-gray-500/10 text-gray-650 dark:text-gray-400 border border-gray-500/20']
                    ];
                    $t = $types[$report->type] ?? [$report->type,'bg-slate-500/10 text-slate-650 border border-slate-500/20'];
                @endphp
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-all" data-status="{{ $report->status }}" data-type="{{ $report->type }}">
                    <td class="px-6 py-4 text-xs font-mono text-slate-400 dark:text-slate-500">
                        {{ $loop->iteration + ($reports->currentPage() - 1) * $reports->perPage() }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="font-bold text-slate-800 dark:text-slate-200">{{ $report->user->name ?? '-' }}</div>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $report->user->email ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="font-bold text-slate-800 dark:text-slate-200">{{ $report->trashBin->name ?? '-' }}</div>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $report->trashBin->location ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md {{ $t[1] }}">{{ $t[0] }}</span>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-350 max-w-[220px] truncate" title="{{ $report->description }}">
                        {{ $report->description }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-full {{ $report->status === 'resolved' ? 'bg-emerald-500/10 text-emerald-650 dark:text-emerald-450 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-605 dark:text-amber-450 border border-amber-500/20' }}">
                            {{ $report->status === 'resolved' ? '✅ Selesai' : '⏳ Pending' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-400 dark:text-slate-500">
                        {{ $report->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($report->status === 'pending')
                            <form action="{{ route('admin.reports.resolve', $report->id) }}" method="POST" class="inline">
                                @csrf
                                <button class="inline-flex items-center justify-center w-8 h-8 text-emerald-500 hover:bg-emerald-500/10 rounded-lg border border-emerald-500/20 transition-all cursor-pointer" title="Tandai Selesai">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> Selesai
                            </span>
                            @endif
                            <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" class="inline"
                                  onsubmit="return confirmAction(event, 'Hapus laporan ini?')">
                                @csrf @method('DELETE')
                                <button class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:bg-red-500/10 rounded-lg border border-red-500/20 transition-all cursor-pointer" title="Hapus Laporan">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-slate-450 dark:text-slate-500">
                        <i class="fas fa-flag fa-2x mb-3 opacity-40"></i>
                        <div class="text-sm font-medium">Belum Ada Laporan</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Tidak ada laporan masalah dari pengguna saat ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($reports->hasPages())
    <div class="p-4 border-t border-slate-200 dark:border-slate-800">
        {{ $reports->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    function filterStatus(val) {
        document.querySelectorAll('#reports-table tbody tr').forEach(row => {
            if (row.dataset.status) {
                row.style.display = !val || row.dataset.status === val ? '' : 'none';
            }
        });
    }
    function filterType(val) {
        document.querySelectorAll('#reports-table tbody tr').forEach(row => {
            if (row.dataset.type) {
                row.style.display = !val || row.dataset.type === val ? '' : 'none';
            }
        });
    }
</script>
@endpush