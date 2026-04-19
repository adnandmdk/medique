<x-app-layout title="Dashboard Admin">
    <x-slot name="header"><div class="topbar-title">Dashboard</div></x-slot>

    <div class="page-header">
        <div>
            <div class="page-greeting">Selamat datang kembali 👋</div>
            <div class="page-title">{{ auth()->user()->name }}</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon green"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></div>
            <div class="stat-label">Poliklinik Aktif</div>
            <div class="stat-value">{{ $stats['active_clinics'] ?? 0 }}</div>
            <div class="stat-sub">dari {{ $stats['total_clinics'] ?? 0 }} total</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div class="stat-label">Total Dokter</div>
            <div class="stat-value">{{ $stats['total_doctors'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/></svg></div>
            <div class="stat-label">Total Pasien</div>
            <div class="stat-value">{{ $stats['total_patients'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg></div>
            <div class="stat-label">Antrian Hari Ini</div>
            <div class="stat-value">{{ $stats['today_queues'] ?? 0 }}</div>
            <div class="stat-sub">{{ $stats['done_queues'] ?? 0 }} selesai · {{ $stats['waiting_queues'] ?? 0 }} menunggu</div>
        </div>
    </div>

    <div class="quick-grid">
        <a href="{{ route('admin.clinics.index') }}" class="quick-card">
            <div class="quick-icon" style="background:var(--brand-light);"><svg viewBox="0 0 24 24" stroke="var(--brand)"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></div>
            <div class="quick-label">Poliklinik</div>
        </a>
        <a href="{{ route('admin.doctors.index') }}" class="quick-card">
            <div class="quick-icon" style="background:#E6F1FB;"><svg viewBox="0 0 24 24" stroke="#185FA5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div class="quick-label">Dokter</div>
        </a>
        <a href="{{ route('admin.schedules.index') }}" class="quick-card">
            <div class="quick-icon" style="background:#EEEDFE;"><svg viewBox="0 0 24 24" stroke="#534AB7"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div class="quick-label">Jadwal</div>
        </a>
        <a href="{{ route('admin.queues.index') }}" class="quick-card">
            <div class="quick-icon" style="background:var(--accent-light);"><svg viewBox="0 0 24 24" stroke="var(--accent)"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg></div>
            <div class="quick-label">Antrian</div>
        </a>
    </div>

    {{-- Kalender Admin --}}
    <x-calendar :doctors="$doctors ?? collect()" :schedules="$calendarSchedules ?? []" :is-admin="true" />

    {{-- Recent Queues --}}
    <div class="card">
        <div class="card-header">
            <div><div class="card-title">Antrian Terbaru</div></div>
            <a href="{{ route('admin.queues.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>No</th><th>Token</th><th>Pasien</th><th>Dokter</th><th>Poliklinik</th><th>Status</th></tr></thead>
                <tbody>
                    @php
                        $sm = ['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled'];
                        $avatarColors = [['#DBEAFE','#1E40AF'],['#D1FAE5','#065F46'],['#EDE9FE','#5B21B6'],['#FEF3C7','#92400E'],['#FEE2E2','#991B1B']];
                    @endphp
                    @forelse($stats['recent_queues'] ?? [] as $queue)
                        @php [$bg,$fg] = $avatarColors[$loop->index % 5]; @endphp
                        <tr>
                            <td><strong>#{{ $queue->queue_number }}</strong></td>
                            <td><span class="token">{{ $queue->token }}</span></td>
                            <td>
                                <div class="avatar-row">
                                    <div class="avatar" style="background:{{ $bg }};color:{{ $fg }};">{{ strtoupper(substr(optional($queue->patient)->name ?? 'NA',0,2)) }}</div>
                                    <div><div class="avatar-name">{{ optional($queue->patient)->name ?? '—' }}</div></div>
                                </div>
                            </td>
                            <td>{{ optional(optional(optional($queue->schedule)->doctor)->user)->name ?? '—' }}</td>
                            <td>{{ optional(optional(optional($queue->schedule)->doctor)->clinic)->name ?? '—' }}</td>
                            <td><span class="badge {{ $sm[$queue->status] ?? '' }}">{{ $queue->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state" style="padding:24px;"><div class="empty-title">Belum ada antrian</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>