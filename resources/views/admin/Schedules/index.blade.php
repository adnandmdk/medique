<x-app-layout title="Jadwal Praktek">
    <x-slot name="header"><div class="topbar-title">Jadwal Praktek</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Jadwal
        </a>
    </x-slot>

    @php
        // Group schedules by doctor
        $grouped = $schedules->groupBy('doctor_id');
        $allDays = ['monday'=>'Sen','tuesday'=>'Sel','wednesday'=>'Rab','thursday'=>'Kam','friday'=>'Jum'];
        $dayColors = ['monday'=>'day-senin','tuesday'=>'day-selasa','wednesday'=>'day-rabu','thursday'=>'day-kamis','friday'=>'day-jumat'];

        // Today's day name
        $todayDay = strtolower(now()->format('l'));

        // Attendance today (from doctor_attendances table)
        $attendances = \App\Models\DoctorAttendance::where('date', today()->toDateString())->pluck('is_present','doctor_id');
    @endphp

    <div style="margin-bottom:20px;">
        @forelse($grouped as $doctorId => $doctorSchedules)
            @php
                $doctor     = optional($doctorSchedules->first()->doctor);
                $user       = optional($doctor->user);
                $clinic     = optional($doctor->clinic);
                $isPresent  = $attendances->get($doctorId, null);
                $uniqueId   = 'sched-' . $doctorId;

                // Map day → schedule
                $dayMap = $doctorSchedules->keyBy('day_of_week');
            @endphp

            <div class="schedule-doctor-row">
                {{-- HEAD --}}
                <div class="schedule-doctor-head" onclick="toggleSchedule('{{ $uniqueId }}')" id="head-{{ $uniqueId }}">
                    {{-- Chevron --}}
                    <svg class="schedule-chevron" id="chev-{{ $uniqueId }}" viewBox="0 0 24 24">
                        <polyline points="6,9 12,15 18,9"/>
                    </svg>

                    {{-- Avatar --}}
                    <div class="avatar" style="background:var(--brand-light);color:var(--brand);">
                        {{ strtoupper(substr($user->name ?? 'NA', 0, 2)) }}
                    </div>

                    {{-- Info --}}
                    <div style="flex:1;min-width:0;">
                        <div class="avatar-name">{{ $user->name ?? '—' }}</div>
                        <div class="avatar-sub">{{ $doctor->specialization ?? '—' }} · {{ $clinic->name ?? '—' }}</div>
                    </div>

                    {{-- Status Hadir/Libur Hari Ini --}}
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">
                        @if($dayMap->has($todayDay))
                            @if($isPresent === true)
                                <span class="badge badge-hadir">Hadir Hari Ini</span>
                            @elseif($isPresent === false)
                                <span class="badge badge-libur">Libur Hari Ini</span>
                            @else
                                <span class="badge badge-inactive">Belum Konfirmasi</span>
                            @endif
                        @else
                            <span class="badge badge-inactive">Tidak Jadwal Hari Ini</span>
                        @endif

                        {{-- Day Pills Sen-Jum --}}
                        <div class="schedule-day-slots">
                            @foreach($allDays as $dayKey => $dayShort)
                                @if($dayMap->has($dayKey))
                                    <div class="day-slot">
                                        <span class="day-slot-label">{{ $dayShort }}</span>
                                        <span class="day-slot-pill dsp-active">✓</span>
                                    </div>
                                @else
                                    <div class="day-slot">
                                        <span class="day-slot-label">{{ $dayShort }}</span>
                                        <span class="day-slot-pill dsp-none">—</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- DETAIL ROWS --}}
                <div class="schedule-details" id="{{ $uniqueId }}">
                    <div style="padding:8px 16px;background:var(--surface2);border-bottom:1px solid var(--border);">
                        <span style="font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.5px;">Detail Jadwal Praktek</span>
                    </div>
                    @foreach($doctorSchedules as $schedule)
                        @php $dc = $dayColors[$schedule->day_of_week] ?? ''; @endphp
                        <div class="schedule-detail-row">
                            <span class="badge {{ $dc }}" style="min-width:60px;justify-content:center;">
                                {{ $schedule->day_label }}
                            </span>
                            <div style="flex:1;">
                                <span style="font-size:13px;font-weight:700;">{{ $schedule->start_time_label }}</span>
                                <span style="color:var(--text3);margin:0 6px;">—</span>
                                <span style="font-size:13px;font-weight:700;">{{ $schedule->end_time_label }}</span>
                            </div>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-secondary btn-xs">Edit</a>
                                <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="card">
                <div class="empty-state">
                    <div class="empty-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                    <div class="empty-title">Belum ada jadwal</div>
                    <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary btn-sm">+ Tambah Jadwal</a>
                </div>
            </div>
        @endforelse
    </div>

    <script>
    function toggleSchedule(id) {
        const detail = document.getElementById(id);
        const head   = document.getElementById('head-' + id);
        const chev   = document.getElementById('chev-' + id);
        const isOpen = detail.classList.contains('open');
        detail.classList.toggle('open', !isOpen);
        head.classList.toggle('open', !isOpen);
        chev.classList.toggle('open', !isOpen);
    }
    </script>
</x-app-layout>