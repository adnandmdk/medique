<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Medique</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #F7F8FA;
            display: flex;
            min-height: 100vh;
            margin: 0;
        }

        /* ── LEFT PANEL ── */
        .auth-left {
            flex: 1;
            background: #0F6E56;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute; right: -80px; top: -80px;
            width: 320px; height: 320px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .auth-left::after {
            content: '';
            position: absolute; left: -60px; bottom: -80px;
            width: 240px; height: 240px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .al-brand { position: relative; z-index: 1; }
        .al-brand-logo { display: flex; align-items: center; gap: 12px; margin-bottom: 4px; }
        .al-brand-icon {
            width: 42px; height: 42px;
            background: rgba(255,255,255,0.15);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
        }
        .al-brand-icon svg { width: 20px; height: 20px; stroke: white; fill: none; stroke-width: 2; }
        .al-brand-name { font-size: 26px; font-weight: 800; color: white; letter-spacing: -0.5px; }
        .al-brand-sub { font-size: 12px; color: rgba(255,255,255,0.55); font-weight: 500; letter-spacing: 0.3px; margin-left: 54px; }
        .al-content { position: relative; z-index: 1; }
        .al-headline {
            font-size: 28px; font-weight: 800;
            color: white; line-height: 1.25; margin-bottom: 14px;
        }
        .al-desc { font-size: 14px; color: rgba(255,255,255,0.72); line-height: 1.75; margin-bottom: 28px; }
        .al-features { display: flex; flex-direction: column; gap: 10px; }
        .al-feature {
            display: flex; align-items: center; gap: 12px;
            font-size: 13px; color: rgba(255,255,255,0.85);
        }
        .al-feature-icon {
            width: 28px; height: 28px;
            background: rgba(255,255,255,0.12);
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .al-feature-icon svg { width: 14px; height: 14px; stroke: white; fill: none; stroke-width: 2; }
        .al-hospital {
            position: relative; z-index: 1;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 14px 16px;
            display: flex; align-items: center; gap: 12px;
        }
        .al-hospital-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 800; color: white; flex-shrink: 0;
        }
        .al-hospital-name  { font-size: 13px; font-weight: 700; color: white; }
        .al-hospital-label { font-size: 10px; color: rgba(255,255,255,0.55); margin-top: 1px; }
        .al-hospital-change { margin-left: auto; font-size: 11px; color: rgba(255,255,255,0.6); text-decoration: none; }
        .al-hospital-change:hover { color: white; }

        /* ── RIGHT PANEL ── */
        .auth-right {
            width: 440px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            background: #F7F8FA;
        }
        .auth-form-wrap { width: 100%; }
        .ar-title { font-size: 22px; font-weight: 800; color: #1A1D23; margin-bottom: 4px; }
        .ar-sub   { font-size: 13px; color: #6B7280; margin-bottom: 28px; }

        /* Demo chips */
        .demo-chips { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; margin-bottom: 20px; }
        .demo-chip {
            text-align: center; padding: 9px 8px;
            border-radius: 9px; border: 1px solid rgba(0,0,0,0.08);
            background: white; cursor: pointer; transition: all 0.15s;
            font-size: 11px; font-weight: 700; color: #6B7280;
        }
        .demo-chip:hover { border-color: #0F6E56; color: #0F6E56; background: #E1F5EE; }
        .demo-chip-icon { font-size: 18px; display: block; margin-bottom: 4px; }
        .demo-label { display: block; font-size: 9px; color: #9CA3AF; margin-top: 2px; font-weight: 400; }

        /* Form elements */
        .f-group { margin-bottom: 16px; }
        .f-label {
            font-size: 12px; font-weight: 700; color: #1A1D23;
            margin-bottom: 6px; display: flex;
            align-items: center; justify-content: space-between;
        }
        .f-label a { font-size: 11px; font-weight: 500; color: #0F6E56; text-decoration: none; }
        .f-label a:hover { text-decoration: underline; }
        .f-input {
            width: 100%; padding: 10px 13px;
            border: 1.5px solid rgba(0,0,0,0.1);
            border-radius: 9px; font-size: 13px;
            font-family: inherit; color: #1A1D23;
            background: white; outline: none;
            transition: all 0.15s; appearance: none;
        }
        .f-input:hover { border-color: rgba(0,0,0,0.18); }
        .f-input:focus { border-color: #0F6E56; box-shadow: 0 0 0 3px #E1F5EE; }
        .f-input.error { border-color: #DC2626; box-shadow: 0 0 0 3px #FEE2E2; }
        .f-pw-wrap { position: relative; }
        .f-pw-wrap .f-input { padding-right: 42px; }
        .f-pw-toggle {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #9CA3AF; padding: 3px; display: flex;
        }
        .f-pw-toggle svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 2; }
        .f-check { display: flex; align-items: center; gap: 9px; cursor: pointer; }
        .f-check input { width: 15px; height: 15px; accent-color: #0F6E56; cursor: pointer; }
        .f-check-text { font-size: 13px; color: #374151; }
        .f-error { font-size: 11px; color: #DC2626; margin-top: 5px; }
        .btn-submit {
            width: 100%; padding: 11px;
            background: #0F6E56; color: white;
            border: none; border-radius: 9px;
            font-size: 14px; font-weight: 700;
            font-family: inherit; cursor: pointer;
            transition: all 0.15s; margin-top: 4px;
        }
        .btn-submit:hover { background: #0D5E48; }

        .divider { display: flex; align-items: center; gap: 12px; margin: 18px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(0,0,0,0.08); }
        .divider span { font-size: 11px; color: #9CA3AF; }

        .ar-footer { text-align: center; font-size: 13px; color: #6B7280; }
        .ar-footer a { color: #0F6E56; font-weight: 700; text-decoration: none; }
        .ar-display { text-align: center; margin-top: 14px; }
        .ar-display a {
            font-size: 12px; color: #9CA3AF; text-decoration: none;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .ar-display a:hover { color: #6B7280; }
        .ar-display svg { width: 13px; height: 13px; fill: none; stroke: currentColor; stroke-width: 2; }

        .alert-e { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
        .alert-s { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }

        @media (max-width: 768px) {
            .auth-left { display: none; }
            .auth-right { width: 100%; padding: 32px 20px; }
        }
    </style>
</head>
<body>

{{-- LEFT --}}
<div class="auth-left">
    <div class="al-brand">
        <div class="al-brand-logo">
            <div class="al-brand-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            </div>
            <span class="al-brand-name">Medique</span>
        </div>
        <div class="al-brand-sub">Built for Health Services</div>
    </div>

    <div class="al-content">
        <div class="al-headline">Antrian Lebih Cerdas,<br>Pelayanan Lebih Baik</div>
        <div class="al-desc">
            Sistem antrian digital untuk rumah sakit dan klinik.<br>
            Efisien, transparan, dan mudah digunakan.
        </div>
        <div class="al-features">
            <div class="al-feature">
                <div class="al-feature-icon"><svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg></div>
                Booking antrian kapan saja
            </div>
            <div class="al-feature">
                <div class="al-feature-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg></div>
                Pantau posisi antrian real-time
            </div>
            <div class="al-feature">
                <div class="al-feature-icon"><svg viewBox="0 0 24 24"><polygon points="11,5 6,9 2,9 2,15 6,15 11,19 11,5"/><path d="M15.54 8.46a5 5 0 010 7.07"/></svg></div>
                Panggilan suara otomatis
            </div>
            <div class="al-feature">
                <div class="al-feature-icon"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></div>
                Multi rumah sakit
            </div>
        </div>
    </div>

    @if(isset($currentHospital) && $currentHospital)
        <div class="al-hospital">
            <div class="al-hospital-icon">{{ strtoupper(substr($currentHospital->name,0,2)) }}</div>
            <div>
                <div class="al-hospital-name">{{ $currentHospital->name }}</div>
                <div class="al-hospital-label">Rumah Sakit Terpilih</div>
            </div>
            <a href="{{ route('hospital.change') }}" class="al-hospital-change">Ganti →</a>
        </div>
    @endif
</div>

{{-- RIGHT --}}
<div class="auth-right">
    <div class="auth-form-wrap">

        <div class="ar-title">Selamat Datang 👋</div>
        <div class="ar-sub">Masuk ke akun Medique Anda</div>

        {{-- Demo accounts --}}
        <div class="demo-chips">
            <div class="demo-chip" onclick="fillDemo('admin@medique.test','password')">
                <span class="demo-chip-icon">🛡️</span>
                Admin
                <span class="demo-label">demo</span>
            </div>
            <div class="demo-chip" onclick="fillDemo('doctor@medique.test','password')">
                <span class="demo-chip-icon">👨‍⚕️</span>
                Dokter
                <span class="demo-label">demo</span>
            </div>
            <div class="demo-chip" onclick="fillDemo('patient@medique.test','password')">
                <span class="demo-chip-icon">🧑</span>
                Pasien
                <span class="demo-label">demo</span>
            </div>
        </div>

        @if($errors->any())
            <div class="alert-e">Email atau password salah. Coba lagi.</div>
        @endif
        @if(session('success'))
            <div class="alert-s">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="f-group">
                <label class="f-label">Email</label>
                <input type="email" name="email" id="loginEmail"
                       value="{{ old('email') }}"
                       class="f-input {{ $errors->has('email') ? 'error' : '' }}"
                       placeholder="email@contoh.com"
                       autocomplete="email" autofocus>
                @error('email')<div class="f-error">{{ $message }}</div>@enderror
            </div>

            <div class="f-group">
                <label class="f-label">
                    <span>Password</span>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Lupa password?</a>
                    @endif
                </label>
                <div class="f-pw-wrap">
                    <input type="password" name="password" id="loginPw"
                           class="f-input {{ $errors->has('password') ? 'error' : '' }}"
                           placeholder="••••••••"
                           autocomplete="current-password">
                    <button type="button" class="f-pw-toggle" onclick="togglePw('loginPw')">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <div class="f-group">
                <label class="f-check">
                    <input type="checkbox" name="remember">
                    <span class="f-check-text">Ingat saya selama 30 hari</span>
                </label>
            </div>

            <button type="submit" class="btn-submit">Masuk ke Akun</button>
        </form>

        <div class="divider"><span>atau</span></div>

        <div class="ar-footer">
            Pasien baru? <a href="{{ route('patient.register') }}">Daftar akun gratis</a>
        </div>

        <div class="ar-display">
            <a href="{{ route('queue.display') }}" target="_blank">
                <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                Lihat Display Antrian Live
            </a>
        </div>

    </div>
</div>

<script>
function fillDemo(e,p){document.getElementById('loginEmail').value=e;document.getElementById('loginPw').value=p;}
function togglePw(id){const i=document.getElementById(id);i.type=i.type==='password'?'text':'password';}
</script>
</body>
</html>