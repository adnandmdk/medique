<x-app-layout title="Booking Antrian">
    <x-slot name="header"><div class="topbar-title">Booking Antrian</div></x-slot>
    <x-slot name="actions"><a href="{{ route('patient.queues.index') }}" class="btn btn-secondary btn-sm">← Kembali</a></x-slot>
    <div class="form-wrap">
        <div class="form-section">
            <div class="form-section-title">Form Booking Antrian</div>
            <div class="form-section-sub">Pilih jadwal dan tanggal kunjungan</div>
            <form action="{{ route('patient.queues.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Pilih Dokter & Jadwal <span class="req">*</span></label>
                    <select name="schedule_id" class="form-control {{ $errors->has('schedule_id') ? 'is-error' : '' }}">
                        <option value="">-- Pilih Jadwal --</option>
                        @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}" {{ old('schedule_id') == $schedule->id ? 'selected' : '' }}>
                                {{ optional(optional($schedule->doctor)->user)->name ?? '—' }}
                                ({{ optional(optional($schedule->doctor)->clinic)->name ?? '—' }}) —
                                {{ $schedule->day_label }} {{ $schedule->start_time_label }}–{{ $schedule->end_time_label }}
                            </option>
                        @endforeach
                    </select>
                    @error('schedule_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Kunjungan <span class="req">*</span></label>
                    <input type="date" name="booking_date" value="{{ old('booking_date', today()->format('Y-m-d')) }}"
                           min="{{ today()->format('Y-m-d') }}"
                           class="form-control {{ $errors->has('booking_date') ? 'is-error' : '' }}">
                    @error('booking_date')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="alert alert-brand" style="margin-top:8px;">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                    <span>Nomor antrian otomatis digenerate. Simpan token untuk tracking status.</span>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Booking Sekarang</button>
                    <a href="{{ route('patient.queues.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>