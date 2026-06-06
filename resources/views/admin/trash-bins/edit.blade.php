@extends('layouts.main')

@section('content')

<div class="mb-4">
    <h5 class="fw-bold mb-1">Edit Trash Bin</h5>
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

            <form action="{{ route('admin.trash-bins.update', $bin->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $bin->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback" style="font-size:11px">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $bin->location) }}">
                    @error('location')
                        <div class="invalid-feedback" style="font-size:11px">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row g-2 mb-3">
                    <div class="col">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $bin->latitude) }}" required>
                        @error('latitude')
                            <div class="invalid-feedback" style="font-size:11px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $bin->longitude) }}" required>
                        @error('longitude')
                            <div class="invalid-feedback" style="font-size:11px">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kedalaman Max (cm)</label>
                    <input type="number" name="max_depth_cm" class="form-control @error('max_depth_cm') is-invalid @enderror" value="{{ old('max_depth_cm', $bin->max_depth_cm) }}" required>
                    @error('max_depth_cm')
                        <div class="invalid-feedback" style="font-size:11px">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Status Aktif</label>
                    <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                        <option value="1" {{ old('is_active', $bin->is_active) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !old('is_active', $bin->is_active) ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback" style="font-size:11px">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <a href="{{ route('admin.trash-bins.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection