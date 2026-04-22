<x-app-layout title="Booking Antrian">
    <x-slot name="header"><div class="topbar-title">Booking Antrian</div></x-slot>
    <x-slot name="actions"><a href="{{ route('patient.queues.index') }}" class="btn btn-secondary btn-sm">← Kembali</a></x-slot>

    <div style="max-width:580px;">
        <div class="form-section">
            <div class="form-section-title">Booking Antrian Baru</div>
            <div class="form-section-sub">Ikuti langkah berikut untuk memesan antrian</div>

            <form method="POST" action="{{ route('patient.queues.store') }}" id="bookingForm">
                @csrf

                {{-- STEP 1: Tanggal --}}
                <div class="form-group">
                    <label class="form-label">1. Pilih Tanggal Kunjungan <span class="req">*</span></label>
                    <input type="date"
                           name="booking_date"
                           id="bookingDate"
                           value="{{ old('booking_date', today()->format('Y-m-d')) }}"
                           min="{{ today()->format('Y-m-d') }}"
                           class="form-control {{ $errors->has('booking_date') ? 'is-error' : '' }}"
                           onchange="onDateChange()">
                    @error('booking_date')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- STEP 2: Rumah Sakit --}}
                <div class="form-group" id="step2" style="display:none;">
                    <label class="form-label">2. Pilih Rumah Sakit <span class="req">*</span></label>
                    <input type="hidden" name="hospital_id" id="hospitalIdInput" value="{{ old('hospital_id') }}">
                    <div id="hospitalList" style="display:grid;gap:8px;"></div>
                    <div id="hospitalLoading" style="display:none;text-align:center;padding:16px;color:var(--text3);font-size:13px;">Memuat...</div>
                    @error('hospital_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- STEP 3: Poli --}}
                <div class="form-group" id="step3" style="display:none;">
                    <label class="form-label">3. Pilih Poliklinik <span class="req">*</span></label>
                    <div id="clinicList" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:8px;"></div>
                    <div id="clinicLoading" style="display:none;text-align:center;padding:16px;color:var(--text3);font-size:13px;">Memuat poli...</div>
                </div>

                {{-- STEP 4: Jadwal --}}
                <div class="form-group" id="step4" style="display:none;">
                    <label class="form-label">4. Pilih Jadwal <span class="req">*</span></label>
                    <input type="hidden" name="schedule_id" id="scheduleIdInput" value="{{ old('schedule_id') }}">
                    <div id="scheduleList" style="display:grid;gap:8px;"></div>
                    <div id="scheduleLoading" style="display:none;text-align:center;padding:16px;color:var(--text3);font-size:13px;">Memuat jadwal...</div>
                    <div id="scheduleEmpty" style="display:none;padding:16px;border:1px solid var(--border);border-radius:8px;text-align:center;color:var(--text3);font-size:13px;">Tidak ada jadwal tersedia pada tanggal ini.</div>
                    @error('schedule_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- SUBMIT --}}
                <div id="step5" style="display:none;">
                    <div class="alert alert-brand">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                        <span>Nomor antrian otomatis digenerate dengan format <strong>PU-0001</strong>.</span>
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

    <style>
        .sel-card { padding:12px 14px; border:1.5px solid var(--border); border-radius:9px; cursor:pointer; transition:all .12s; background:var(--surface); }
        .sel-card:hover { border-color:var(--brand); background:var(--brand-light); }
        .sel-card.selected { border-color:var(--brand); background:var(--brand-light); }
        .sel-card-name { font-size:13px; font-weight:700; margin-bottom:2px; }
        .sel-card-sub  { font-size:11px; color:var(--text2); }
        .sched-card { display:flex; align-items:center; gap:12px; }
        .sched-check { width:18px; height:18px; border-radius:50%; border:2px solid var(--border); display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:all .12s; font-size:10px; }
        .sel-card.selected .sched-check { background:var(--brand); border-color:var(--brand); color:white; }
    </style>

    <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let selectedHospital = null, selectedClinic = null, selectedSchedule = null;

    function onDateChange() {
        const date = document.getElementById('bookingDate').value;
        if (!date) return;

        // Reset
        resetFrom(2);

        // Show & load hospitals
        document.getElementById('step2').style.display = 'block';
        loadHospitals();
    }

    async function loadHospitals() {
        const loading = document.getElementById('hospitalLoading');
        const list    = document.getElementById('hospitalList');
        loading.style.display = 'block';
        list.style.display    = 'none';

        try {
            const res  = await fetch('/patient/booking/hospitals');
            const data = await res.json();
            list.innerHTML = '';

            if (!data.length) {
                list.innerHTML = '<div style="padding:16px;text-align:center;color:var(--text3);font-size:13px;">Tidak ada rumah sakit tersedia.</div>';
            } else {
                data.forEach(h => {
                    const el = document.createElement('div');
                    el.className = 'sel-card';
                    el.dataset.id = h.id;
                    el.innerHTML = `
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:36px;height:36px;border-radius:9px;background:var(--brand-light);color:var(--brand);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0;">${h.initials}</div>
                            <div>
                                <div class="sel-card-name">${h.name}</div>
                                <div class="sel-card-sub">${h.address || ''}</div>
                            </div>
                        </div>`;
                    el.onclick = () => selectHospital(el, h.id);
                    list.appendChild(el);
                });
            }

            loading.style.display = 'none';
            list.style.display = 'block';
        } catch(e) {
            loading.innerHTML = '<span style="color:#EF4444;">Gagal memuat. Coba lagi.</span>';
        }
    }

    function selectHospital(el, id) {
        document.querySelectorAll('#hospitalList .sel-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        selectedHospital = id;
        document.getElementById('hospitalIdInput').value = id;
        resetFrom(3);
        loadClinics();
        document.getElementById('step3').style.display = 'block';
    }

    async function loadClinics() {
        const date     = document.getElementById('bookingDate').value;
        const loading  = document.getElementById('clinicLoading');
        const list     = document.getElementById('clinicList');
        loading.style.display = 'block';
        list.style.display    = 'none';

        try {
            const res  = await fetch(`/patient/booking/clinics?hospital_id=${selectedHospital}&date=${date}`, {
                headers: { 'X-CSRF-TOKEN': CSRF }
            });
            const data = await res.json();
            list.innerHTML = '';

            if (!data.length) {
                list.innerHTML = '<div style="grid-column:1/-1;padding:14px;text-align:center;color:var(--text3);font-size:13px;">Tidak ada poli dengan jadwal tersedia.</div>';
            } else {
                data.forEach(c => {
                    const el = document.createElement('div');
                    el.className = 'sel-card';
                    el.dataset.id = c.id;
                    el.innerHTML = `
                        <div style="text-align:center;">
                            <div style="font-size:20px;font-weight:800;color:var(--brand);margin-bottom:4px;">${c.code}</div>
                            <div class="sel-card-name" style="font-size:12px;">${c.name}</div>
                        </div>`;
                    el.onclick = () => selectClinic(el, c.id);
                    list.appendChild(el);
                });
            }

            loading.style.display = 'none';
            list.style.display    = 'grid';
        } catch(e) {}
    }

    function selectClinic(el, id) {
        document.querySelectorAll('#clinicList .sel-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        selectedClinic = id;
        resetFrom(4);
        loadSchedules();
        document.getElementById('step4').style.display = 'block';
    }

    async function loadSchedules() {
        const date    = document.getElementById('bookingDate').value;
        const loading = document.getElementById('scheduleLoading');
        const list    = document.getElementById('scheduleList');
        const empty   = document.getElementById('scheduleEmpty');

        loading.style.display = 'block';
        list.style.display    = 'none';
        empty.style.display   = 'none';

        try {
            const params = new URLSearchParams({
                clinic_id:   selectedClinic,
                hospital_id: selectedHospital,
                date:        date,
            });
            const res  = await fetch(`/patient/booking/schedules?${params}`);
            const data = await res.json();
            list.innerHTML = '';

            if (!data.length) {
                loading.style.display = 'none';
                empty.style.display   = 'block';
                return;
            }

            data.forEach(s => {
                const el = document.createElement('div');
                el.className = 'sel-card';
                el.dataset.id = s.id;
                el.innerHTML = `
                    <div class="sched-card">
                        <div class="sched-check" id="chk-${s.id}">✓</div>
                        <div style="flex:1;">
                            <div class="sel-card-name">${s.doctor}</div>
                            <div class="sel-card-sub">${s.day} · ${s.start_time} — ${s.end_time}</div>
                        </div>
                        <div style="font-size:11px;color:var(--text3);">${s.booked} dipesan</div>
                    </div>`;
                el.onclick = () => selectSchedule(el, s.id);
                list.appendChild(el);
            });

            loading.style.display = 'none';
            list.style.display    = 'block';
        } catch(e) {}
    }

    function selectSchedule(el, id) {
        document.querySelectorAll('#scheduleList .sel-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        selectedSchedule = id;
        document.getElementById('scheduleIdInput').value = id;
        document.getElementById('step5').style.display = 'block';
    }

    function resetFrom(step) {
        if (step <= 3) { selectedClinic=null; document.getElementById('clinicList').innerHTML=''; document.getElementById('step3').style.display='none'; }
        if (step <= 4) { selectedSchedule=null; document.getElementById('scheduleList').innerHTML=''; document.getElementById('scheduleIdInput').value=''; document.getElementById('step4').style.display='none'; document.getElementById('scheduleEmpty').style.display='none'; }
        if (step <= 5) { document.getElementById('step5').style.display='none'; }
        if (step <= 2) { selectedHospital=null; document.getElementById('hospitalList').innerHTML=''; document.getElementById('hospitalIdInput').value=''; }
    }

    // Restore on validation error
    document.addEventListener('DOMContentLoaded', function() {
        const date = document.getElementById('bookingDate').value;
        if (date) onDateChange();
    });
    </script>
</x-app-layout>