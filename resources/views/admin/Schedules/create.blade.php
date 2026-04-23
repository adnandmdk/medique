{{-- resources/views/admin/schedules/create.blade.php --}}
<x-app-layout title="Tambah Jadwal">
    <x-slot name="header"><div class="topbar-title">Tambah Jadwal</div></x-slot>
    <x-slot name="actions"><a href="{{ route('admin.hospitals.schedules.index', $hospital->id) }}" class="btn btn-secondary btn-sm">← Kembali</a></x-slot>
    <div class="form-wrap">
        <div class="form-section">
            <div class="form-section-title">Jadwal Praktek Baru</div>
            <div class="form-section-sub">Tentukan jadwal praktek dokter</div>
            <form action="{{ route('admin.hospitals.schedules.store', $hospital->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Dokter <span class="req">*</span></label>
                    <select name="doctor_id" class="form-control {{ $errors->has('doctor_id') ? 'is-error' : '' }}">
                        <option value="">-- Pilih Dokter --</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ optional($doctor->user)->name ?? '—' }} — {{ optional($doctor->clinic)->name ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Hari <span class="req">*</span></label>
                    <select name="day_of_week" class="form-control {{ $errors->has('day_of_week') ? 'is-error' : '' }}">
                        <option value="">-- Pilih Hari --</option>
                        @foreach($days as $value => $label)
                            <option value="{{ $value }}" {{ old('day_of_week') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('day_of_week')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Jam Mulai <span class="req">*</span></label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}"
                               class="form-control {{ $errors->has('start_time') ? 'is-error' : '' }}">
                        @error('start_time')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jam Selesai <span class="req">*</span></label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}"
                               class="form-control {{ $errors->has('end_time') ? 'is-error' : '' }}">
                        @error('end_time')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                    <a href="{{ route('admin.hospitals.schedules.index', $hospital->id) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>