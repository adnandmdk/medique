<x-app-layout title="{{ $hospital->name }}">
    <x-slot name="header">
        <div>
            <div style="font-size:12px;color:var(--text2);margin-bottom:2px;">
                <a href="{{ route('admin.hospitals.index') }}" style="color:var(--brand);">Rumah Sakit</a> /
            </div>
            <div class="topbar-title">{{ $hospital->name }}</div>
        </div>
    </x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.hospitals.edit', $hospital) }}" class="btn btn-secondary btn-sm">Edit RS</a>
        <a href="{{ route('admin.doctors.create', $hospital) }}" class="btn btn-primary btn-sm">+ Dokter</a>
    </x-slot>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon g"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></div>
            <div class="stat-label">Poliklinik</div>
            <div class="stat-value">{{ $stats['total_clinics'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon b"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div class="stat-label">Dokter</div>
            <div class="stat-value">{{ $stats['total_doctors'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon o"><svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg></div>
            <div class="stat-label">Antrian Hari Ini</div>
            <div class="stat-value">{{ $stats['today_queues'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon r"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg></div>
            <div class="stat-label">Menunggu</div>
            <div class="stat-value" style="color:#EF4444;">{{ $stats['waiting_queues'] }}</div>
        </div>
    </div>

    {{-- Dokter --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Dokter</div>
                <div class="card-sub">{{ $doctors->count() }} dokter terdaftar</div>
            </div>
            <a href="{{ route('admin.doctors.create', $hospital) }}" class="btn btn-primary btn-sm">+ Tambah</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nama</th><th>Poli</th><th>Spesialisasi</th><th>Jadwal</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($doctors as $doctor)
                        <tr>
                            <td>
                                <div class="avatar-row">
                                    <div class="avatar" style="background:var(--brand-light);color:var(--brand);">{{ strtoupper(substr(optional($doctor->user)->name??'NA',0,2)) }}</div>
                                    <div>
                                        <div class="avatar-name">{{ optional($doctor->user)->name ?? '—' }}</div>
                                        <div class="avatar-sub">{{ optional($doctor->user)->email ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ optional($doctor->clinic)->name ?? '—' }}</td>
                            <td>{{ $doctor->specialization }}</td>
                            <td>
                                <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                    @foreach($doctor->schedules as $s)
                                        <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:4px;background:var(--brand-light);color:var(--brand);">{{ $s->day_label }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <a href="{{ route('admin.doctors.edit', [$hospital, $doctor]) }}" class="btn btn-secondary btn-xs">Edit</a>
                                    <form action="{{ route('admin.doctors.destroy', [$hospital, $doctor]) }}" method="POST" onsubmit="return confirm('Hapus dokter?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state" style="padding:24px;"><div class="empty-title">Belum ada dokter</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Antrian Hari Ini --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Antrian Hari Ini</div>
            <a href="{{ route('admin.queues.index', $hospital) }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>No</th><th>Pasien</th><th>Dokter</th><th>Poli</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @php $sm=['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled']; @endphp
                    @forelse($queues as $queue)
                        <tr>
                            <td><strong>{{ $queue->queue_number }}</strong></td>
                            <td>{{ optional($queue->patient)->name ?? '—' }}</td>
                            <td>{{ optional(optional(optional($queue->schedule)->doctor)->user)->name ?? '—' }}</td>
                            <td>{{ optional(optional(optional($queue->schedule)->doctor)->clinic)->name ?? '—' }}</td>
                            <td><span class="badge {{ $sm[$queue->status]??'' }}">{{ $queue->status_label }}</span></td>
                            <td>
                                @if(!in_array($queue->status,['done','cancelled']))
                                    <form action="{{ route('admin.queues.cancel', [$hospital, $queue]) }}" method="POST" onsubmit="return confirm('Batalkan?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-danger btn-xs">Batalkan</button>
                                    </form>
                                @else
                                    <span style="color:var(--text3);font-size:11px;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state" style="padding:24px;"><div class="empty-title">Tidak ada antrian hari ini</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>