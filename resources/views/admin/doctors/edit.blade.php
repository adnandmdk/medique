{{-- resources/views/admin/doctors/edit.blade.php --}}
<x-app-layout title="Edit Dokter">
    <x-slot name="header"><div class="topbar-title">Edit Dokter</div></x-slot>
    <x-slot name="actions"><a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary btn-sm">← Kembali</a></x-slot>
    <div class="form-wrap">
        <div class="form-section">
            <div class="form-section-title">Edit Data Dokter</div>
            <div class="form-section-sub">Perbarui profil <strong>{{ optional($doctor->user)->name ?? '—' }}</strong></div>
            <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST">
                @csrf @method('PATCH')
                <div class="form-group">
                    <label class="form-label">Akun User <span class="req">*</span></label>
                    <select name="user_id" class="form-control {{ $errors->has('user_id') ? 'is-error' : '' }}">
                        <option value="{{ optional($doctor->user)->id }}" selected>
                            {{ optional($doctor->user)->name ?? '—' }} ({{ optional($doctor->user)->email ?? '—' }})
                        </option>
                        @foreach($users as $user)
                            @if($user->id !== $doctor->user_id)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('user_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Poliklinik <span class="req">*</span></label>
                    <select name="clinic_id" class="form-control {{ $errors->has('clinic_id') ? 'is-error' : '' }}">
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}" {{ old('clinic_id', $doctor->clinic_id) == $clinic->id ? 'selected' : '' }}>
                                {{ $clinic->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('clinic_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Spesialisasi <span class="req">*</span></label>
                    <input type="text" name="specialization" value="{{ old('specialization', $doctor->specialization) }}"
                           class="form-control {{ $errors->has('specialization') ? 'is-error' : '' }}">
                    @error('specialization')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Lisensi <span class="req">*</span></label>
                    <input type="text" name="licence_number" value="{{ old('licence_number', $doctor->licence_number) }}"
                           class="form-control {{ $errors->has('licence_number') ? 'is-error' : '' }}">
                    @error('licence_number')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Dokter</button>
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>