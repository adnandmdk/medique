<x-app-layout title="Profil Saya">
    <x-slot name="header"><div class="topbar-title">Profil Saya</div></x-slot>

    <div style="max-width:600px;">

        <div class="profile-hero">
            <div class="ph-avatar">{{ strtoupper(substr($user->name,0,2)) }}</div>
            <div style="flex:1;min-width:0;">
                <div class="ph-name">{{ $user->name }}</div>
                <div class="ph-sub">{{ $user->email }}</div>
                <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap;">
                    <span style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">{{ ucfirst($user->role) }}</span>
                    @if($user->hospital)
                        <span style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);padding:3px 10px;border-radius:20px;font-size:11px;">{{ $user->hospital->name }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-title">Informasi Pribadi</div>
            <div class="form-section-sub">Perbarui data diri Anda</div>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name',$user->name) }}"
                           class="form-control {{ $errors->has('name')?'is-error':'' }}">
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" value="{{ $user->email }}" class="form-control"
                           readonly style="background:var(--surface2);cursor:not-allowed;color:var(--text2);">
                    <div class="form-hint">Email tidak dapat diubah.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor HP</label>
                    <input type="tel" name="phone" value="{{ old('phone',$user->phone) }}"
                           class="form-control" placeholder="08123456789">
                </div>

                @if($user->isPatient())
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="form-group">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" value="{{ old('nik',$user->nik) }}"
                                   class="form-control {{ $errors->has('nik')?'is-error':'' }}"
                                   placeholder="3271xxxxxxxxxxxx" maxlength="16">
                            @error('nik')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="date_of_birth"
                                   value="{{ old('date_of_birth',optional($user->date_of_birth)->format('Y-m-d')) }}"
                                   class="form-control" max="{{ today()->subDay()->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                            <label class="check-label" id="lm">
                                <input type="radio" name="gender" value="male"
                                       {{ old('gender',$user->gender)==='male'?'checked':'' }}
                                       style="accent-color:var(--brand);"
                                       onchange="setG('male')">
                                <span class="check-text">👨 Laki-laki</span>
                            </label>
                            <label class="check-label" id="lf">
                                <input type="radio" name="gender" value="female"
                                       {{ old('gender',$user->gender)==='female'?'checked':'' }}
                                       style="accent-color:var(--brand);"
                                       onchange="setG('female')">
                                <span class="check-text">👩 Perempuan</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control"
                                  placeholder="Jl. Contoh No. 1...">{{ old('address',$user->address) }}</textarea>
                    </div>
                @endif

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>

        @if($queues->count() > 0)
            <div class="card">
                <div class="card-header"><div class="card-title">Riwayat Kunjungan</div></div>
                @php $sm=['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled']; @endphp
                @foreach($queues as $q)
                    <div style="padding:11px 16px;border-bottom:1px solid #F8FAFC;display:flex;align-items:center;gap:10px;">
                        <div style="font-size:14px;font-weight:800;color:var(--brand);min-width:60px;">{{ $q->queue_number }}</div>
                        <div style="flex:1;">
                            <div style="font-size:13px;font-weight:600;">{{ optional(optional(optional($q->schedule)->doctor)->user)->name??'—' }}</div>
                            <div style="font-size:11px;color:var(--text2);">{{ $q->booking_date->format('d/m/Y') }}</div>
                        </div>
                        <span class="badge {{ $sm[$q->status]??'' }}">{{ $q->status_label }}</span>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    <script>
    function setG(v){
        const m=document.getElementById('lm'),f=document.getElementById('lf');
        m.style.borderColor=v==='male'?'var(--brand)':'var(--border)';
        m.style.background=v==='male'?'var(--brand-light)':'var(--surface)';
        f.style.borderColor=v==='female'?'var(--brand)':'var(--border)';
        f.style.background=v==='female'?'var(--brand-light)':'var(--surface)';
    }
    // Init
    const cur='{{ old("gender",$user->gender) }}';
    if(cur) setG(cur);
    </script>
</x-app-layout>