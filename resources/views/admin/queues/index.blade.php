{{-- resources/views/admin/queues/index.blade.php --}}
<x-app-layout title="Monitor Antrian">
    <x-slot name="header"><div class="topbar-title">Monitor Antrian</div></x-slot>

    @php
        $todayQ = \App\Models\Queue::where('booking_date', today());
    @endphp
    <div class="stats-grid" style="grid-template-columns:repeat(5,1fr);">
        <div class="stat-card"><div class="stat-label">Total Hari Ini</div><div class="stat-value">{{ (clone $todayQ)->count() }}</div></div>
        <div class="stat-card"><div class="stat-label">Menunggu</div><div class="stat-value" style="color:#D97706;">{{ (clone $todayQ)->where('status','waiting')->count() }}</div></div>
        <div class="stat-card"><div class="stat-label">Dipanggil</div><div class="stat-value" style="color:#2563EB;">{{ (clone $todayQ)->where('status','called')->count() }}</div></div>
        <div class="stat-card"><div class="stat-label">Dilayani</div><div class="stat-value" style="color:#7C3AED;">{{ (clone $todayQ)->where('status','in_progress')->count() }}</div></div>
        <div class="stat-card"><div class="stat-label">Selesai</div><div class="stat-value" style="color:var(--brand);">{{ (clone $todayQ)->where('status','done')->count() }}</div></div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Semua Antrian</div>
                <div class="card-subtitle">Total {{ $queues->total() }} antrian</div>
            </div>
        </div>
        <table>
            <thead>
                <tr><th>No</th><th>Token</th><th>Pasien</th><th>Dokter</th><th>Poliklinik</th><th>Tgl</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @php
                    $sm = ['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled'];
                @endphp
                @forelse($queues as $queue)
                    <tr>
                        <td><strong>#{{ $queue->queue_number }}</strong></td>
                        <td><span class="token">{{ $queue->token }}</span></td>
                        <td>
                            <div class="avatar-name">{{ optional($queue->patient)->name ?? '—' }}</div>
                            <div class="avatar-sub">{{ optional($queue->patient)->phone ?? '—' }}</div>
                        </td>
                        <td>{{ optional(optional(optional($queue->schedule)->doctor)->user)->name ?? '—' }}</td>
                        <td>{{ optional(optional(optional($queue->schedule)->doctor)->clinic)->name ?? '—' }}</td>
                        <td style="color:var(--text2);">{{ $queue->booking_date->format('d/m/Y') }}</td>
                        <td><span class="badge {{ $sm[$queue->status] ?? '' }}">{{ $queue->status_label }}</span></td>
                        <td>
                            @if(!in_array($queue->status, ['done','cancelled']))
                                <form action="{{ route('admin.hospitals.queues.cancel', [
    'hospital' => $queue->hospital_id,
    'queue' => $queue->id
]) }}" method="POST" onsubmit="return confirm('Batalkan antrian?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-xs">Batalkan</button>
                                </form>
                            @else
                                <span style="color:var(--text3);font-size:11px;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">
                        <div class="empty-state"><div class="empty-title">Belum ada antrian</div></div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
        @if($queues->hasPages())
            <div class="pagination">
                <span class="pagination-info">{{ $queues->firstItem() }}–{{ $queues->lastItem() }} dari {{ $queues->total() }}</span>
                <div class="pagination-btns">
                    <a href="{{ $queues->previousPageUrl() ?? '#' }}" class="page-btn {{ $queues->onFirstPage() ? 'disabled' : '' }}">‹</a>
                    @foreach($queues->getUrlRange(1,$queues->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page===$queues->currentPage()?'active':'' }}">{{ $page }}</a>
                    @endforeach
                    <a href="{{ $queues->nextPageUrl() ?? '#' }}" class="page-btn {{ !$queues->hasMorePages() ? 'disabled' : '' }}">›</a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>