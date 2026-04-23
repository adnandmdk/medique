<x-app-layout title="Riwayat Antrian">
    <x-slot name="header"><div class="topbar-title">Riwayat Antrian</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('patient.queues.create') }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Booking Baru
        </a>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Semua Antrian</div>
                <div class="card-sub">{{ $queues->total() }} antrian terdaftar</div>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. Antrian</th>
                        <th>Token</th>
                        <th>Dokter</th>
                        <th>Ruangan</th>
                        <th>RS</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sm=['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled'];
                    @endphp
                    @forelse($queues as $queue)
                        <tr>
                            <td>
                                <strong style="font-size:14px;color:var(--brand);">
                                    {{ $queue->queue_number }}
                                </strong>
                            </td>
                            <td>
                                {{-- Token disensor untuk pasien --}}
                                <span class="token">****{{ substr($queue->token, -4) }}</span>
                            </td>
                            <td>{{ optional(optional($queue->schedule)->doctor)->user->name ?? '—' }}</td>
                            <td>{{ optional(optional(optional($queue->schedule)->doctor)->clinic)->name ?? '—' }}</td>
                            <td style="color:var(--text2);">{{ optional($queue->hospital)->name ?? '—' }}</td>
                            <td style="color:var(--text2);">{{ $queue->booking_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $sm[$queue->status] ?? 'badge-inactive' }}">
                                    {{ $queue->status_label }}
                                </span>
                            </td>
                            <td>
                                @if($queue->status === 'waiting')
                                    <form method="POST" action="{{ route('patient.queues.cancel', $queue) }}"
                                          onsubmit="return confirm('Batalkan antrian ini?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-danger btn-xs">Batalkan</button>
                                    </form>
                                @else
                                    <span style="color:var(--text3);font-size:11px;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                                    </div>
                                    <div class="empty-title">Belum ada antrian</div>
                                    <div class="empty-sub">Booking antrian pertama Anda sekarang</div>
                                    <a href="{{ route('patient.queues.create') }}" class="btn btn-primary btn-sm">+ Booking Sekarang</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($queues->hasPages())
            <div class="pagination">
                <span class="pagination-info">{{ $queues->firstItem() }}–{{ $queues->lastItem() }} dari {{ $queues->total() }}</span>
                <div class="pagination-btns">
                    <a href="{{ $queues->previousPageUrl() ?? '#' }}" class="page-btn {{ $queues->onFirstPage() ? 'disabled' : '' }}">‹</a>
                    @foreach($queues->getUrlRange(1, $queues->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page === $queues->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    <a href="{{ $queues->nextPageUrl() ?? '#' }}" class="page-btn {{ !$queues->hasMorePages() ? 'disabled' : '' }}">›</a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>