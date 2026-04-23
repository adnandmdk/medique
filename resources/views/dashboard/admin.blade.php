<x-app-layout title="Dashboard">
    <x-slot name="header"><div class="topbar-title">Dashboard</div></x-slot>

    <div class="page-header">
        <div>
            <div style="font-size:12px;color:var(--text2);margin-bottom:2px;">Selamat datang kembali 👋</div>
            <div class="page-title">{{ auth()->user()->name }}</div>
        </div>
    </div>

    {{-- STATS --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon g"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></div>
            <div class="stat-label">Total RS</div>
            <div class="stat-value">{{ $stats['total_hospitals'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon b"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div class="stat-label">Total Dokter</div>
            <div class="stat-value">{{ $stats['total_doctors'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon o"><svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg></div>
            <div class="stat-label">Antrian Hari Ini</div>
            <div class="stat-value">{{ $stats['today_queues'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon r"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg></div>
            <div class="stat-label">Menunggu</div>
            <div class="stat-value" style="color:#EF4444;">{{ $stats['waiting_queues'] ?? 0 }}</div>
        </div>
    </div>

    {{-- HOSPITAL GRID --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Rumah Sakit</div>
            <a href="{{ route('admin.hospitals.index') }}" class="btn btn-secondary btn-sm">Kelola →</a>
        </div>
        <div style="padding:16px;">
            <div class="hospitals-grid" style="margin-bottom:0;">
                @forelse($hospitals as $hospital)
                    <a href="{{ route('admin.hospitals.show', $hospital) }}" class="hospital-card">
                        <div style="width:44px;height:44px;border-radius:11px;background:var(--brand-light);color:var(--brand);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;margin-bottom:10px;">
                            {{ $hospital->initials }}
                        </div>
                        <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:2px;">{{ $hospital->name }}</div>
                        <div style="font-size:11px;color:var(--text2);margin-bottom:8px);">{{ $hospital->address ?? '' }}</div>
                        <div style="display:flex;gap:5px;flex-wrap:wrap;margin-top:8px;">
                            <span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:8px;background:var(--brand-light);color:var(--brand);">{{ $hospital->doctors_count ?? 0 }} Dr</span>
                            <span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:8px;background:#FFFBEB;color:#92400E;">{{ $hospital->today_queues ?? 0 }} Antrian</span>
                        </div>
                    </a>
                @empty
                    <div style="grid-column:1/-1;text-align:center;padding:24px;color:var(--text3);font-size:13px;">
                        Belum ada rumah sakit. <a href="{{ route('admin.hospitals.create') }}" style="color:var(--brand);font-weight:600;">Tambah sekarang</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- RECENT QUEUES --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Antrian Terbaru</div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>No</th><th>Token</th><th>Pasien</th><th>Dokter</th><th>Poli</th><th>RS</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @php $sm=['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled']; @endphp
                    @forelse($stats['recent_queues'] ?? [] as $queue)
                        <tr>
                            <td><strong style="color:var(--brand);">{{ $queue->queue_number }}</strong></td>
                            <td>
                                {{-- Admin bisa lihat token penuh --}}
                                <span class="token">{{ $queue->token }}</span>
                            </td>
                            <td>{{ optional($queue->patient)->name ?? '—' }}</td>
                            <td>{{ optional(optional(optional($queue->schedule)->doctor)->user)->name ?? '—' }}</td>
                            <td>{{ optional(optional(optional($queue->schedule)->doctor)->clinic)->name ?? '—' }}</td>
                            <td style="color:var(--text2);">{{ optional($queue->hospital)->name ?? '—' }}</td>
                            <td><span class="badge {{ $sm[$queue->status] ?? '' }}">{{ $queue->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7">
                            <div class="empty-state" style="padding:24px;">
                                <div class="empty-title">Belum ada antrian</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>