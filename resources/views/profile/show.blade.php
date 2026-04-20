<x-app-layout title="Profil Saya">
    <x-slot name="header">
        <div class="topbar-title">Profil Saya</div>
    </x-slot>

    <div style="max-width:640px;">

        {{-- Profile Header --}}
        <div style="background:linear-gradient(135deg,#0F6E56,#1D9E75);border-radius:16px;padding:28px;color:white;margin-bottom:20px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            <div style="width:72px;height:72px;border-radius:18px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;color:white;flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name,0,2)) }}
            </div>
            <div>
                <div style="font-size:20px;font-weight:800;margin-bottom:3px;">{{ $user->name }}</div>
                <div style="font-size:13px;opacity:0.8;">{{ $user->email }}</div>
                <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;">
                    <span style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);padding:3px 12px;border-radius:20px;font-size:11px;font-weight:700;">
                        {{ ucfirst($user->role) }}
                    </span>
                    @if($user->hospital)
                        <span style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);padding:3px 12px;border-radius:20px;font-size:11px;">
                            {{ $user->hospital->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- SUCCESS ALERT --}}
        @if(session('success'))
            <div style="background:#e6fffa;color:#065f46;padding:12px 16px;border-radius:10px;margin-bottom:15px;font-size:13px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Edit Form --}}
        <div class="form-section">
            <div class="form-section-title">Informasi Pribadi</div>
            <div class="form-section-sub">Perbarui data diri Anda</div>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH') {{-- ✅ FIX DI SINI --}}

                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="form-control {{ $errors->has('name') ? 'is-error' : '' }}">
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" value="{{ $user->email }}" class="form-control" readonly
                           style="background:var(--surface2);cursor:not-allowed;">
                    <div class="form-hint">Email tidak dapat diubah.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor HP</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="form-control {{ $errors->has('phone') ? 'is-error' : '' }}"
                           placeholder="08123456789">
                    @error('phone')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                @if($user->isPatient())
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="form-group">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" value="{{ old('nik', $user->nik) }}"
                                   class="form-control {{ $errors->has('nik') ? 'is-error' : '' }}"
                                   maxlength="16">
                            @error('nik')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="date_of_birth"
                                   value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}"
                                   class="form-control {{ $errors->has('date_of_birth') ? 'is-error' : '' }}">
                            @error('date_of_birth')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" rows="3"
                                  class="form-control {{ $errors->has('address') ? 'is-error' : '' }}">{{ old('address', $user->address) }}</textarea>
                        @error('address')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                @endif

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>