<x-app-layout title="Rumah Sakit">
    <x-slot name="header"><div class="topbar-title">Rumah Sakit</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.hospitals.create') }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah RS
        </a>
    </x-slot>

    <div class="page-header">
        <div>
            <div class="page-title">Daftar Rumah Sakit</div>
            <div class="page-sub">{{ $hospitals->count() }} rumah sakit terdaftar</div>
        </div>
    </div>

    <div class="hospitals-grid">
        @forelse($hospitals as $hospital)
            <a href="{{ route('admin.hospitals.show', $hospital) }}" class="hospital-card">
                <div style="width:48px;height:48px;border-radius:12px;background:var(--brand-light);color:var(--brand);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;margin-bottom:12px;">
                    {{ $hospital->initials }}
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:3px;">{{ $hospital->name }}</div>
                <div style="font-size:12px;color:var(--text2);margin-bottom:10px;">{{ $hospital->address ?? 'Alamat belum diisi' }}</div>
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;background:var(--brand-light);color:var(--brand);">
                        {{ $hospital->clinics_count ?? 0 }} Poli
                    </span>
                    <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;background:#EFF6FF;color:#1E40AF;">
                        {{ $hospital->doctors_count ?? 0 }} Dokter
                    </span>
                    <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;background:#FFFBEB;color:#92400E;">
                        {{ $hospital->today_queues ?? 0 }} Antrian
                    </span>
                </div>
                @if(!$hospital->is_active)
                    <div style="margin-top:8px;"><span class="badge badge-inactive">Nonaktif</span></div>
                @endif
            </a>
        @empty
            <div style="grid-column:1/-1;">
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div class="empty-title">Belum ada rumah sakit</div>
                    <div class="empty-sub">Tambahkan rumah sakit pertama Anda</div>
                    <a href="{{ route('admin.hospitals.create') }}" class="btn btn-primary btn-sm">+ Tambah Sekarang</a>
                </div>
            </div>
        @endforelse
    </div>
</x-app-layout>