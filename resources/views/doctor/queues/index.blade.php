{{-- resources/views/doctor/queues/index.blade.php --}}
<x-app-layout title="Antrian Hari Ini">
    <x-slot name="header">
        <div class="topbar-title">Antrian Hari Ini</div>
        <div class="topbar-subtitle">{{ now()->format('d/m/Y') }}</div>
    </x-slot>
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Daftar Antrian</div>
                <div class="card-subtitle">Total {{ $queues->total() }} pasien hari ini</div>
            </div>
        </div>
        <table>
            <thead>
                <tr><th>No</th><th>Token</th><th>Pasien</th><th>Kontak</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @php $sm = ['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled']; @endphp
                @forelse($queues as $queue)
                    <tr @if($queue->status==='waiting') style="background:#F0FDF4;" @endif>
                        <td><strong style="font-size:15px;">#{{ $queue->queue_number }}</strong></td>
                        <td><span class="token">{{ $queue->token }}</span></td>
                        <td>
                            <div class="avatar-row">
                                <div class="avatar" style="background:var(--brand-light);color:var(--brand);">
                                    {{ strtoupper(substr(optional($queue->patient)->name ?? 'NA', 0, 2)) }}
                                </div>
                                <div class="avatar-name">{{ optional($queue->patient)->name ?? '—' }}</div>
                            </div>
                        </td>
                        <td style="color:var(--text2);">{{ optional($queue->patient)->phone ?? '—' }}</td>
                        <td><span class="badge {{ $sm[$queue->status] ?? '' }}">{{ $queue->status_label }}</span></td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                @if($queue->status === 'waiting')
                                    <form method="POST" action="{{ route('doctor.queues.call', $queue) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-primary btn-sm">Panggil</button>
                                    </form>
                                @elseif($queue->status === 'called')
                                    <form method="POST" action="{{ route('doctor.queues.start', $queue) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm" style="background:#EDE9FE;color:#5B21B6;">Mulai</button>
                                    </form>
                                @elseif($queue->status === 'in_progress')
                                    <form method="POST" action="{{ route('doctor.queues.finish', $queue) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm" style="background:var(--brand-light);color:var(--brand);">Selesai</button>
                                    </form>
                                @else
                                    <span style="color:var(--text3);font-size:11px;">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon"><svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg></div>
                            <div class="empty-title">Tidak ada antrian hari ini</div>
                            <div class="empty-sub">Pasien belum booking untuk jadwal Anda hari ini</div>
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
        @if($queues->hasPages())
            <div class="pagination">
                <span class="pagination-info">{{ $queues->firstItem() }}–{{ $queues->lastItem() }} dari {{ $queues->total() }}</span>
                <div class="pagination-btns">
                    @foreach($queues->getUrlRange(1,$queues->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page===$queues->currentPage()?'active':'' }}">{{ $page }}</a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>