@extends('layouts.main')

@section('content')

<div class="mb-4">
    <h5 class="fw-bold mb-1">Tambah Trash Bin</h5>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card-custom p-4">
            <!-- Global error display -->
            @if ($errors->any())
            <div class="alert alert-danger p-3 mb-3 border-0" style="font-size:13px; border-radius:8px; background-color:#fef2f2; color:#dc2626">
                <div class="fw-bold mb-2"><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan pengisian data:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.trash-bins.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Bin 001">
                    @error('name')
                        <div class="invalid-feedback" style="font-size:11px">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" placeholder="Gedung A Lantai 1">
                    @error('location')
                        <div class="invalid-feedback" style="font-size:11px">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Kedalaman Max (cm)</label>
                    <input type="number" name="max_depth_cm" class="form-control @error('max_depth_cm') is-invalid @enderror" value="{{ old('max_depth_cm') }}" required placeholder="50">
                    @error('max_depth_cm')
                        <div class="invalid-feedback" style="font-size:11px">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('admin.trash-bins.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection