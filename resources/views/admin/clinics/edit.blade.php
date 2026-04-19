{{-- resources/views/admin/clinics/edit.blade.php --}}
<x-app-layout title="Edit Poliklinik">
    <x-slot name="header"><div class="topbar-title">Edit Poliklinik</div></x-slot>
    <x-slot name="actions"><a href="{{ route('admin.clinics.index') }}" class="btn btn-secondary btn-sm">← Kembali</a></x-slot>
    <div class="form-wrap">
        <div class="form-section">
            <div class="form-section-title">Edit Poliklinik</div>
            <div class="form-section-sub">Perbarui data <strong>{{ $clinic->name }}</strong></div>
            <form action="{{ route('admin.clinics.update', $clinic) }}" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Nama Poliklinik <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $clinic->name) }}"
                           class="form-control {{ $errors->has('name') ? 'is-error' : '' }}">
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Lokasi <span class="req">*</span></label>
                    <input type="text" name="location" value="{{ old('location', $clinic->location) }}"
                           class="form-control {{ $errors->has('location') ? 'is-error' : '' }}">
                    @error('location')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="check-label">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $clinic->is_active) ? 'checked' : '' }}>
                        <span class="check-text">Aktifkan poliklinik ini</span>
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.clinics.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>