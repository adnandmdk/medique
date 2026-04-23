{{-- resources/views/admin/doctors/create.blade.php --}}
<x-app-layout title="Tambah Dokter">
    <x-slot name="header"><div class="topbar-title">Tambah Dokter</div></x-slot>
    <x-slot name="actions"><a href="{{ route('admin.hospitals.doctors.index', $hospital->id) }}" class="btn btn-secondary btn-sm">← Kembali</a></x-slot>
    <div class="form-wrap">
        <div class="form-section">
            <div class="form-section-title">Data Dokter Baru</div>
            <div class="form-section-sub">Hubungkan akun user dengan profil dokter</div>
            <form action="{{ route('admin.hospitals.doctors.store', $hospital->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Akun User (Role: Doctor) <span class="req">*</span></label>
                    <select name="user_id" class="form-control {{ $errors->has('user_id') ? 'is-error' : '' }}">
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Poliklinik <span class="req">*</span></label>
                    <select name="clinic_id" class="form-control {{ $errors->has('clinic_id') ? 'is-error' : '' }}">
                        <option value="">-- Pilih Poliklinik --</option>
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}" {{ old('clinic_id') == $clinic->id ? 'selected' : '' }}>
                                {{ $clinic->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('clinic_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Spesialisasi <span class="req">*</span></label>
                    <input type="text" name="specialization" value="{{ old('specialization') }}"
                           class="form-control {{ $errors->has('specialization') ? 'is-error' : '' }}"
                           placeholder="contoh: Dokter Umum">
                    @error('specialization')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Lisensi <span class="req">*</span></label>
                    <input type="text" name="licence_number" value="{{ old('licence_number') }}"
                           class="form-control {{ $errors->has('licence_number') ? 'is-error' : '' }}"
                           placeholder="contoh: 123/DU/2024">
                    @error('licence_number')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Dokter</button>
                    <a href="{{ route('admin.hospitals.doctors.index', $hospital->id) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>