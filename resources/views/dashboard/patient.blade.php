<x-app-layout title="Dashboard Pasien">
    <x-slot name="header"><div class="topbar-title">Dashboard</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('patient.queues.create') }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Booking Antrian
        </a>
    </x-slot>

    <div class="profile-card">
        <div class="avatar avatar-lg" style="background:#EDE9FE;color:#534AB7;">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="profile-info">
            <div class="profile-name">{{ auth()->user()->name }}</div>
            <div class="profile-sub">{{ auth()->user()->email }}</div>
        </div>
        <span class="badge badge-active" style="font-size:11px;padding:4px 12px;">Pasien</span>
    </div>

    @if($stats['active_queue'] ?? null)
        <div class="queue-hero">
            <div class="queue-hero-label">Antrian Aktif Anda</div>
            <div class="queue-hero-num">#{{ $stats['active_queue']->queue_number }}</div>
            <div class="queue-hero-sub">
                {{ optional(optional(optional($stats['active_queue']->schedule)->doctor)->user)->name ?? '—' }} ·
                {{ optional(optional(optional($stats['active_queue']->schedule)->doctor)->clinic)->name ?? '—' }}
            </div>
            <div class="queue-hero-meta">
                <div class="queue-meta-item">
                    <label>Token</label>
                    <span>{{ $stats['active_queue']->token }}</span>
                </div>
                <div class="queue-meta-item">
                    <label>Tanggal</label>
                    <span>{{ $stats['active_queue']->booking_date->format('d/m/Y') }}</span>
                </div>
                <div class="queue-meta-item">
                    <label>Status</label>
                    <span>{{ $stats['active_queue']->status_label }}</span>
                </div>
            </div>
            <div class="queue-hero-actions">
                @if($stats['active_queue']->status === 'waiting')
                    <form method="POST" action="{{ route('patient.queues.cancel', $stats['active_queue']) }}"
                          onsubmit="return confirm('Batalkan antrian ini?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-hero">Batalkan</button>
                    </form>
                @endif
                <a href="{{ route('patient.queues.index') }}" class="btn-hero">Lihat Semua</a>
            </div>
        </div>
    @else
        <div class="card" style="border:2px dashed var(--border);margin-bottom:20px;">
            <div class="card-body" style="text-align:center;padding:36px;">
                <div class="empty-icon" style="margin:0 auto 14px;">
                    <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                </div>
                <div class="empty-title">Tidak ada antrian aktif</div>
                <div class="empty-sub">Buat booking antrian untuk berobat</div>
                <a href="{{ route('patient.queues.create') }}" class="btn btn-primary btn-sm">+ Booking Sekarang</a>
            </div>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:180px 1fr;gap:14px;align-items:start;">
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
            </div>
            <div class="stat-label">Total Booking</div>
            <div class="stat-value">{{ $stats['total_bookings'] ?? 0 }}</div>
            <div class="stat-sub">semua antrian</div>
        </div>

        <div class="card" style="margin-bottom:0;">
            <div class="card-header">
                <div class="card-title">Riwayat Antrian</div>
                <a href="{{ route('patient.queues.index') }}" class="btn btn-secondary btn-sm">Semua →</a>
            </div>
            <table>
                <thead>
                    <tr><th>No</th><th>Token</th><th>Dokter</th><th>Poliklinik</th><th>Tanggal</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @php $sm = ['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled']; @endphp
                    @forelse($stats['recent_queues'] ?? [] as $queue)
                        <tr>
                            <td><strong>#{{ $queue->queue_number }}</strong></td>
                            <td><span class="token">{{ $queue->token }}</span></td>
                            <td>{{ optional(optional(optional($queue->schedule)->doctor)->user)->name ?? '—' }}</td>
                            <td>{{ optional(optional(optional($queue->schedule)->doctor)->clinic)->name ?? '—' }}</td>
                            <td style="color:var(--text2);">{{ $queue->booking_date->format('d/m/Y') }}</td>
                            <td><span class="badge {{ $sm[$queue->status] ?? '' }}">{{ $queue->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6">
                            <div class="empty-state" style="padding:24px;">
                                <div class="empty-title">Belum ada riwayat</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>