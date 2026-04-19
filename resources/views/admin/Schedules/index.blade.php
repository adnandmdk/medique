{{-- resources/views/admin/schedules/index.blade.php --}}
<x-app-layout title="Jadwal Praktek">
    <x-slot name="header"><div class="topbar-title">Jadwal Praktek</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Jadwal
        </a>
    </x-slot>
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Semua Jadwal</div>
                <div class="card-subtitle">Total {{ $schedules->total() }} jadwal</div>
            </div>
        </div>
        <table>
            <thead>
                <tr><th>#</th><th>Dokter</th><th>Poliklinik</th><th>Hari</th><th>Jam</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @php
                    $dayColors = [
                        'monday'    => 'day-senin',
                        'tuesday'   => 'day-selasa',
                        'wednesday' => 'day-rabu',
                        'thursday'  => 'day-kamis',
                        'friday'    => 'day-jumat',
                        'saturday'  => 'day-sabtu',
                        'sunday'    => 'day-minggu',
                    ];
                @endphp
                @forelse($schedules as $schedule)
                    @php $dayClass = $dayColors[$schedule->day_of_week] ?? ''; @endphp
                    <tr>
                        <td style="color:var(--text2);">{{ $loop->iteration + ($schedules->currentPage()-1)*$schedules->perPage() }}</td>
                        <td>
                            {{-- ✅ optional() mencegah undefined variable --}}
                            <div class="avatar-name">
                                {{ optional(optional($schedule->doctor)->user)->name ?? '—' }}
                            </div>
                            <div class="avatar-sub">
                                {{ optional($schedule->doctor)->specialization ?? '—' }}
                            </div>
                        </td>
                        <td>{{ optional(optional($schedule->doctor)->clinic)->name ?? '—' }}</td>
                        <td><span class="badge {{ $dayClass }}">{{ $schedule->day_label }}</span></td>
                        <td>
                            <strong>{{ $schedule->start_time_label }}</strong>
                            <span style="color:var(--text3);margin:0 4px;">—</span>
                            <strong>{{ $schedule->end_time_label }}</strong>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                            <div class="empty-title">Belum ada jadwal</div>
                            <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary btn-sm">+ Tambah Jadwal</a>
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
        @if($schedules->hasPages())
            <div class="pagination">
                <span class="pagination-info">{{ $schedules->firstItem() }}–{{ $schedules->lastItem() }} dari {{ $schedules->total() }}</span>
                <div class="pagination-btns">
                    <a href="{{ $schedules->previousPageUrl() ?? '#' }}" class="page-btn {{ $schedules->onFirstPage() ? 'disabled' : '' }}">‹</a>
                    @foreach($schedules->getUrlRange(1,$schedules->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page===$schedules->currentPage()?'active':'' }}">{{ $page }}</a>
                    @endforeach
                    <a href="{{ $schedules->nextPageUrl() ?? '#' }}" class="page-btn {{ !$schedules->hasMorePages() ? 'disabled' : '' }}">›</a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>