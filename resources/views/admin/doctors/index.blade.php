{{-- resources/views/admin/doctors/index.blade.php --}}
<x-app-layout title="Dokter">
    <x-slot name="header"><div class="topbar-title">Dokter</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Dokter
        </a>
    </x-slot>
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Daftar Dokter</div>
                <div class="card-subtitle">Total {{ $doctors->total() }} dokter</div>
            </div>
        </div>
        <table>
            <thead>
                <tr><th>#</th><th>Nama Dokter</th><th>Poliklinik</th><th>Spesialisasi</th><th>No. Lisensi</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @php
                    $colors = [
                        ['#DBEAFE','#1E40AF'],
                        ['#D1FAE5','#065F46'],
                        ['#EDE9FE','#5B21B6'],
                        ['#FEF3C7','#92400E'],
                    ];
                @endphp
                @forelse($doctors as $doctor)
                    @php [$bg,$fg] = $colors[$loop->index % 4]; @endphp
                    <tr>
                        <td style="color:var(--text2);">{{ $loop->iteration + ($doctors->currentPage()-1)*$doctors->perPage() }}</td>
                        <td>
                            <div class="avatar-row">
                                <div class="avatar" style="background:{{ $bg }};color:{{ $fg }};">
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
                        <td><span class="token">{{ $doctor->licence_number }}</span></td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('Hapus dokter ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="empty-state">
                            <div class="empty-title">Belum ada dokter terdaftar</div>
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
        @if($doctors->hasPages())
            <div class="pagination">
                <span class="pagination-info">{{ $doctors->firstItem() }}–{{ $doctors->lastItem() }} dari {{ $doctors->total() }}</span>
                <div class="pagination-btns">
                    <a href="{{ $doctors->previousPageUrl() ?? '#' }}" class="page-btn {{ $doctors->onFirstPage() ? 'disabled' : '' }}">‹</a>
                    @foreach($doctors->getUrlRange(1,$doctors->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page===$doctors->currentPage()?'active':'' }}">{{ $page }}</a>
                    @endforeach
                    <a href="{{ $doctors->nextPageUrl() ?? '#' }}" class="page-btn {{ !$doctors->hasMorePages() ? 'disabled' : '' }}">›</a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>