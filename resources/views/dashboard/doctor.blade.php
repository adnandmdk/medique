<x-app-layout title="Dashboard Dokter">
    <x-slot name="header">
        <div class="topbar-title">Dashboard</div>
        <div class="topbar-subtitle">{{ now()->format('d/m/Y') }}</div>
    </x-slot>

    @if(empty($stats))
        <div class="alert alert-warning">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>Akun Anda belum terdaftar sebagai dokter. Hubungi Admin.</span>
        </div>
    @else

        {{-- Profile --}}
        <div class="profile-card">
            <div class="avatar avatar-lg" style="background:var(--brand-light);color:var(--brand);">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="profile-info">
                <div class="profile-name">{{ auth()->user()->name }}</div>
                <div class="profile-sub">
                    {{ optional(auth()->user()->doctor)->specialization ?? '—' }} ·
                    {{ optional(optional(auth()->user()->doctor)->clinic)->name ?? '—' }}
                </div>
            </div>
            <div class="profile-actions">
                <span class="badge badge-active" style="font-size:11px;padding:4px 12px;">Bertugas</span>
                <a href="{{ route('doctor.queues.index') }}" class="btn btn-primary btn-sm">Kelola Antrian</a>
            </div>
        </div>

        {{-- Next Queue Hero --}}
        @if($stats['next_queue'] ?? null)
            <div class="queue-hero">
                <div class="queue-hero-label">Antrian Berikutnya</div>
                <div class="queue-hero-num">#{{ $stats['next_queue']->queue_number }}</div>
                <div class="queue-hero-sub">{{ optional($stats['next_queue']->patient)->name ?? '—' }}</div>
                <div class="queue-hero-meta">
                    <div class="queue-meta-item">
                        <label>Token</label>
                        <span>{{ $stats['next_queue']->token }}</span>
                    </div>
                    <div class="queue-meta-item">
                        <label>No. HP</label>
                        <span>{{ optional($stats['next_queue']->patient)->phone ?? '—' }}</span>
                    </div>
                    <div class="queue-meta-item">
                        <label>Status</label>
                        <span>{{ $stats['next_queue']->status_label }}</span>
                    </div>
                </div>
                <div class="queue-hero-actions">
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
                <span>Tidak ada antrian yang menunggu hari ini.</span>
            </div>
        @endif

        {{-- Stats --}}
        <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--surface2);">
                    <svg viewBox="0 0 24 24" style="stroke:var(--text2);"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                </div>
                <div class="stat-label">Total Hari Ini</div>
                <div class="stat-value">{{ $stats['today_total'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                </div>
                <div class="stat-label">Menunggu</div>
                <div class="stat-value" style="color:#D97706;">{{ $stats['today_waiting'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">
                    <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/></svg>
                </div>
                <div class="stat-label">Dilayani</div>
                <div class="stat-value" style="color:#7C3AED;">{{ $stats['today_in_progress'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                </div>
                <div class="stat-label">Selesai</div>
                <div class="stat-value" style="color:var(--brand);">{{ $stats['today_done'] ?? 0 }}</div>
            </div>
        </div>

        {{-- Queue list --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Antrian Hari Ini</div>
                <a href="{{ route('doctor.queues.index') }}" class="btn btn-secondary btn-sm">Kelola →</a>
            </div>
            <table>
                <thead>
                    <tr><th>No</th><th>Token</th><th>Pasien</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @php
                        $sm = ['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled'];
                    @endphp
                    @forelse($stats['queue_list'] ?? [] as $queue)
                        <tr @if($queue->status==='waiting') style="background:#F0FDF4;" @endif>
                            <td><strong>#{{ $queue->queue_number }}</strong></td>
                            <td><span class="token">{{ $queue->token }}</span></td>
                            <td><div class="avatar-name">{{ optional($queue->patient)->name ?? '—' }}</div></td>
                            <td><span class="badge {{ $sm[$queue->status] ?? '' }}">{{ $queue->status_label }}</span></td>
                            <td>
                                @if($queue->status === 'waiting')
                                    <form method="POST" action="{{ route('doctor.queues.call', $queue) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-primary btn-xs">Panggil</button>
                                    </form>
                                @elseif($queue->status === 'called')
                                    <form method="POST" action="{{ route('doctor.queues.start', $queue) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-xs" style="background:#EDE9FE;color:#5B21B6;">Mulai</button>
                                    </form>
                                @elseif($queue->status === 'in_progress')
                                    <form method="POST" action="{{ route('doctor.queues.finish', $queue) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-xs" style="background:var(--brand-light);color:var(--brand);">Selesai</button>
                                    </form>
                                @else
                                    <span style="color:var(--text3);font-size:11px;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">
                            <div class="empty-state" style="padding:28px;">
                                <div class="empty-title">Tidak ada antrian hari ini</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @endif
</x-app-layout>