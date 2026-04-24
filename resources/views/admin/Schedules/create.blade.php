<x-app-layout title="Tambah Jadwal">
    <x-slot name="header">
        <div>
            <div style="font-size:11px;color:var(--text2);margin-bottom:2px;">
                <a href="{{ route('admin.hospitals.index') }}" style="color:var(--brand);text-decoration:none;">Rumah Sakit</a>
                <span style="margin:0 4px;">›</span>
                <a href="{{ route('admin.hospitals.show', $hospital) }}" style="color:var(--brand);text-decoration:none;">{{ $hospital->name }}</a>
                <span style="margin:0 4px;">›</span>
                <a href="{{ route('admin.hospitals.schedules.index', $hospital) }}" style="color:var(--brand);text-decoration:none;">Jadwal</a>
                <span style="margin:0 4px;">›</span>
                Tambah
            </div>
            <div class="topbar-title">Tambah Jadwal</div>
        </div>
    </x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.hospitals.schedules.index', $hospital) }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </x-slot>

    <div class="form-wrap">
        <div class="form-section">
            <div class="form-section-title">Jadwal Praktek Baru</div>
            <div class="form-section-sub">Tentukan jadwal praktek dokter di {{ $hospital->name }}</div>

            <form action="{{ route('admin.hospitals.schedules.store', $hospital) }}" method="POST">
                @csrf

                {{-- Dokter --}}
                <div class="form-group">
                    <label class="form-label">Dokter <span class="req">*</span></label>
                    <select name="doctor_id" id="doctorSelect"
                            class="form-control {{ $errors->has('doctor_id') ? 'is-error' : '' }}"
                            onchange="showDoctorInfo(this)">
                        <option value="">-- Pilih Dokter --</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}"
                                    data-name="{{ optional($doctor->user)->name }}"
                                    data-spec="{{ $doctor->specialization }}"
                                    data-clinic="{{ optional($doctor->clinic)->name }}"
                                    {{ (old('doctor_id') == $doctor->id || request('doctor_id') == $doctor->id) ? 'selected' : '' }}>
                                {{ optional($doctor->user)->name ?? '—' }} — {{ $doctor->specialization }} ({{ optional($doctor->clinic)->name ?? '—' }})
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Doctor Info Card --}}
                <div id="doctorInfo" style="display:none;margin-bottom:16px;background:var(--brand-light);border:1px solid rgba(15,110,86,.15);border-radius:10px;padding:12px 14px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div id="docAvatar" style="width:36px;height:36px;border-radius:9px;background:var(--brand);color:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0;"></div>
                        <div>
                            <div id="docName" style="font-size:13px;font-weight:700;color:var(--brand);"></div>
                            <div id="docDetail" style="font-size:11px;color:var(--text2);margin-top:1px;"></div>
                        </div>
                    </div>
                </div>

                {{-- Hari --}}
                <div class="form-group">
                    <label class="form-label">Hari <span class="req">*</span></label>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px;">
                        @php
                            $dayList = [
                                'monday'    => ['Senin',   '#D1FAE5', '#065F46'],
                                'tuesday'   => ['Selasa',  '#DBEAFE', '#1E40AF'],
                                'wednesday' => ['Rabu',    '#F5F3FF', '#5B21B6'],
                                'thursday'  => ['Kamis',   '#FEF3C7', '#92400E'],
                                'friday'    => ['Jumat',   '#ECFDF5', '#0F6E56'],
                                'saturday'  => ['Sabtu',   '#FEE2E2', '#991B1B'],
                                'sunday'    => ['Minggu',  '#F1F5F9', '#475569'],
                            ];
                        @endphp
                        @foreach($dayList as $val => [$label, $bg, $fg])
                            @php $isSelected = old('day_of_week') === $val || request('day_of_week') === $val; @endphp
                            <label id="dayLabel-{{ $val }}"
                                   style="display:flex;flex-direction:column;align-items:center;gap:4px;padding:10px 8px;border:2px solid {{ $isSelected ? 'var(--brand)' : 'var(--border)' }};border-radius:9px;cursor:pointer;background:{{ $isSelected ? 'var(--brand-light)' : 'white' }};transition:all .12s;"
                                   onclick="selectDay('{{ $val }}')">
                                <input type="radio" name="day_of_week" value="{{ $val }}"
                                       {{ $isSelected ? 'checked' : '' }}
                                       style="display:none;">
                                <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:5px;background:{{ $bg }};color:{{ $fg }};">
                                    {{ substr($label,0,3) }}
                                </span>
                                <span style="font-size:11px;font-weight:600;color:var(--text);">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('day_of_week')<div class="form-error" style="margin-top:6px;">{{ $message }}</div>@enderror
                </div>

                {{-- Jam --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Jam Mulai <span class="req">*</span></label>
                        <input type="time" name="start_time"
                               value="{{ old('start_time') }}"
                               class="form-control {{ $errors->has('start_time') ? 'is-error' : '' }}">
                        @error('start_time')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jam Selesai <span class="req">*</span></label>
                        <input type="time" name="end_time"
                               value="{{ old('end_time') }}"
                               class="form-control {{ $errors->has('end_time') ? 'is-error' : '' }}">
                        @error('end_time')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Simpan Jadwal
                    </button>
                    <a href="{{ route('admin.hospitals.schedules.index', $hospital) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    function showDoctorInfo(sel) {
        const opt  = sel.options[sel.selectedIndex];
        const info = document.getElementById('doctorInfo');
        if (!opt.value) { info.style.display = 'none'; return; }
        document.getElementById('docName').textContent   = opt.dataset.name || '';
        document.getElementById('docDetail').textContent = (opt.dataset.spec || '') + ' · ' + (opt.dataset.clinic || '');
        document.getElementById('docAvatar').textContent = (opt.dataset.name || 'NA').substring(0,2).toUpperCase();
        info.style.display = 'block';
    }

    function selectDay(val) {
        document.querySelectorAll('[id^="dayLabel-"]').forEach(el => {
            el.style.borderColor = 'var(--border)';
            el.style.background  = 'white';
        });
        const lbl = document.getElementById('dayLabel-' + val);
        if (lbl) {
            lbl.style.borderColor = 'var(--brand)';
            lbl.style.background  = 'var(--brand-light)';
            lbl.querySelector('input[type="radio"]').checked = true;
        }
    }

    // Restore on init
    document.addEventListener('DOMContentLoaded', function () {
        const sel = document.getElementById('doctorSelect');
        if (sel.value) showDoctorInfo(sel);
    });
    </script>
</x-app-layout>