{{-- resources/views/admin/clinics/create.blade.php --}}
<x-app-layout title="Tambah Poliklinik">
    <x-slot name="header"><div class="topbar-title">Tambah Poliklinik</div></x-slot>
    <x-slot name="actions"><a href="{{ route('admin.clinics.index') }}" class="btn btn-secondary btn-sm">← Kembali</a></x-slot>
    <div class="form-wrap">
        <div class="form-section">
            <div class="form-section-title">Informasi Poliklinik</div>
            <div class="form-section-sub">Isi data poliklinik baru</div>
            <form action="{{ route('admin.clinics.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Poliklinik <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control {{ $errors->has('name') ? 'is-error' : '' }}"
                           placeholder="contoh: Poli Umum">
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Lokasi <span class="req">*</span></label>
                    <input type="text" name="location" value="{{ old('location') }}"
                           class="form-control {{ $errors->has('location') ? 'is-error' : '' }}"
                           placeholder="contoh: Lantai 1, Gedung A">
                    @error('location')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="check-label">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="check-text">Aktifkan poliklinik ini</span>
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.clinics.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>