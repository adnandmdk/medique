<x-app-layout title="Tambah Rumah Sakit">
    <x-slot name="header"><div class="topbar-title">Tambah Rumah Sakit</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.hospitals.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </x-slot>

    <div class="form-wrap">
        <div class="form-section">
            <div class="form-section-title">Data Rumah Sakit</div>
            <div class="form-section-sub">Isi informasi rumah sakit baru</div>

            <form method="POST" action="{{ route('admin.hospitals.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nama Rumah Sakit <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control {{ $errors->has('name') ? 'is-error' : '' }}"
                           placeholder="contoh: RSUD Kota Medika">
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Kode RS <span class="req">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           class="form-control {{ $errors->has('code') ? 'is-error' : '' }}"
                           placeholder="contoh: RSUDKM"
                           maxlength="10"
                           style="text-transform:uppercase;">
                    <div class="form-hint">Kode unik maks. 10 karakter (huruf & angka)</div>
                    @error('code')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control"
                              placeholder="Jl. Kesehatan No. 1...">{{ old('address') }}</textarea>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="form-control" placeholder="021-12345678">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-control" placeholder="info@rs.id">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline') }}"
                           class="form-control" placeholder="contoh: Melayani dengan Hati"
                           maxlength="100">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Simpan Rumah Sakit
                    </button>
                    <a href="{{ route('admin.hospitals.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>