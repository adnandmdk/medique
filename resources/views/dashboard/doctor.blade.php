<x-app-layout title="Dashboard Dokter">
    <x-slot name="header"><div class="topbar-title">Dashboard</div></x-slot>
    <x-slot name="actions">
        @php
            $todayAtt = \App\Models\DoctorAttendance::where('doctor_id', auth()->user()->doctor?->id)
                ->where('date', today()->toDateString())->first();
            $isPresent = $todayAtt?->is_present;
        @endphp
        <form method="POST" action="{{ route('doctor.attendance.toggle') }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn {{ $isPresent ? 'btn-danger' : 'btn-primary' }} btn-sm">
                {{ $isPresent === true ? '🔴 Tandai Libur' : ($isPresent === false ? '🟢 Tandai Hadir' : '🟢 Konfirmasi Hadir') }}
            </button>
        </form>
    </x-slot>

    @if(! $stats)
        <div class="alert alert-warning">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
            <span>Akun belum terdaftar sebagai dokter. Hubungi Admin.</span>
        </div>
    @else

    <div class="profile-card">
        <div class="avatar avatar-lg" style="background:var(--brand-light);color:var(--brand);">
            {{ strtoupper(substr(auth()->user()->name,0,2)) }}
        </div>
        <div class="profile-info">
            <div class="profile-name">{{ auth()->user()->name }}</div>
            <div class="profile-sub">
                {{ optional(auth()->user()->doctor)->specialization ?? '—' }} ·
                {{ optional(optional(auth()->user()->doctor)->clinic)->name ?? '—' }}
            </div>
        </div>
        <div class="profile-actions">
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

    @if($stats['next_queue'] ?? null)
        <div class="queue-hero">
            <div class="queue-hero-label">🔔 Antrian Berikutnya</div>
            <div class="queue-hero-num">#{{ $stats['next_queue']->queue_number }}</div>
            <div class="queue-hero-sub">{{ optional($stats['next_queue']->patient)->name ?? '—' }}</div>
            <div class="queue-hero-meta">
                <div class="queue-meta-item"><label>Token</label><span>****{{ substr($stats['next_queue']->token, -4) }}</span></div>
                <div class="queue-meta-item"><label>No. HP</label><span>{{ optional($stats['next_queue']->patient)->phone ?? '—' }}</span></div>
                <div class="queue-meta-item"><label>Status</label><span>{{ $stats['next_queue']->status_label }}</span></div>
            </div>
            <div class="queue-hero-actions">
                @if($stats['next_queue']->status === 'waiting')
                    <form method="POST" action="{{ route('doctor.queues.call', $stats['next_queue']) }}">
                        @csrf @method('PATCH')<button type="submit" class="btn-hero-solid">Panggil Pasien</button>
                    </form>
                @elseif($stats['next_queue']->status === 'called')
                    <form method="POST" action="{{ route('doctor.queues.start', $stats['next_queue']) }}">
                        @csrf @method('PATCH')<button type="submit" class="btn-hero-solid">Mulai Layanan</button>
                    </form>
                @elseif($stats['next_queue']->status === 'in_progress')
                    <form method="POST" action="{{ route('doctor.queues.finish', $stats['next_queue']) }}">
                        @csrf @method('PATCH')<button type="submit" class="btn-hero-solid">Selesai</button>
                    </form>
                @endif
                <a href="{{ route('doctor.queues.index') }}" class="btn-hero">Lihat Semua</a>
            </div>
        </div>
    @else
        <div class="alert alert-brand">
            <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            <span>Tidak ada antrian menunggu hari ini.</span>
        </div>
    @endif

    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);">
        <div class="stat-card"><div class="stat-icon" style="background:var(--surface2);"><svg viewBox="0 0 24 24" style="stroke:var(--text2);"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg></div><div class="stat-label">Total Hari Ini</div><div class="stat-value">{{ $stats['today_total'] ?? 0 }}</div></div>
        <div class="stat-card"><div class="stat-icon yellow"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg></div><div class="stat-label">Menunggu</div><div class="stat-value" style="color:#D97706;">{{ $stats['today_waiting'] ?? 0 }}</div></div>
        <div class="stat-card"><div class="stat-icon purple"><svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/></svg></div><div class="stat-label">Dilayani</div><div class="stat-value" style="color:#7C3AED;">{{ $stats['today_in_progress'] ?? 0 }}</div></div>
        <div class="stat-card"><div class="stat-icon green"><svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg></div><div class="stat-label">Selesai</div><div class="stat-value" style="color:var(--brand);">{{ $stats['today_done'] ?? 0 }}</div></div>
    </div>

    {{-- Kalender Dokter --}}
    <x-calendar :is-admin="false" />

    @endif
</x-app-layout>