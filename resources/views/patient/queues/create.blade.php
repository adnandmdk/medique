<x-app-layout title="Booking Antrian">
    <x-slot name="header"><div class="topbar-title">Booking Antrian</div></x-slot>
    <x-slot name="actions"><a href="{{ route('patient.queues.index') }}" class="btn btn-secondary btn-sm">← Kembali</a></x-slot>

    <div class="form-wrap">
        <div class="form-section">
            <div class="form-section-title">Pilih Dokter</div>
            <div class="form-section-sub">Pilih dokter yang ingin Anda kunjungi</div>

            <form action="{{ route('patient.queues.store') }}" method="POST" id="bookingForm">
                @csrf

                {{-- Step 1: Pilih Dokter --}}
                <div class="form-group">
                    <label class="form-label">Dokter <span class="req">*</span></label>
                    <select name="doctor_id"
                            id="doctorSelect"
                            class="form-control {{ $errors->has('doctor_id') ? 'is-error' : '' }}"
                            onchange="loadSchedules(this.value)">
                        <option value="">-- Pilih Dokter --</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}"
                                    data-name="{{ optional($doctor->user)->name }}"
                                    data-spec="{{ $doctor->specialization }}"
                                    data-clinic="{{ optional($doctor->clinic)->name }}"
                                    {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ optional($doctor->user)->name ?? '—' }} —
                                {{ $doctor->specialization }} ({{ optional($doctor->clinic)->name ?? '—' }})
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Doctor Info Card --}}
                <div id="doctorInfo" style="display:none;margin-bottom:16px;background:var(--brand-light);border:1px solid rgba(15,110,86,0.15);border-radius:10px;padding:14px;">
                    <div style="display:flex;gap:12px;align-items:center;">
                        <div id="docAvatar" style="width:44px;height:44px;border-radius:11px;background:var(--brand);color:white;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;flex-shrink:0;"></div>
                        <div>
                            <div id="docName" style="font-size:14px;font-weight:800;color:var(--brand);"></div>
                            <div id="docSpec" style="font-size:12px;color:var(--text2);"></div>
                            <div id="docClinic" style="font-size:12px;color:var(--text2);margin-top:2px;"></div>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Jadwal (auto load) --}}
                <div id="scheduleSection" style="display:none;">
                    <div class="form-group">
                        <label class="form-label">Jadwal Tersedia <span class="req">*</span></label>
                        <input type="hidden" name="schedule_id" id="scheduleIdInput" value="{{ old('schedule_id') }}">
                        <div id="scheduleList" style="display:grid;gap:8px;"></div>
                        @error('schedule_id')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Step 3: Tanggal --}}
                <div id="dateSection" style="display:none;">
                    <div class="form-group">
                        <label class="form-label">Tanggal Kunjungan <span class="req">*</span></label>
                        <input type="date"
                               name="booking_date"
                               id="bookingDate"
                               value="{{ old('booking_date', today()->format('Y-m-d')) }}"
                               min="{{ today()->format('Y-m-d') }}"
                               class="form-control {{ $errors->has('booking_date') ? 'is-error' : '' }}">
                        @error('booking_date')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="alert alert-brand">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                        <span>Nomor antrian otomatis digenerate. Token antrian akan diberikan setelah booking berhasil.</span>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                            Booking Sekarang
                        </button>
                        <a href="{{ route('patient.queues.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
    const scheduleData = @json($doctorSchedules ?? []);
    const dayMap = {
        monday:'Senin', tuesday:'Selasa', wednesday:'Rabu',
        thursday:'Kamis', friday:'Jumat', saturday:'Sabtu', sunday:'Minggu'
    };

    function loadSchedules(doctorId) {
        const infoDiv   = document.getElementById('doctorInfo');
        const schedSec  = document.getElementById('scheduleSection');
        const dateSec   = document.getElementById('dateSection');
        const schedList = document.getElementById('scheduleList');

        if (!doctorId) {
            infoDiv.style.display  = 'none';
            schedSec.style.display = 'none';
            dateSec.style.display  = 'none';
            return;
        }

        // Doctor info
        const sel = document.getElementById('doctorSelect');
        const opt = sel.options[sel.selectedIndex];
        const name   = opt.dataset.name || '';
        const spec   = opt.dataset.spec || '';
        const clinic = opt.dataset.clinic || '';

        document.getElementById('docName').textContent   = name;
        document.getElementById('docSpec').textContent   = spec + ' · ' + clinic;
        document.getElementById('docClinic').textContent = 'Ruangan: ' + clinic;
        document.getElementById('docAvatar').textContent = name.substring(0,2).toUpperCase();
        infoDiv.style.display = 'block';

        // Schedules
        const scheds = scheduleData[doctorId] || [];
        if (scheds.length === 0) {
            schedSec.style.display = 'none';
            dateSec.style.display  = 'none';
            return;
        }

        schedList.innerHTML = '';
        scheds.forEach(s => {
            const div = document.createElement('div');
            div.style.cssText = 'padding:12px 14px;border:2px solid var(--border);border-radius:9px;cursor:pointer;display:flex;align-items:center;gap:12px;transition:all 0.15s;';
            div.innerHTML = `
                <div style="width:8px;height:8px;border-radius:50%;background:var(--brand);flex-shrink:0;"></div>
                <div style="flex:1;">
                    <div style="font-size:13px;font-weight:700;">${dayMap[s.day_of_week] || s.day_of_week}</div>
                    <div style="font-size:12px;color:var(--text2);">${s.start_time.substring(0,5)} — ${s.end_time.substring(0,5)}</div>
                </div>
                <div style="font-size:11px;color:var(--brand);font-weight:700;">Pilih →</div>
            `;
            div.onclick = () => selectSchedule(div, s.id);
            div.dataset.schedId = s.id;
            schedList.appendChild(div);
        });

        schedSec.style.display = 'block';
        dateSec.style.display  = 'none';
        document.getElementById('scheduleIdInput').value = '';
    }

    function selectSchedule(el, schedId) {
        // Reset all
        document.querySelectorAll('#scheduleList > div').forEach(d => {
            d.style.borderColor = 'var(--border)';
            d.style.background  = 'var(--surface)';
        });
        // Highlight selected
        el.style.borderColor = 'var(--brand)';
        el.style.background  = 'var(--brand-light)';
        document.getElementById('scheduleIdInput').value = schedId;
        document.getElementById('dateSection').style.display = 'block';
    }

    // Restore old value on error
    document.addEventListener('DOMContentLoaded', function() {
        const oldDoctor = '{{ old("doctor_id") }}';
        if (oldDoctor) loadSchedules(oldDoctor);
    });
    </script>
</x-app-layout>