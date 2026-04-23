<x-app-layout title="Dashboard Dokter">
    <x-slot name="header"><div class="topbar-title">Dashboard</div></x-slot>
    <x-slot name="actions">
        @if($stats)
            @php
                $att = \App\Models\DoctorAttendance::where('doctor_id', auth()->user()->doctor?->id)
                    ->where('date', today()->toDateString())->first();
                $isPresent = $att?->is_present;
            @endphp
            <form method="POST" action="{{ route('doctor.attendance.toggle') }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-sm {{ $isPresent ? 'btn-danger' : 'btn-primary' }}">
                    {{ $isPresent === true ? '🔴 Tandai Libur' : ($isPresent === false ? '🟢 Tandai Hadir' : '🟢 Konfirmasi Hadir') }}
                </button>
            </form>
        @endif
    </x-slot>

    @if(! $stats)
        <div class="alert alert-warning">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
            <span>Akun Anda belum terdaftar sebagai dokter. Hubungi Admin.</span>
        </div>
    @else

    {{-- Profile Card --}}
    <div style="background:white;border:1px solid var(--border);border-radius:var(--radius);padding:18px;display:flex;align-items:center;gap:14px;margin-bottom:16px;flex-wrap:wrap;">
        <div class="avatar avatar-lg" style="background:var(--brand-light);color:var(--brand);">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:16px;font-weight:800;">{{ auth()->user()->name }}</div>
            <div style="font-size:12px;color:var(--text2);">
                {{ optional($stats['clinic'])->name ?? '—' }} ·
                {{ optional(auth()->user()->doctor)->specialization ?? '—' }}
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
            @if($isPresent === true)
                <span class="badge badge-hadir" style="font-size:12px;padding:5px 14px;">Hadir Hari Ini</span>
            @elseif($isPresent === false)
                <span class="badge badge-libur" style="font-size:12px;padding:5px 14px;">Libur Hari Ini</span>
            @else
                <span class="badge badge-inactive" style="font-size:12px;padding:5px 14px;">Belum Konfirmasi</span>
            @endif
            <a href="{{ route('doctor.queues.index') }}" class="btn btn-primary btn-sm">Kelola Antrian</a>
        </div>
    </div>

    {{-- Next Queue Hero --}}
    @if($stats['next_queue'] ?? null)
        <div class="queue-hero">
            <div class="qh-label">🔔 Antrian Berikutnya</div>
            <div class="qh-num">#{{ $stats['next_queue']->queue_number }}</div>
            <div class="qh-sub">{{ optional($stats['next_queue']->patient)->name ?? '—' }}</div>
            <div class="qh-meta">
                <div class="qhm-item">
                    <label>Token</label>
                    <span>****{{ substr($stats['next_queue']->token ?? '', -4) }}</span>
                </div>
                <div class="qhm-item">
                    <label>No. HP</label>
                    <span>{{ optional($stats['next_queue']->patient)->phone ?? '—' }}</span>
                </div>
                <div class="qhm-item">
                    <label>Status</label>
                    <span>{{ $stats['next_queue']->status_label }}</span>
                </div>
            </div>
            <div class="qh-actions">
                @if($stats['next_queue']->status === 'waiting')
                    <form method="POST" action="{{ route('doctor.queues.call', $stats['next_queue']) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-hero-solid">Panggil Pasien</button>
                    </form>
                @elseif($stats['next_queue']->status === 'called')
                    <form method="POST" action="{{ route('doctor.queues.start', $stats['next_queue']) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-hero-solid">Mulai Layanan</button>
                    </form>
                @elseif($stats['next_queue']->status === 'in_progress')
                    <form method="POST" action="{{ route('doctor.queues.finish', $stats['next_queue']) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-hero-solid">Selesai</button>
                    </form>
                @endif
                <a href="{{ route('doctor.queues.index') }}" class="btn-hero">Lihat Semua</a>
            </div>
        </div>
    @else
        <div class="alert alert-brand">
            <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            <span>Tidak ada antrian menunggu hari ini. Semua pasien sudah terlayani!</span>
        </div>
    @endif

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--surface2);"><svg viewBox="0 0 24 24" style="stroke:var(--text2);"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg></div>
            <div class="stat-label">Total Hari Ini</div>
            <div class="stat-value">{{ $stats['today_total'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon o"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg></div>
            <div class="stat-label">Menunggu</div>
            <div class="stat-value" style="color:#F59E0B;">{{ $stats['today_waiting'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon p"><svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/></svg></div>
            <div class="stat-label">Dilayani</div>
            <div class="stat-value" style="color:#8B5CF6;">{{ $stats['today_in_progress'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon g"><svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg></div>
            <div class="stat-label">Selesai</div>
            <div class="stat-value" style="color:var(--brand);">{{ $stats['today_done'] ?? 0 }}</div>
        </div>
    </div>

    {{-- Queue List --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Antrian Hari Ini</div>
            <a href="{{ route('doctor.queues.index') }}" class="btn btn-secondary btn-sm">Kelola →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>No</th><th>Pasien</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @php $sm=['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled']; @endphp
                    @forelse($stats['queue_list'] as $q)
                        <tr @if($q->status==='waiting') style="background:#F0FDF4;" @endif>
                            <td><strong style="font-size:15px;color:var(--brand);">#{{ $q->queue_number }}</strong></td>
                            <td>
                                <div class="avatar-row">
                                    <div class="avatar" style="background:var(--brand-light);color:var(--brand);">
                                        {{ strtoupper(substr(optional($q->patient)->name ?? 'NA', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="avatar-name">{{ optional($q->patient)->name ?? '—' }}</div>
                                        <div class="avatar-sub">{{ optional($q->patient)->phone ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge {{ $sm[$q->status] ?? '' }}">{{ $q->status_label }}</span></td>
                            <td>
                                @if($q->status === 'waiting')
                                    <form method="POST" action="{{ route('doctor.queues.call', $q) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-primary btn-xs">Panggil</button>
                                    </form>
                                @elseif($q->status === 'called')
                                    <form method="POST" action="{{ route('doctor.queues.start', $q) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-xs" style="background:#F5F3FF;color:#5B21B6;">Mulai</button>
                                    </form>
                                @elseif($q->status === 'in_progress')
                                    <form method="POST" action="{{ route('doctor.queues.finish', $q) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-xs" style="background:var(--brand-light);color:var(--brand);">Selesai</button>
                                    </form>
                                @else
                                    <span style="color:var(--text3);font-size:11px;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">
                            <div class="empty-state" style="padding:24px;">
                                <div class="empty-title">Tidak ada antrian hari ini</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @endif
</x-app-layout>