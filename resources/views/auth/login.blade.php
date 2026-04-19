<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Medique</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: var(--bg);
            display: flex;
            min-height: 100vh;
            margin: 0;
        }

        /* ── LEFT PANEL ── */
        .auth-left {
            flex: 1;
            background: var(--brand);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            right: -80px; top: -80px;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .auth-left::after {
            content: '';
            position: absolute;
            left: -60px; bottom: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .auth-left-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
            max-width: 380px;
        }
        .auth-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            margin-bottom: 32px;
        }
        .auth-brand-icon {
            width: 40px; height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
        }
        .auth-brand-icon svg { width: 18px; height: 18px; stroke: white; fill: none; stroke-width: 2; }
        .auth-brand-name {
            font-family: 'Syne', sans-serif;
            font-size: 24px; font-weight: 800; color: white;
        }
        .auth-headline {
            font-family: 'Syne', sans-serif;
            font-size: 26px; font-weight: 800;
            margin-bottom: 12px; line-height: 1.25;
        }
        .auth-desc { font-size: 14px; opacity: 0.8; line-height: 1.7; }
        .auth-features { margin-top: 28px; display: flex; flex-direction: column; gap: 10px; text-align: left; }
        .auth-feature {
            display: flex; align-items: center; gap: 10px;
            font-size: 13px; opacity: 0.9;
        }
        .auth-feature svg { width: 15px; height: 15px; fill: none; stroke: white; stroke-width: 2.5; flex-shrink: 0; }

        /* ── RIGHT PANEL ── */
        .auth-right {
            width: 460px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: var(--bg);
        }
        .auth-form-wrap { width: 100%; }
        .auth-title {
            font-family: 'Syne', sans-serif;
            font-size: 22px; font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }
        .auth-sub { font-size: 13px; color: var(--text2); margin-bottom: 24px; }

        /* ── DEMO CHIPS ── */
        .demo-chips {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }
        .demo-chip {
            text-align: center;
            padding: 9px 8px;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: var(--surface);
            cursor: pointer;
            transition: all 0.15s;
            font-size: 11px;
            font-weight: 600;
            color: var(--text2);
        }
        .demo-chip:hover {
            border-color: var(--brand);
            color: var(--brand);
            background: var(--brand-light);
        }
        .demo-chip-icon { font-size: 20px; margin-bottom: 4px; display: block; }
        .demo-label { display: block; font-size: 10px; color: var(--text3); margin-top: 2px; font-weight: 400; }

        /* ── MISC ── */
        .pw-wrap { position: relative; }
        .pw-wrap input { padding-right: 40px; }
        .pw-toggle {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: var(--text3); padding: 2px;
            display: flex; align-items: center;
        }
        .pw-toggle:hover { color: var(--text); }
        .pw-toggle svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 2; }

        .auth-divider {
            display: flex; align-items: center; gap: 12px;
            margin: 16px 0; font-size: 11px; color: var(--text3);
        }
        .auth-divider::before, .auth-divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }
        .auth-footer {
            text-align: center; font-size: 13px; color: var(--text2);
        }
        .auth-footer a { color: var(--brand); font-weight: 600; }

        @media (max-width: 768px) {
            .auth-left { display: none; }
            .auth-right { width: 100%; padding: 28px 20px; }
        }
    </style>
</head>
<body>

{{-- LEFT --}}
<div class="auth-left">
    <div class="auth-left-content">
        <div class="auth-brand">
            <div class="auth-brand-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            </div>
            <span class="auth-brand-name">Medique</span>
        </div>
        <div class="auth-headline">Sistem Antrian Online<br>Fasilitas Kesehatan</div>
        <div class="auth-desc">
            Booking antrian dokter kapan saja, pantau posisi antrian secara real-time,
            dan dapatkan notifikasi saat giliran Anda tiba.
        </div>
        <div class="auth-features">
            <div class="auth-feature">
                <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                Booking antrian dari mana saja
            </div>
            <div class="auth-feature">
                <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                Pantau posisi antrian live
            </div>
            <div class="auth-feature">
                <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                Panggilan suara otomatis
            </div>
            <div class="auth-feature">
                <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                Riwayat kunjungan tersimpan
            </div>
        </div>
    </div>
</div>

{{-- RIGHT --}}
<div class="auth-right">
    <div class="auth-form-wrap">

        <div class="auth-title">Selamat Datang 👋</div>
        <div class="auth-sub">Masuk ke akun Medique Anda</div>

        {{-- Demo Quick Fill --}}
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

        {{-- Error --}}
        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom:16px;">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>Email atau password salah. Coba lagi.</span>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success" style="margin-bottom:16px;">
                <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email"
                       name="email"
                       id="loginEmail"
                       value="{{ old('email') }}"
                       class="form-control {{ $errors->has('email') ? 'is-error' : '' }}"
                       placeholder="email@contoh.com"
                       autocomplete="email"
                       autofocus>
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label class="form-label" style="display:flex;justify-content:space-between;align-items:center;">
                    <span>Password</span>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="font-size:11px;color:var(--brand);font-weight:500;">Lupa password?</a>
                    @endif
                </label>
                <div class="pw-wrap">
                    <input type="password"
                           name="password"
                           id="loginPw"
                           class="form-control {{ $errors->has('password') ? 'is-error' : '' }}"
                           placeholder="••••••••"
                           autocomplete="current-password">
                    <button type="button" class="pw-toggle" onclick="togglePw('loginPw')">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Remember --}}
            <div class="form-group">
                <label class="check-label">
                    <input type="checkbox" name="remember">
                    <span class="check-text">Ingat saya selama 30 hari</span>
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;font-size:14px;">
                Masuk ke Akun
            </button>

        </form>

        <div class="auth-divider">atau</div>

        {{-- Register Link --}}
        <div class="auth-footer">
            Pasien baru? <a href="{{ route('patient.register') }}">Daftar akun gratis</a>
        </div>

        {{-- Display Link --}}
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);text-align:center;">
            <a href="{{ route('queue.display') }}"
               target="_blank"
               style="font-size:12px;color:var(--text3);display:inline-flex;align-items:center;gap:5px;text-decoration:none;">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/>
                    <line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
                Lihat Display Antrian Live
            </a>
        </div>

    </div>
</div>

<script>
function fillDemo(email, pw) {
    document.getElementById('loginEmail').value = email;
    document.getElementById('loginPw').value = pw;
}

function togglePw(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>