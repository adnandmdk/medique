<x-app-layout title="Jadwal Saya">
    <x-slot name="header"><div class="topbar-title">Jadwal Saya</div></x-slot>
    <div class="card">
        <div class="card-header"><div class="card-title">Jadwal Praktek Mingguan</div></div>
        <table>
            <thead><tr><th>#</th><th>Hari</th><th>Jam Mulai</th><th>Jam Selesai</th><th>Poliklinik</th></tr></thead>
            <tbody>
                @php $dayColors = ['monday'=>'day-senin','tuesday'=>'day-selasa','wednesday'=>'day-rabu','thursday'=>'day-kamis','friday'=>'day-jumat','saturday'=>'day-sabtu','sunday'=>'day-minggu']; @endphp
                @forelse($schedules as $schedule)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><span class="badge {{ $dayColors[$schedule->day_of_week] ?? '' }}">{{ $schedule->day_label }}</span></td>
                        <td><strong>{{ $schedule->start_time_label }}</strong></td>
                        <td><strong>{{ $schedule->end_time_label }}</strong></td>
                        <td>{{ optional(optional($schedule->doctor)->clinic)->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty-state"><div class="empty-title">Belum ada jadwal</div><div class="empty-sub">Hubungi Admin</div></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>