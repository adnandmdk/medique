<x-app-layout title="Booking Antrian">
    <x-slot name="header"><div class="topbar-title">Booking Antrian</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('patient.queues.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </x-slot>

    <div style="max-width:580px;">
        <div class="form-section">
            <div class="form-section-title">Booking Antrian Baru</div>
            <div class="form-section-sub">Ikuti langkah berikut untuk memesan antrian</div>

            <form method="POST" action="{{ route('patient.queues.store') }}" id="bookingForm">
                @csrf

                {{-- STEP 1: Tanggal --}}
                <div class="form-group">
                    <label class="form-label">
                        <span style="background:#0F6E56;color:white;width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;margin-right:6px;">1</span>
                        Pilih Tanggal Kunjungan <span class="req">*</span>
                    </label>
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
                    <label class="form-label">
                        <span style="background:#0F6E56;color:white;width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;margin-right:6px;">2</span>
                        Pilih Rumah Sakit <span class="req">*</span>
                    </label>
                    <input type="hidden" name="hospital_id" id="hospitalIdInput" value="{{ old('hospital_id') }}">
                    <div id="hospitalList" style="display:grid;gap:8px;"></div>
                    <div id="hospitalLoading" style="display:none;text-align:center;padding:16px;color:var(--text3);font-size:13px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite;margin-right:6px;vertical-align:middle;"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>
                        Memuat...
                    </div>
                    @error('hospital_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- STEP 3: Poli --}}
                <div class="form-group" id="step3" style="display:none;">
                    <label class="form-label">
                        <span style="background:#0F6E56;color:white;width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;margin-right:6px;">3</span>
                        Pilih Poliklinik <span class="req">*</span>
                    </label>
                    <div id="clinicList" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:8px;"></div>
                    <div id="clinicLoading" style="display:none;text-align:center;padding:16px;color:var(--text3);font-size:13px;">Memuat poli...</div>
                    <div id="clinicEmpty" style="display:none;padding:14px;border:1px solid var(--border);border-radius:8px;text-align:center;color:var(--text3);font-size:13px;">
                        Tidak ada poliklinik dengan jadwal pada tanggal ini.
                    </div>
                </div>

                {{-- STEP 4: Jadwal --}}
                <div class="form-group" id="step4" style="display:none;">
                    <label class="form-label">
                        <span style="background:#0F6E56;color:white;width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;margin-right:6px;">4</span>
                        Pilih Jadwal <span class="req">*</span>
                    </label>
                    <input type="hidden" name="schedule_id" id="scheduleIdInput" value="{{ old('schedule_id') }}">
                    <div id="scheduleList" style="display:grid;gap:8px;"></div>
                    <div id="scheduleLoading" style="display:none;text-align:center;padding:16px;color:var(--text3);font-size:13px;">Memuat jadwal...</div>
                    <div id="scheduleEmpty" style="display:none;padding:14px;border:1px solid var(--border);border-radius:8px;text-align:center;color:var(--text3);font-size:13px;">
                        Tidak ada jadwal tersedia pada tanggal ini.
                    </div>
                    @error('schedule_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- STEP 5: Submit --}}
                <div id="step5" style="display:none;">
                    <div class="alert alert-brand" style="margin-bottom:14px;">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                        <span>Nomor antrian otomatis digenerate dengan format <strong>KODE-NOMOR</strong> (contoh: <strong>PU-0001</strong>).</span>
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
        @keyframes spin{to{transform:rotate(360deg)}}
        .sel-card{padding:12px 14px;border:1.5px solid var(--border);border-radius:9px;cursor:pointer;transition:all .12s;background:var(--surface);}
        .sel-card:hover{border-color:var(--brand);background:var(--brand-light);}
        .sel-card.selected{border-color:var(--brand);background:var(--brand-light);}
        .sched-check{width:18px;height:18px;border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .12s;font-size:10px;color:transparent;}
        .sel-card.selected .sched-check{background:var(--brand);border-color:var(--brand);color:white;}
    </style>

    <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let selHospital=null, selClinic=null, selSchedule=null;

    function onDateChange(){
        const d=document.getElementById('bookingDate').value;
        if(!d) return;
        resetFrom(2);
        document.getElementById('step2').style.display='block';
        loadHospitals();
    }

    async function loadHospitals(){
        show('hospitalLoading'); hide('hospitalList');
        try{
            const r=await fetch('{{ route('patient.booking.hospitals') }}');
            const data=await r.json();
            const list=document.getElementById('hospitalList');
            list.innerHTML='';
            if(!data.length){
                list.innerHTML='<div style="padding:14px;text-align:center;color:var(--text3);font-size:13px;">Tidak ada rumah sakit tersedia.</div>';
            } else {
                data.forEach(h=>{
                    const el=document.createElement('div');
                    el.className='sel-card';
                    el.dataset.id=h.id;
                    el.innerHTML=`
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:38px;height:38px;border-radius:9px;background:var(--brand-light);color:var(--brand);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0;">${h.initials}</div>
                            <div>
                                <div style="font-size:13px;font-weight:700;">${h.name}</div>
                                <div style="font-size:11px;color:var(--text2);">${h.address||''}</div>
                            </div>
                        </div>`;
                    el.onclick=()=>selectHospital(el,h.id);
                    list.appendChild(el);
                });
            }
            hide('hospitalLoading'); show('hospitalList');
        } catch(e){ document.getElementById('hospitalLoading').innerHTML='<span style="color:#EF4444;">Gagal memuat. Coba lagi.</span>'; }
    }

    function selectHospital(el,id){
        document.querySelectorAll('#hospitalList .sel-card').forEach(c=>c.classList.remove('selected'));
        el.classList.add('selected');
        selHospital=id;
        document.getElementById('hospitalIdInput').value=id;
        resetFrom(3);
        document.getElementById('step3').style.display='block';
        loadClinics();
    }

    async function loadClinics(){
        const date=document.getElementById('bookingDate').value;
        show('clinicLoading'); hide('clinicList'); hide('clinicEmpty');
        try{
            const r=await fetch(`{{ route('patient.booking.clinics') }}?hospital_id=${selHospital}&date=${date}`);
            const data=await r.json();
            const list=document.getElementById('clinicList');
            list.innerHTML='';
            if(!data.length){
                hide('clinicLoading'); show('clinicEmpty'); return;
            }
            data.forEach(c=>{
                const el=document.createElement('div');
                el.className='sel-card';
                el.dataset.id=c.id;
                el.style.textAlign='center';
                el.innerHTML=`
                    <div style="font-size:22px;font-weight:800;color:var(--brand);margin-bottom:4px;">${c.code}</div>
                    <div style="font-size:12px;font-weight:700;">${c.name}</div>`;
                el.onclick=()=>selectClinic(el,c.id);
                list.appendChild(el);
            });
            hide('clinicLoading'); show('clinicList','grid');
        } catch(e){}
    }

    function selectClinic(el,id){
        document.querySelectorAll('#clinicList .sel-card').forEach(c=>c.classList.remove('selected'));
        el.classList.add('selected');
        selClinic=id;
        resetFrom(4);
        document.getElementById('step4').style.display='block';
        loadSchedules();
    }

    async function loadSchedules(){
        const date=document.getElementById('bookingDate').value;
        show('scheduleLoading'); hide('scheduleList'); hide('scheduleEmpty');
        try{
            const p=new URLSearchParams({clinic_id:selClinic,hospital_id:selHospital,date});
            const r=await fetch(`{{ route('patient.booking.schedules') }}?${p}`);
            const data=await r.json();
            const list=document.getElementById('scheduleList');
            list.innerHTML='';
            if(!data.length){
                hide('scheduleLoading'); show('scheduleEmpty'); return;
            }
            data.forEach(s=>{
                const el=document.createElement('div');
                el.className='sel-card';
                el.dataset.id=s.id;
                el.innerHTML=`
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="sched-check" id="chk-${s.id}">✓</div>
                        <div style="flex:1;">
                            <div style="font-size:13px;font-weight:700;">${s.doctor}</div>
                            <div style="font-size:11px;color:var(--text2);">${s.day} · ${s.start_time} — ${s.end_time}</div>
                        </div>
                        <div style="font-size:11px;color:var(--text3);white-space:nowrap;">${s.booked} dipesan</div>
                    </div>`;
                el.onclick=()=>selectSchedule(el,s.id);
                list.appendChild(el);
            });
            hide('scheduleLoading'); show('scheduleList');
        } catch(e){}
    }

    function selectSchedule(el,id){
        document.querySelectorAll('#scheduleList .sel-card').forEach(c=>c.classList.remove('selected'));
        el.classList.add('selected');
        selSchedule=id;
        document.getElementById('scheduleIdInput').value=id;
        document.getElementById('step5').style.display='block';
    }

    function resetFrom(step){
        if(step<=3){selClinic=null;document.getElementById('clinicList').innerHTML='';document.getElementById('step3').style.display='none';hide('clinicEmpty');}
        if(step<=4){selSchedule=null;document.getElementById('scheduleList').innerHTML='';document.getElementById('scheduleIdInput').value='';document.getElementById('step4').style.display='none';hide('scheduleEmpty');}
        if(step<=5){document.getElementById('step5').style.display='none';}
        if(step<=2){selHospital=null;document.getElementById('hospitalList').innerHTML='';document.getElementById('hospitalIdInput').value='';}
    }

    function show(id,display='block'){const el=document.getElementById(id);if(el)el.style.display=display;}
    function hide(id){const el=document.getElementById(id);if(el)el.style.display='none';}

    // Restore jika ada error validasi
    document.addEventListener('DOMContentLoaded',function(){
        const d=document.getElementById('bookingDate').value;
        if(d) onDateChange();
    });
    </script>
</x-app-layout>