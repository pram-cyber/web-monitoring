@extends('layouts.main')

@section('content')

<div class="mb-6">
    <h5 class="text-xl font-bold text-slate-800 dark:text-slate-100">Laporan Masalah</h5>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Laporkan tong sampah rusak, hilang, atau terjadi penumpukan</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Form Laporan -->
    <div class="lg:col-span-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 p-5 rounded-2xl shadow-sm h-fit">
        <h6 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-4 flex items-center gap-2">
            <i class="fas fa-plus text-blue-500"></i> Buat Laporan Baru
        </h6>
        
        <form action="{{ route('user.reports.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-455 uppercase mb-1.5">Pilih Tong Sampah</label>
                <select name="trash_bin_id" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all" required>
                    <option value="">-- Pilih Tong Sampah --</option>
                    @foreach($bins as $bin)
                    <option value="{{ $bin->id }}">{{ $bin->name }} - {{ $bin->location }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-455 uppercase mb-1.5">Jenis Masalah</label>
                <select name="type" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all" required>
                    <option value="rusak">🔧 Tong Sampah Rusak</option>
                    <option value="hilang">❌ Tong Sampah Hilang</option>
                    <option value="penumpukan">⚠️ Penumpukan Sampah</option>
                    <option value="sensor">📡 Sensor Tidak Akurat</option>
                    <option value="lainnya">📝 Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-455 uppercase mb-1.5">Deskripsi</label>
                <textarea name="description" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-emerald-500 transition-all" rows="4"
                          required placeholder="Jelaskan masalah secara detail..."></textarea>
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-semibold bg-rose-500 hover:bg-rose-600 text-white rounded-xl shadow-md transition-all active:scale-95 cursor-pointer">
                <i class="fas fa-flag text-xs"></i> Kirim Laporan
            </button>
        </form>
    </div>

    <!-- Riwayat Laporan -->
    <div class="lg:col-span-7 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 p-5 rounded-2xl shadow-sm">
        <h6 class="font-bold text-slate-800 dark:text-slate-100 text-sm mb-4 flex items-center gap-2">
            <i class="fas fa-list text-amber-500"></i> Laporan Saya
        </h6>
        
        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
            @forelse($myReports as $report)
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
            <div class="bg-slate-50/50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800/80 p-4 rounded-xl shadow-sm">
                <div class="flex justify-between items-start gap-3">
                    <div class="min-w-0 flex-grow">
                        <div class="font-bold text-xs text-slate-850 dark:text-slate-200 truncate">{{ $report->trashBin->name ?? '-' }}</div>
                        <p class="text-xs text-slate-600 dark:text-slate-350 mt-1.5 leading-relaxed">{{ $report->description }}</p>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-3 flex items-center gap-1 font-semibold">
                            <i class="far fa-clock"></i> {{ $report->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-md {{ $t[1] }}">{{ $t[0] }}</span>
                        <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-md {{ $report->status === 'resolved' ? 'bg-emerald-500/10 text-emerald-650 dark:text-emerald-450 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-605 dark:text-amber-450 border border-amber-500/20' }}">
                            {{ $report->status === 'resolved' ? '✅ Selesai' : '⏳ Pending' }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-slate-450 dark:text-slate-500 py-16">
                <i class="fas fa-flag fa-2x mb-3 opacity-40"></i>
                <div class="text-sm font-medium">Belum Ada Laporan</div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">Anda belum mengirimkan laporan masalah tong sampah saat ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection