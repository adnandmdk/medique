<x-app-layout title="Antrian Saya">
    <x-slot name="header"><div class="topbar-title">Antrian Saya</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('patient.queues.create') }}" class="btn btn-primary btn-sm">+ Booking</a>
    </x-slot>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Riwayat Antrian</div>
        </div>
        <table>
            <thead><tr><th>No</th><th>Token</th><th>Dokter</th><th>Poliklinik</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @php $sm = ['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled']; @endphp
                @forelse($queues as $queue)
                    <tr>
                        <td><strong>#{{ $queue->queue_number }}</strong></td>
                        <td><span class="token">{{ $queue->token }}</span></td>
                        <td>{{ optional(optional(optional($queue->schedule)->doctor)->user)->name ?? '—' }}</td>
                        <td>{{ optional(optional(optional($queue->schedule)->doctor)->clinic)->name ?? '—' }}</td>
                        <td>{{ $queue->booking_date->format('d/m/Y') }}</td>
                        <td><span class="badge {{ $sm[$queue->status] ?? '' }}">{{ $queue->status_label }}</span></td>
                        <td>
                            @if($queue->status === 'waiting')
                                <form method="POST" action="{{ route('patient.queues.cancel', $queue) }}" onsubmit="return confirm('Batalkan?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-xs">Batalkan</button>
                                </form>
                            @else
                                <span style="color:var(--text3);">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-title">Belum ada antrian</div><a href="{{ route('patient.queues.create') }}" class="btn btn-primary btn-sm">+ Booking</a></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>