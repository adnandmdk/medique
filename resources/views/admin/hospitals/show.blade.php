<x-app-layout title="{{ $hospital->name }}">
    <x-slot name="header">
        <div>
            <div style="font-size:11px;color:var(--text2);margin-bottom:2px;">
                <a href="{{ route('admin.hospitals.index') }}" style="color:var(--brand);">Rumah Sakit</a>
                <span style="margin:0 4px;">›</span>
            </div>
            <div class="topbar-title">{{ $hospital->name }}</div>
        </div>
    </x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.hospitals.edit', $hospital) }}" class="btn btn-secondary btn-sm">Edit RS</a>
        <a href="{{ route('admin.hospitals.doctors.create', $hospital) }}" class="btn btn-primary btn-sm">+ Dokter</a>
    </x-slot>

    {{-- STATS --}}
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

    {{-- QUICK LINKS --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;margin-bottom:16px;">
        <a href="{{ route('admin.hospitals.clinics.index', $hospital) }}"
           style="background:var(--surface);border:1.5px solid var(--border);border-radius:10px;padding:14px;text-decoration:none;transition:all .12s;display:flex;align-items:center;gap:10px;"
           onmouseover="this.style.borderColor='var(--brand)';this.style.background='var(--brand-light)'"
           onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--surface)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            <span style="font-size:13px;font-weight:600;color:var(--text);">Kelola Poli</span>
        </a>
        <a href="{{ route('admin.hospitals.doctors.index', $hospital) }}"
           style="background:var(--surface);border:1.5px solid var(--border);border-radius:10px;padding:14px;text-decoration:none;transition:all .12s;display:flex;align-items:center;gap:10px;"
           onmouseover="this.style.borderColor='#3B82F6';this.style.background='#EFF6FF'"
           onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--surface)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span style="font-size:13px;font-weight:600;color:var(--text);">Kelola Dokter</span>
        </a>
        <a href="{{ route('admin.hospitals.schedules.index', $hospital) }}"
           style="background:var(--surface);border:1.5px solid var(--border);border-radius:10px;padding:14px;text-decoration:none;transition:all .12s;display:flex;align-items:center;gap:10px;"
           onmouseover="this.style.borderColor='#8B5CF6';this.style.background='#F5F3FF'"
           onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--surface)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8B5CF6" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span style="font-size:13px;font-weight:600;color:var(--text);">Kelola Jadwal</span>
        </a>
        <a href="{{ route('admin.hospitals.queues.index', $hospital) }}"
           style="background:var(--surface);border:1.5px solid var(--border);border-radius:10px;padding:14px;text-decoration:none;transition:all .12s;display:flex;align-items:center;gap:10px;"
           onmouseover="this.style.borderColor='#F59E0B';this.style.background='#FFFBEB'"
           onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--surface)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
            <span style="font-size:13px;font-weight:600;color:var(--text);">Monitor Antrian</span>
        </a>
    </div>

    {{-- DOKTER --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Dokter</div>
                <div class="card-sub">{{ $doctors->count() }} dokter terdaftar</div>
            </div>
            <a href="{{ route('admin.hospitals.doctors.create', $hospital) }}" class="btn btn-primary btn-sm">+ Tambah</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Nama</th><th>Poli</th><th>Spesialisasi</th><th>Jadwal</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($doctors as $doctor)
                        <tr>
                            <td>
                                <div class="avatar-row">
                                    <div class="avatar" style="background:var(--brand-light);color:var(--brand);">
                                        {{ strtoupper(substr(optional($doctor->user)->name ?? 'NA', 0, 2)) }}
                                    </div>
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
                                        <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:4px;background:var(--brand-light);color:var(--brand);">
                                            {{ $s->day_label }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <a href="{{ route('admin.hospitals.doctors.edit', [$hospital, $doctor]) }}" class="btn btn-secondary btn-xs">Edit</a>
                                    <form action="{{ route('admin.hospitals.doctors.destroy', [$hospital, $doctor]) }}"
                                          method="POST" onsubmit="return confirm('Hapus dokter ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">
                            <div class="empty-state" style="padding:24px;">
                                <div class="empty-title">Belum ada dokter</div>
                                <a href="{{ route('admin.hospitals.doctors.create', $hospital) }}" class="btn btn-primary btn-sm">+ Tambah Dokter</a>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ANTRIAN HARI INI --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Antrian Hari Ini</div>
            <a href="{{ route('admin.hospitals.queues.index', $hospital) }}" class="btn btn-secondary btn-sm">Monitor →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>No</th><th>Pasien</th><th>Dokter</th><th>Poli</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @php $sm=['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled']; @endphp
                    @forelse($queues as $queue)
                        <tr>
                            <td><strong>{{ $queue->queue_number }}</strong></td>
                            <td>{{ optional($queue->patient)->name ?? '—' }}</td>
                            <td>{{ optional(optional(optional($queue->schedule)->doctor)->user)->name ?? '—' }}</td>
                            <td>{{ optional(optional(optional($queue->schedule)->doctor)->clinic)->name ?? '—' }}</td>
                            <td><span class="badge {{ $sm[$queue->status] ?? '' }}">{{ $queue->status_label }}</span></td>
                            <td>
                                @if(! in_array($queue->status, ['done','cancelled']))
                                    <form action="{{ route('admin.hospitals.queues.cancel', [$hospital, $queue]) }}"
                                          method="POST" onsubmit="return confirm('Batalkan antrian?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-danger btn-xs">Batalkan</button>
                                    </form>
                                @else
                                    <span style="color:var(--text3);font-size:11px;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">
                            <div class="empty-state" style="padding:24px;">
                                <div class="empty-title">Tidak ada antrian hari ini</div>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>