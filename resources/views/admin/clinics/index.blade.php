{{-- resources/views/admin/clinics/index.blade.php --}}
<x-app-layout title="Poliklinik">
    <x-slot name="header"><div class="topbar-title">Poliklinik</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.hospitals.clinics.create', $hospital->id) }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Poliklinik
        </a>
    </x-slot>
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Daftar Poliklinik</div>
                <div class="card-subtitle">Total {{ $clinics->total() }} terdaftar</div>
            </div>
            <div class="search-wrap">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input class="search-input" placeholder="Cari poliklinik...">
            </div>
        </div>
        <table>
            <thead>
                <tr><th>#</th><th>Nama</th><th>Lokasi</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($clinics as $clinic)
                    <tr>
                        <td style="color:var(--text2);">{{ $loop->iteration + ($clinics->currentPage()-1)*$clinics->perPage() }}</td>
                        <td>
                            <div class="avatar-row">
                                <div class="avatar" style="background:var(--brand-light);color:var(--brand);">
                                    {{ strtoupper(substr($clinic->name, 0, 2)) }}
                                </div>
                                <div class="avatar-name">{{ $clinic->name }}</div>
                            </div>
                        </td>
                        <td style="color:var(--text2);">{{ $clinic->location }}</td>
                        <td>
                            <form action="{{ route('admin.hospitals.clinics.toggle', ['hospital' => $hospital->id, 'clinic' => $clinic->id]) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="badge {{ $clinic->is_active ? 'badge-active' : 'badge-inactive' }}" style="cursor:pointer;border:none;font-family:inherit;">
                                    {{ $clinic->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.hospitals.clinics.edit', ['hospital' => $hospital->id, 'clinic' => $clinic->id]) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form action="{{ route('admin.hospitals.clinics.destroy', ['hospital' => $hospital->id, 'clinic' => $clinic->id]) }}" method="POST" onsubmit="return confirm('Hapus poliklinik ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></div>
                            <div class="empty-title">Belum ada poliklinik</div>
                            <a href="{{ route('admin.clinics.create') }}" class="btn btn-primary btn-sm">+ Tambah</a>
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
        @if($clinics->hasPages())
            <div class="pagination">
                <span class="pagination-info">{{ $clinics->firstItem() }}–{{ $clinics->lastItem() }} dari {{ $clinics->total() }}</span>
                <div class="pagination-btns">
                    <a href="{{ $clinics->previousPageUrl() ?? '#' }}" class="page-btn {{ $clinics->onFirstPage() ? 'disabled' : '' }}">‹</a>
                    @foreach($clinics->getUrlRange(1,$clinics->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page===$clinics->currentPage()?'active':'' }}">{{ $page }}</a>
                    @endforeach
                    <a href="{{ $clinics->nextPageUrl() ?? '#' }}" class="page-btn {{ !$clinics->hasMorePages() ? 'disabled' : '' }}">›</a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>