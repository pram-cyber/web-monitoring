@extends('layouts.main')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Manajemen Trash Bin</h5>
        <small style="color:var(--text-muted)">Kelola semua tong sampah</small>
    </div>
    <a href="{{ route('admin.trash-bins.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Tambah Trash Bin
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible mb-3">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card-custom p-3">
    <div class="mb-3">
        <input type="text" class="form-control form-control-sm" style="max-width:300px"
               placeholder="Cari trash bin..." onkeyup="filterBins(this.value)">
    </div>

    <table class="table table-hover" id="bins-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>Volume</th>
                <th>Jarak Sensor</th>
                <th>Status</th>
                <th>Koordinat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bins as $bin)
            @php
                $color = $bin->percentage >= 90 ? 'danger' : ($bin->percentage >= 70 ? 'warning' : 'success');
                $label = $bin->percentage >= 90 ? 'Kritis' : ($bin->percentage >= 70 ? 'Warning' : 'Normal');
            @endphp
            <tr>
                <td>{{ $bin->id }}</td>
                <td><b>{{ $bin->name }}</b></td>
                <td>{{ $bin->location ?? '-' }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height:8px;min-width:80px">
                            <div id="bar-{{ $bin->id }}" class="progress-bar bg-{{ $color }}" style="width:{{ $bin->percentage }}%"></div>
                        </div>
                        <small id="perc-{{ $bin->id }}">{{ $bin->percentage }}%</small>
                    </div>
                </td>
                <td id="dist-{{ $bin->id }}">{{ $bin->distance_cm ?? '-' }} cm</td>
                <td><span id="badge-{{ $bin->id }}" class="badge bg-{{ $color }}">{{ $label }}</span></td>
                <td>
                    <small style="font-family:monospace;font-size:11px">
                        {{ $bin->latitude ? number_format($bin->latitude,6).', '.number_format($bin->longitude,6) : '-' }}
                    </small>
                </td>
                <td>
                    <a href="{{ route('admin.trash-bins.edit', $bin->id) }}"
                       class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.trash-bins.destroy', $bin->id) }}"
                          method="POST" class="d-inline"
                          onsubmit="return confirmAction(event, 'Hapus bin ini?')">
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
                        <i class="fas fa-trash"></i>
                        <span>Belum Ada Trash Bin</span>
                        <p>Belum ada data unit tong sampah yang terdaftar di dalam sistem.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
    // Fitur 1: Pencarian / Filter
    function filterBins(val) {
        document.querySelectorAll('#bins-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(val.toLowerCase()) ? '' : 'none';
        });
    }

    // Fitur 2: Auto-Refresh Anti-Cache
    setInterval(function() {
        // Tambahkan ?_t= (timestamp) agar URL selalu unik dan tidak di-cache oleh browser
        let url = '{{ route("admin.trash-bins.index") }}?api=1&_t=' + new Date().getTime();

        console.log("Meminta data terbaru ke: " + url); // Log untuk cek di F12 (Inspect)

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest', // Wajib ada agar Laravel tahu ini AJAX
                'Cache-Control': 'no-cache' // Memaksa browser jangan pakai cache
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Respon server bukan OK');
            return response.json();
        })
        .then(result => {
            if(result.status === 'success') {
                console.log("Data berhasil didapat!", result.data); // Cek data di console

                result.data.forEach(bin => {
                    let textPerc = document.getElementById('perc-' + bin.id);
                    let textDist = document.getElementById('dist-' + bin.id);
                    let bar = document.getElementById('bar-' + bin.id);
                    let badge = document.getElementById('badge-' + bin.id);

                    if(textPerc) textPerc.innerText = bin.percentage + '%';
                    if(textDist) textDist.innerText = bin.distance_cm + ' cm';

                    let color = bin.percentage >= 90 ? 'danger' : (bin.percentage >= 70 ? 'warning' : 'success');
                    let label = bin.percentage >= 90 ? 'Kritis' : (bin.percentage >= 70 ? 'Warning' : 'Normal');

                    if(bar) {
                        bar.style.width = bin.percentage + '%';
                        bar.className = 'progress-bar bg-' + color; 
                    }

                    if(badge) {
                        badge.innerText = label;
                        badge.className = 'badge bg-' + color; 
                    }
                });
            }
        })
        .catch(error => console.error('Gagal mengambil data:', error));
    }, 5000); 
</script>
@endpush