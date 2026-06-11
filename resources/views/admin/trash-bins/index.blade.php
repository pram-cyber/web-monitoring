@extends('layouts.main')

@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <div>
        <h5 class="text-xl font-bold text-slate-800 dark:text-slate-100">Manajemen Trash Bin</h5>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kelola semua tong sampah</p>
    </div>
    <a href="{{ route('admin.trash-bins.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold bg-blue-600 dark:bg-emerald-600 hover:bg-blue-700 dark:hover:bg-emerald-700 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer">
        <i class="fas fa-plus"></i> Tambah Trash Bin
    </a>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl shadow-sm p-5">
    <div class="mb-5">
        <input type="text" class="w-full sm:max-w-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all text-slate-800 dark:text-slate-100"
               placeholder="Cari trash bin..." onkeyup="filterBins(this.value)">
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-150 dark:border-slate-805">
        <table class="w-full border-collapse text-left" id="bins-table">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-4">#</th>
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">Lokasi</th>
                    <th class="px-6 py-4">Volume</th>
                    <th class="px-6 py-4">Jarak Sensor</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Koordinat</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-150 dark:divide-slate-800/60">
                @forelse($bins as $bin)
                @php
                    $isConn = $bin->is_connected;
                    $perc = $isConn ? $bin->percentage : 0;
                    
                    if (!$isConn) {
                        $badgeStyle = 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20';
                        $label = 'Offline';
                    } else {
                        if ($perc > 85) {
                            $badgeStyle = 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20';
                            $label = 'Full';
                        } elseif ($perc < 25) {
                            $badgeStyle = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
                            $label = 'Empty';
                        } else {
                            $badgeStyle = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
                            $label = 'Normal';
                        }
                    }
                @endphp
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-all">
                    <td class="px-6 py-4 text-xs font-mono text-slate-450 dark:text-slate-500">{{ $bin->id }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-800 dark:text-slate-100">{{ $bin->name }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $bin->location ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-20 bg-slate-150 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                <div id="bar-{{ $bin->id }}" class="h-full rounded-full transition-all duration-700" style="width:{{ $perc }}%; background-color: {{ !$isConn ? '#64748b' : ($perc > 85 ? '#f43f5e' : ($perc < 25 ? '#10b981' : '#f59e0b')) }}"></div>
                            </div>
                            <small id="perc-{{ $bin->id }}" class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $isConn ? $bin->percentage . '%' : '-' }}</small>
                        </div>
                    </td>
                    <td id="dist-{{ $bin->id }}" class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300 font-semibold">{{ $isConn ? ($bin->distance_cm ?? '-') . ' cm' : '-' }}</td>
                    <td class="px-6 py-4"><span id="badge-{{ $bin->id }}" class="px-2.5 py-1 text-[10px] font-extrabold rounded-full whitespace-nowrap {{ $badgeStyle }}">{{ $label }}</span></td>
                    <td class="px-6 py-4">
                        <small class="font-mono text-slate-500 dark:text-slate-400 text-xs">
                            {{ $isConn && $bin->latitude ? number_format($bin->latitude,6).', '.number_format($bin->longitude,6) : '-' }}
                        </small>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.trash-bins.edit', $bin->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-amber-500 hover:bg-amber-500/10 rounded-lg border border-amber-500/20 transition-all" title="Edit Bin">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form action="{{ route('admin.trash-bins.destroy', $bin->id) }}" method="POST" class="inline"
                                  onsubmit="return confirmAction(event, 'Hapus bin ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:bg-red-500/10 rounded-lg border border-red-500/20 transition-all cursor-pointer" title="Hapus Bin">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-slate-450 dark:text-slate-500">
                        <i class="fas fa-trash fa-2x mb-3 opacity-40"></i>
                        <div class="text-sm font-medium">Belum Ada Trash Bin</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Belum ada data unit tong sampah yang terdaftar di dalam sistem.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function filterBins(val) {
        document.querySelectorAll('#bins-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(val.toLowerCase()) ? '' : 'none';
        });
    }

    var twColors = {
        'secondary': {
            bg: 'bg-slate-500/10',
            text: 'text-slate-600 dark:text-slate-400',
            border: 'border-slate-500/20',
            hex: '#64748b'
        },
        'danger': {
            bg: 'bg-rose-500/10',
            text: 'text-rose-600 dark:text-rose-400',
            border: 'border-rose-500/20',
            hex: '#f43f5e'
        },
        'success': {
            bg: 'bg-emerald-500/10',
            text: 'text-emerald-600 dark:text-emerald-400',
            border: 'border-emerald-500/20',
            hex: '#10b981'
        },
        'warning': {
            bg: 'bg-amber-500/10',
            text: 'text-amber-600 dark:text-amber-400',
            border: 'border-amber-500/20',
            hex: '#f59e0b'
        }
    };

    // Auto-Refresh Anti-Cache
    setInterval(function() {
        let url = '{{ route("admin.trash-bins.index") }}?api=1&_t=' + new Date().getTime();

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Respon server bukan OK');
            return response.json();
        })
        .then(result => {
            if(result.status === 'success') {
                result.data.forEach(bin => {
                    let textPerc = document.getElementById('perc-' + bin.id);
                    let textDist = document.getElementById('dist-' + bin.id);
                    let bar = document.getElementById('bar-' + bin.id);
                    let badge = document.getElementById('badge-' + bin.id);

                    let isConn = bin.is_connected;
                    if(textPerc) textPerc.innerText = isConn ? bin.percentage + '%' : '-';
                    if(textDist) textDist.innerText = isConn ? bin.distance_cm + ' cm' : '-';

                    let key = !isConn ? 'secondary' : (bin.percentage > 85 ? 'danger' : (bin.percentage < 25 ? 'success' : 'warning'));
                    let style = twColors[key];
                    let label = !isConn ? 'Offline' : (bin.percentage > 85 ? 'Full' : (bin.percentage < 25 ? 'Empty' : 'Normal'));

                    if(bar) {
                        bar.style.width = (isConn ? bin.percentage : 0) + '%';
                        bar.style.backgroundColor = style.hex; 
                    }

                    if(badge) {
                        badge.innerText = label;
                        badge.className = `px-2.5 py-1 text-[10px] font-extrabold rounded-full whitespace-nowrap ${style.bg} ${style.text} ${style.border}`; 
                    }
                });
            }
        })
        .catch(error => console.error('Gagal mengambil data:', error));
    }, 5000); 
</script>
@endpush