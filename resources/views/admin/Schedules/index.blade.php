<x-app-layout title="Jadwal Praktek">
    <x-slot name="header">
        <div>
            <div style="font-size:11px;color:var(--text2);margin-bottom:2px;">
                <a href="{{ route('admin.hospitals.index') }}" style="color:var(--brand);text-decoration:none;">Rumah Sakit</a>
                <span style="margin:0 4px;">›</span>
                <a href="{{ route('admin.hospitals.show', $hospital) }}" style="color:var(--brand);text-decoration:none;">{{ $hospital->name }}</a>
                <span style="margin:0 4px;">›</span>
                Jadwal
            </div>
            <div class="topbar-title">Jadwal Praktek</div>
        </div>
    </x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.hospitals.show', $hospital) }}" class="btn btn-secondary btn-sm">← Kembali</a>
        <a href="{{ route('admin.hospitals.schedules.create', $hospital) }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Jadwal
        </a>
    </x-slot>

    @php
        // Group jadwal berdasarkan dokter
        $grouped = $schedules->getCollection()->groupBy('doctor_id');

        $dayColors = [
            'monday'    => ['background:#D1FAE5;color:#065F46;', 'Senin'],
            'tuesday'   => ['background:#DBEAFE;color:#1E40AF;', 'Selasa'],
            'wednesday' => ['background:#F5F3FF;color:#5B21B6;', 'Rabu'],
            'thursday'  => ['background:#FEF3C7;color:#92400E;', 'Kamis'],
            'friday'    => ['background:#ECFDF5;color:#0F6E56;', 'Jumat'],
            'saturday'  => ['background:#FEE2E2;color:#991B1B;', 'Sabtu'],
            'sunday'    => ['background:#F1F5F9;color:#475569;', 'Minggu'],
        ];

        $allDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        $todayDayName = strtolower(now()->format('l'));

        $attendances = \App\Models\DoctorAttendance::where('date', today()->toDateString())
            ->pluck('is_present', 'doctor_id');
    @endphp

    {{-- SUMMARY STATS --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
        <div style="background:white;border-radius:12px;border:1px solid var(--border);padding:16px;">
            <div style="font-size:11px;font-weight:600;color:var(--text2);margin-bottom:4px;">Total Jadwal</div>
            <div style="font-size:26px;font-weight:800;color:var(--text);">{{ $schedules->total() }}</div>
        </div>
        <div style="background:white;border-radius:12px;border:1px solid var(--border);padding:16px;">
            <div style="font-size:11px;font-weight:600;color:var(--text2);margin-bottom:4px;">Total Dokter</div>
            <div style="font-size:26px;font-weight:800;color:var(--text);">{{ $grouped->count() }}</div>
        </div>
        <div style="background:#ECFDF5;border-radius:12px;border:1px solid #A7F3D0;padding:16px;">
            <div style="font-size:11px;font-weight:600;color:#059669;margin-bottom:4px;">Hadir Hari Ini</div>
            <div style="font-size:26px;font-weight:800;color:#0F6E56;">{{ $attendances->filter(fn($v) => $v === true)->count() }}</div>
        </div>
        <div style="background:#FEF2F2;border-radius:12px;border:1px solid #FECACA;padding:16px;">
            <div style="font-size:11px;font-weight:600;color:#DC2626;margin-bottom:4px;">Libur Hari Ini</div>
            <div style="font-size:26px;font-weight:800;color:#991B1B;">{{ $attendances->filter(fn($v) => $v === false)->count() }}</div>
        </div>
    </div>

    {{-- JADWAL PER DOKTER --}}
    @if($grouped->isEmpty())
        <div style="background:white;border-radius:var(--radius);border:1px solid var(--border);padding:48px;text-align:center;">
            <div style="width:52px;height:52px;border-radius:14px;background:var(--surface2);margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="var(--text3)" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div style="font-size:14px;font-weight:700;margin-bottom:4px;">Belum ada jadwal</div>
            <div style="font-size:12px;color:var(--text2);margin-bottom:14px;">Tambahkan jadwal praktek dokter</div>
            <a href="{{ route('admin.hospitals.schedules.create', $hospital) }}" class="btn btn-primary btn-sm">+ Tambah Jadwal</a>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach($grouped as $doctorId => $doctorSchedules)
                @php
                    $firstSched  = $doctorSchedules->first();
                    $doctor      = optional($firstSched->doctor);
                    $doctorUser  = optional($doctor->user);
                    $clinic      = optional($doctor->clinic);
                    $isPresent   = $attendances->get($doctorId, null);
                    $dayMap      = $doctorSchedules->keyBy('day_of_week');
                    $panelId     = 'panel-' . $doctorId;

                    $avatarColors = [
                        ['#DBEAFE','#1E40AF'], ['#D1FAE5','#065F46'],
                        ['#F5F3FF','#5B21B6'], ['#FEF3C7','#92400E'],
                        ['#FEE2E2','#991B1B'],
                    ];
                    [$avBg, $avFg] = $avatarColors[$loop->index % 5];
                @endphp

                <div style="background:white;border-radius:12px;border:1px solid var(--border);overflow:hidden;">

                    {{-- ─── HEADER ROW ─── --}}
                    <div style="padding:14px 18px;display:flex;align-items:center;gap:14px;cursor:pointer;transition:background .12s;flex-wrap:wrap;"
                         onclick="togglePanel('{{ $panelId }}')"
                         id="head-{{ $panelId }}"
                         onmouseover="this.style.background='#FAFBFE'"
                         onmouseout="this.style.background='white'">

                        {{-- Chevron --}}
                        <svg id="chev-{{ $panelId }}"
                             viewBox="0 0 24 24" width="16" height="16"
                             fill="none" stroke="var(--text3)" stroke-width="2"
                             style="flex-shrink:0;transition:transform .2s;transform:rotate(0deg);">
                            <polyline points="6,9 12,15 18,9"/>
                        </svg>

                        {{-- Avatar --}}
                        <div style="width:38px;height:38px;border-radius:10px;background:{{ $avBg }};color:{{ $avFg }};display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0;">
                            {{ strtoupper(substr($doctorUser->name ?? 'NA', 0, 2)) }}
                        </div>

                        {{-- Info --}}
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:13px;font-weight:700;color:var(--text);">
                                {{ $doctorUser->name ?? '—' }}
                            </div>
                            <div style="font-size:11px;color:var(--text2);margin-top:1px;">
                                {{ $doctor->specialization ?? '—' }}
                                @if($clinic->name)
                                    <span style="color:var(--text3);margin:0 4px;">·</span>
                                    {{ $clinic->name }}
                                @endif
                            </div>
                        </div>

                        {{-- Hari Pills Sen–Jum --}}
                        <div style="display:flex;gap:5px;align-items:center;flex-wrap:wrap;">
                            @foreach($allDays as $dayKey)
                                @php [$dayStyle, $dayLabel] = $dayColors[$dayKey]; @endphp
                                @if($dayMap->has($dayKey))
                                    <span style="display:inline-flex;flex-direction:column;align-items:center;gap:2px;">
                                        <span style="font-size:9px;font-weight:600;color:var(--text3);text-transform:uppercase;">{{ substr($dayLabel,0,3) }}</span>
                                        <span style="padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;{{ $dayStyle }}">✓</span>
                                    </span>
                                @else
                                    <span style="display:inline-flex;flex-direction:column;align-items:center;gap:2px;">
                                        <span style="font-size:9px;font-weight:600;color:var(--text3);text-transform:uppercase;">{{ substr($dayLabel,0,3) }}</span>
                                        <span style="padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;background:#F1F5F9;color:#CBD5E1;">—</span>
                                    </span>
                                @endif
                            @endforeach
                        </div>

                        {{-- Status Hadir / Libur --}}
                        <div style="flex-shrink:0;">
                            @if($dayMap->has($todayDayName))
                                @if($isPresent === true)
                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#ECFDF5;color:#065F46;">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#059669;"></span>
                                        Aktif
                                    </span>
                                @elseif($isPresent === false)
                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#FEF2F2;color:#991B1B;">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#DC2626;"></span>
                                        Libur
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#F1F5F9;color:#64748B;">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#94A3B8;"></span>
                                        Belum Konfirmasi
                                    </span>
                                @endif
                            @else
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#F1F5F9;color:#94A3B8;">
                                    Tidak ada jadwal hari ini
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- ─── DETAIL PANEL (collapsible) ─── --}}
                    <div id="{{ $panelId }}" style="display:none;border-top:1px solid var(--border);">

                        {{-- Sub-header --}}
                        <div style="padding:8px 18px;background:var(--surface2);">
                            <span style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.5px;">
                                Detail Jadwal · {{ $doctorSchedules->count() }} jadwal
                            </span>
                        </div>

                        {{-- Jadwal rows --}}
                        @foreach($doctorSchedules->sortBy(fn($s) => array_search($s->day_of_week, array_keys($dayColors))) as $schedule)
                            @php [$schedStyle, $schedLabel] = $dayColors[$schedule->day_of_week]; @endphp
                            <div style="display:flex;align-items:center;gap:14px;padding:12px 18px;border-bottom:1px solid #F8FAFC;">

                                {{-- Hari badge --}}
                                <span style="display:inline-block;min-width:64px;padding:4px 10px;border-radius:7px;font-size:11px;font-weight:700;text-align:center;{{ $schedStyle }}">
                                    {{ $schedLabel }}
                                </span>

                                {{-- Jam --}}
                                <div style="display:flex;align-items:center;gap:6px;flex:1;">
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="var(--text3)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                                    <span style="font-size:13px;font-weight:700;color:var(--text);">{{ $schedule->start_time_label }}</span>
                                    <span style="color:var(--text3);font-size:12px;">—</span>
                                    <span style="font-size:13px;font-weight:700;color:var(--text);">{{ $schedule->end_time_label }}</span>
                                </div>

                                {{-- Jumlah antrian hari ini jika hari ini --}}
                                @if($schedule->day_of_week === $todayDayName)
                                    @php
                                        $todayCount = \App\Models\Queue::where('schedule_id', $schedule->id)
                                            ->where('booking_date', today())
                                            ->whereNotIn('status', ['cancelled'])
                                            ->count();
                                    @endphp
                                    <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:10px;background:#FFFBEB;color:#92400E;">
                                        {{ $todayCount }} antrian hari ini
                                    </span>
                                @endif

                                {{-- Actions --}}
                                <div style="display:flex;gap:6px;flex-shrink:0;">
                                    <a href="{{ route('admin.hospitals.schedules.edit', [$hospital, $schedule]) }}"
                                       class="btn btn-secondary btn-xs">
                                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.hospitals.schedules.destroy', [$hospital, $schedule]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus jadwal {{ $schedLabel }} untuk {{ $doctorUser->name ?? 'dokter ini' }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs">
                                            <svg viewBox="0 0 24 24"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,01-2,2H8a2,2,0,01-2-2L5,6"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach

                        {{-- Tambah jadwal untuk dokter ini --}}
                        <div style="padding:10px 18px;background:#FAFBFE;">
                            <a href="{{ route('admin.hospitals.schedules.create', $hospital) }}?doctor_id={{ $doctorId }}"
                               style="font-size:12px;font-weight:600;color:var(--brand);text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Tambah Jadwal untuk Dokter Ini
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($schedules->hasPages())
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0;flex-wrap:wrap;gap:8px;margin-top:12px;">
                <span style="font-size:12px;color:var(--text2);">{{ $schedules->firstItem() }}–{{ $schedules->lastItem() }} dari {{ $schedules->total() }} jadwal</span>
                <div style="display:flex;gap:3px;">
                    <a href="{{ $schedules->previousPageUrl() ?? '#' }}"
                       style="width:30px;height:30px;border-radius:7px;border:1px solid var(--border);background:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;text-decoration:none;color:var(--text2);{{ $schedules->onFirstPage() ? 'opacity:.35;pointer-events:none;' : '' }}">‹</a>
                    @foreach($schedules->getUrlRange(1, $schedules->lastPage()) as $page => $url)
                        <a href="{{ $url }}"
                           style="width:30px;height:30px;border-radius:7px;border:1px solid {{ $page === $schedules->currentPage() ? 'var(--brand)' : 'var(--border)' }};background:{{ $page === $schedules->currentPage() ? 'var(--brand)' : 'white' }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;text-decoration:none;color:{{ $page === $schedules->currentPage() ? 'white' : 'var(--text2)' }};">{{ $page }}</a>
                    @endforeach
                    <a href="{{ $schedules->nextPageUrl() ?? '#' }}"
                       style="width:30px;height:30px;border-radius:7px;border:1px solid var(--border);background:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;text-decoration:none;color:var(--text2);{{ !$schedules->hasMorePages() ? 'opacity:.35;pointer-events:none;' : '' }}">›</a>
                </div>
            </div>
        @endif
    @endif

    <script>
    function togglePanel(id) {
        const panel = document.getElementById(id);
        const chev  = document.getElementById('chev-' + id);
        const isOpen = panel.style.display !== 'none';

        panel.style.display = isOpen ? 'none' : 'block';
        chev.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    // Auto-buka panel pertama jika hanya ada 1 dokter
    document.addEventListener('DOMContentLoaded', function () {
        const panels = document.querySelectorAll('[id^="panel-"]');
        if (panels.length === 1) togglePanel(panels[0].id);
    });
    </script>
</x-app-layout>