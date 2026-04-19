<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun Pasien — Medique</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            margin: 0;
        }
        .auth-wrap { width: 100%; max-width: 480px; }
        .auth-logo {
            text-align: center;
            margin-bottom: 24px;
        }
        .auth-logo a {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .auth-logo-icon {
            width: 36px; height: 36px;
            background: var(--brand);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
        }
        .auth-logo-icon svg { width: 16px; height: 16px; stroke: white; fill: none; stroke-width: 2; }
        .auth-logo-text {
            font-family: 'Syne', sans-serif;
            font-size: 20px; font-weight: 800; color: var(--text);
        }
        .auth-logo-text span { color: var(--brand); }
        .auth-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 32px;
        }
        .auth-title {
            font-family: 'Syne', sans-serif;
            font-size: 20px; font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }
        .auth-sub { font-size: 13px; color: var(--text2); margin-bottom: 24px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .pw-wrap { position: relative; }
        .pw-wrap input { padding-right: 40px; }
        .pw-toggle {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: var(--text3); padding: 2px;
            display: flex; align-items: center;
        }
        .pw-toggle:hover { color: var(--text2); }
        .pw-toggle svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-width: 2; }
        .strength-bar {
            height: 3px; border-radius: 2px;
            background: var(--border); margin-top: 6px; overflow: hidden;
        }
        .strength-fill {
            height: 100%; border-radius: 2px;
            transition: width 0.3s, background 0.3s;
            width: 0%;
        }
        .strength-text { font-size: 10px; margin-top: 3px; color: var(--text3); }
        .auth-footer { text-align: center; margin-top: 16px; font-size: 13px; color: var(--text2); }
        .auth-footer a { color: var(--brand); font-weight: 600; }
        .match-ok  { color: var(--brand); font-size: 11px; margin-top: 4px; }
        .match-err { color: var(--accent); font-size: 11px; margin-top: 4px; }
    </style>
</head>
<body>
<div class="auth-wrap">

    {{-- Logo --}}
    <div class="auth-logo">
        <a href="{{ route('login') }}">
            <div class="auth-logo-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            </div>
            <span class="auth-logo-text">Medi<span>que</span></span>
        </a>
    </div>

    <div class="auth-card">
        <div class="auth-title">Daftar Akun Pasien</div>
        <div class="auth-sub">Buat akun gratis untuk booking antrian online</div>

        {{-- Error --}}
        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom:16px;">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('patient.register.store') }}" id="regForm">
            @csrf

            {{-- Nama --}}
            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="form-control {{ $errors->has('name') ? 'is-error' : '' }}"
                       placeholder="contoh: Budi Santoso"
                       autocomplete="name"
                       autofocus>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label">Email <span class="req">*</span></label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="form-control {{ $errors->has('email') ? 'is-error' : '' }}"
                       placeholder="email@contoh.com"
                       autocomplete="email">
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Phone --}}
            <div class="form-group">
                <label class="form-label">Nomor HP <span class="req">*</span></label>
                <input type="tel"
                       name="phone"
                       value="{{ old('phone') }}"
                       class="form-control {{ $errors->has('phone') ? 'is-error' : '' }}"
                       placeholder="08123456789">
                @error('phone')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label class="form-label">Password <span class="req">*</span></label>
                <div class="pw-wrap">
                    <input type="password"
                           name="password"
                           id="pw"
                           class="form-control {{ $errors->has('password') ? 'is-error' : '' }}"
                           placeholder="Minimal 8 karakter"
                           autocomplete="new-password"
                           oninput="checkStrength(this.value)">
                    <button type="button" class="pw-toggle" onclick="togglePw('pw')">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <div class="strength-bar"><div class="strength-fill" id="strengthBar"></div></div>
                <div class="strength-text" id="strengthText">Masukkan password</div>
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div class="form-group">
                <label class="form-label">Konfirmasi Password <span class="req">*</span></label>
                <div class="pw-wrap">
                    <input type="password"
                           name="password_confirmation"
                           id="pwConfirm"
                           class="form-control"
                           placeholder="Ulangi password"
                           autocomplete="new-password"
                           oninput="checkMatch()">
                    <button type="button" class="pw-toggle" onclick="togglePw('pwConfirm')">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <div id="matchMsg"></div>
            </div>

            {{-- Terms --}}
            <div class="form-group">
                <label class="check-label {{ $errors->has('terms') ? '' : '' }}">
                    <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                    <span class="check-text">
                        Saya setuju dengan
                        <a href="#" style="color:var(--brand);">syarat & ketentuan</a>
                        layanan Medique
                    </span>
                </label>
                @error('terms')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;font-size:14px;">
                <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Buat Akun Pasien
            </button>

        </form>

        <div class="auth-footer" style="margin-top:16px;">
            Sudah punya akun? <a href="{{ route('login') }}">Login sekarang</a>
        </div>
    </div>

    <div style="text-align:center;margin-top:14px;font-size:11px;color:var(--text3);">
        © {{ date('Y') }} Medique. Sistem Antrian Online Fasilitas Kesehatan.
    </div>

</div>

<script>
function togglePw(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function checkStrength(val) {
    const bar  = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    let score  = 0;
    if (val.length >= 8)           score++;
    if (/[A-Z]/.test(val))         score++;
    if (/[0-9]/.test(val))         score++;
    if (/[^A-Za-z0-9]/.test(val))  score++;

    const levels = [
        { w: '0%',   c: 'var(--border)', t: 'Masukkan password',  tc: 'var(--text3)' },
        { w: '25%',  c: '#DC2626',       t: 'Sangat lemah',       tc: '#DC2626' },
        { w: '50%',  c: '#D97706',       t: 'Lemah',              tc: '#D97706' },
        { w: '75%',  c: '#2563EB',       t: 'Cukup kuat',         tc: '#2563EB' },
        { w: '100%', c: 'var(--brand)',  t: 'Kuat ✓',             tc: 'var(--brand)' },
    ];
    const idx = val.length === 0 ? 0 : Math.min(score, 4);
    bar.style.width      = levels[idx].w;
    bar.style.background = levels[idx].c;
    text.textContent     = levels[idx].t;
    text.style.color     = levels[idx].tc;
}

function checkMatch() {
    const pw   = document.getElementById('pw').value;
    const conf = document.getElementById('pwConfirm').value;
    const msg  = document.getElementById('matchMsg');
    if (conf.length === 0) { msg.innerHTML = ''; return; }
    if (pw === conf) {
        msg.innerHTML = '<div class="match-ok">✓ Password cocok</div>';
    } else {
        msg.innerHTML = '<div class="match-err">✗ Password tidak cocok</div>';
    }
}
</script>
</body>
</html>