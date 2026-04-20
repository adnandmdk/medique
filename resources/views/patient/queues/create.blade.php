<x-app-layout title="Booking Antrian">
    <x-slot name="header"><div class="topbar-title">Booking Antrian</div></x-slot>
    <x-slot name="actions"><a href="{{ route('patient.queues.index') }}" class="btn btn-secondary btn-sm">← Kembali</a></x-slot>

    <div class="form-wrap">
        <div class="form-section">
            <div class="form-section-title">Pilih Jadwal Dokter</div>
            <div class="form-section-sub">Pilih slot jadwal yang tersedia</div>

            <form action="{{ route('patient.queues.store') }}" method="POST">
                @csrf

                {{-- Schedule Selection --}}
                <div class="form-group">
                    <label class="form-label">Jadwal Tersedia <span class="req">*</span></label>
                    <input type="hidden" name="schedule_id" id="selectedScheduleId" value="{{ old('schedule_id') }}">
                    @error('schedule_id')<div class="form-error">{{ $message }}</div>@enderror

                    @if($schedules->isEmpty())
                        <div style="padding:20px;border:1px solid var(--border);border-radius:9px;text-align:center;color:var(--text3);">
                            Tidak ada jadwal tersedia saat ini.
                        </div>
                    @else
                        {{-- Group by clinic --}}
                        @foreach($schedules as $doctorId => $doctorScheds)
                            @php
                                $firstSched = $doctorScheds->first();
                                $doctor     = optional($firstSched->doctor);
                                $user       = optional($doctor->user);
                                $clinic     = optional($doctor->clinic);
                                $dayMap     = ['monday'=>'Senin','tuesday'=>'Selasa','wednesday'=>'Rabu','thursday'=>'Kamis','friday'=>'Jumat','saturday'=>'Sabtu','sunday'=>'Minggu'];
                                $dayColors  = ['monday'=>'#D1FAE5:#065F46','tuesday'=>'#DBEAFE:#1E40AF','wednesday'=>'#EDE9FE:#5B21B6','thursday'=>'#FEF3C7:#92400E','friday'=>'#E1F5EE:#0F6E56','saturday'=>'#FEE2E2:#991B1B','sunday'=>'#F0F0F0:#6B7280'];
                            @endphp
                            <div style="border:1.5px solid var(--border);border-radius:12px;margin-bottom:10px;overflow:hidden;">
                                {{-- Doctor Header --}}
                                <div style="padding:12px 16px;background:var(--surface2);display:flex;align-items:center;gap:12px;">
                                    <div style="width:36px;height:36px;border-radius:9px;background:var(--brand-light);color:var(--brand);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0;">
                                        {{ strtoupper(substr($user->name??'NA',0,2)) }}
                                    </div>
                                    <div>
                                        <div style="font-size:13px;font-weight:700;color:var(--text);">{{ $user->name ?? '—' }}</div>
                                        <div style="font-size:11px;color:var(--text2);">{{ $doctor->specialization ?? '—' }} · {{ $clinic->name ?? '—' }}</div>
                                    </div>
                                </div>

                                {{-- Schedule Slots --}}
                                @foreach($doctorScheds as $sched)
                                    @php [$bg,$fg] = explode(':', $dayColors[$sched->day_of_week] ?? '#F0F0F0:#6B7280'); @endphp
                                    <div class="sched-slot {{ old('schedule_id') == $sched->id ? 'selected' : '' }}"
                                         id="slot-{{ $sched->id }}"
                                         onclick="selectSlot('{{ $sched->id }}', this)"
                                         style="padding:12px 16px;border-top:1px solid var(--border);display:flex;align-items:center;gap:12px;cursor:pointer;transition:all 0.15s;">
                                        <span style="padding:4px 10px;border-radius:7px;font-size:11px;font-weight:700;background:{{ $bg }};color:{{ $fg }};min-width:56px;text-align:center;">
                                            {{ $dayMap[$sched->day_of_week] ?? $sched->day_of_week }}
                                        </span>
                                        <div style="flex:1;">
                                            <span style="font-size:13px;font-weight:700;color:var(--text);">{{ substr($sched->start_time,0,5) }} — {{ substr($sched->end_time,0,5) }}</span>
                                        </div>
                                        <div class="slot-check" style="width:20px;height:20px;border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all 0.15s;">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Tanggal --}}
                <div class="form-group" id="dateSection" style="{{ old('schedule_id') ? '' : 'display:none;' }}">
                    <label class="form-label">Tanggal Kunjungan <span class="req">*</span></label>
                    <input type="date"
                           name="booking_date"
                           id="bookingDate"
                           value="{{ old('booking_date', today()->format('Y-m-d')) }}"
                           min="{{ today()->format('Y-m-d') }}"
                           class="form-control {{ $errors->has('booking_date') ? 'is-error' : '' }}">
                    @error('booking_date')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div id="submitSection" style="{{ old('schedule_id') ? '' : 'display:none;' }}">
                    <div class="alert alert-brand" style="margin-bottom:16px;">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                        <span>Nomor antrian otomatis digenerate dengan format <strong>KODE-NOMOR</strong> (contoh: PU-0001).</span>
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
        .sched-slot:hover { background: var(--surface2); }
        .sched-slot.selected { background: var(--brand-light); }
        .sched-slot.selected .slot-check { background: var(--brand); border-color: var(--brand); }
        .sched-slot.selected .slot-check::after { content: '✓'; font-size: 11px; color: white; font-weight: 800; }
    </style>
    <script>
    function selectSlot(id, el) {
        document.querySelectorAll('.sched-slot').forEach(s => s.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('selectedScheduleId').value = id;
        document.getElementById('dateSection').style.display = 'block';
        document.getElementById('submitSection').style.display = 'block';
    }
    </script>
</x-app-layout>