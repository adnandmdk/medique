<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Medique</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body{font-family:'Inter',sans-serif;background:#F8FAFC;display:flex;min-height:100vh;margin:0;}
        .al{flex:1;background:#0F6E56;display:flex;flex-direction:column;justify-content:space-between;padding:48px;position:relative;overflow:hidden;}
        .al::before{content:'';position:absolute;right:-80px;top:-80px;width:320px;height:320px;border-radius:50%;background:rgba(255,255,255,0.04);}
        .al::after{content:'';position:absolute;left:-60px;bottom:-80px;width:240px;height:240px;border-radius:50%;background:rgba(255,255,255,0.04);}
        .al-brand{position:relative;z-index:1;}
        .al-logo{display:flex;align-items:center;gap:12px;margin-bottom:2px;}
        .al-logo-icon{width:40px;height:40px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;}
        .al-logo-icon svg{width:20px;height:20px;stroke:white;fill:none;stroke-width:2;}
        .al-logo-text{font-size:24px;font-weight:800;color:white;letter-spacing:-.5px;}
        .al-tagline{font-size:11px;color:rgba(255,255,255,0.55);font-weight:500;letter-spacing:.3px;margin-left:52px;}
        .al-content{position:relative;z-index:1;}
        .al-h1{font-size:28px;font-weight:800;color:white;line-height:1.25;margin-bottom:12px;}
        .al-desc{font-size:14px;color:rgba(255,255,255,.72);line-height:1.75;margin-bottom:24px;}
        .al-feat{display:flex;flex-direction:column;gap:10px;}
        .al-feat-item{display:flex;align-items:center;gap:10px;font-size:13px;color:rgba(255,255,255,.85);}
        .al-feat-item svg{width:16px;height:16px;stroke:white;fill:none;stroke-width:2;}
        .ar{width:440px;flex-shrink:0;display:flex;align-items:center;justify-content:center;padding:48px 40px;background:#F8FAFC;}
        .ar-wrap{width:100%;}
        .ar-title{font-size:22px;font-weight:800;color:#0F172A;margin-bottom:4px;}
        .ar-sub{font-size:13px;color:#64748B;margin-bottom:24px;}
        .demo-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:18px;}
        .demo-chip{text-align:center;padding:9px 6px;border-radius:8px;border:1.5px solid rgba(15,23,42,.1);background:white;cursor:pointer;transition:all .12s;font-family:inherit;}
        .demo-chip:hover{border-color:#0F6E56;background:#ECFDF5;}
        .demo-chip-icon{font-size:18px;display:block;margin-bottom:3px;}
        .demo-chip-label{font-size:11px;font-weight:700;color:#475569;display:block;}
        .demo-chip-hint{font-size:9px;color:#94A3B8;display:block;margin-top:1px;}
        .f-group{margin-bottom:14px;}
        .f-label{font-size:12px;font-weight:700;color:#0F172A;margin-bottom:5px;display:flex;align-items:center;justify-content:space-between;}
        .f-label a{font-size:11px;font-weight:500;color:#0F6E56;text-decoration:none;}
        .f-input{width:100%;padding:10px 12px;border:1.5px solid rgba(15,23,42,.1);border-radius:8px;font-size:13px;font-family:inherit;color:#0F172A;background:white;outline:none;transition:all .12s;}
        .f-input:focus{border-color:#0F6E56;box-shadow:0 0 0 3px #ECFDF5;}
        .f-pw-wrap{position:relative;}
        .f-pw-wrap .f-input{padding-right:40px;}
        .f-pw-btn{position:absolute;right:11px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94A3B8;padding:2px;display:flex;}
        .f-pw-btn svg{width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2;}
        .f-check{display:flex;align-items:center;gap:9px;cursor:pointer;font-size:13px;color:#374151;}
        .f-check input{width:15px;height:15px;accent-color:#0F6E56;cursor:pointer;}
        .btn-login{width:100%;padding:11px;background:#0F6E56;color:white;border:none;border-radius:8px;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer;transition:all .12s;margin-top:4px;}
        .btn-login:hover{background:#0D5E48;}
        .divider{display:flex;align-items:center;gap:12px;margin:16px 0;font-size:11px;color:#94A3B8;}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:rgba(15,23,42,.08);}
        .ar-footer{text-align:center;font-size:13px;color:#64748B;margin-top:8px;}
        .ar-footer a{color:#0F6E56;font-weight:700;text-decoration:none;}
        .ar-display{text-align:center;margin-top:12px;}
        .ar-display a{font-size:12px;color:#94A3B8;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
        .ar-display a:hover{color:#64748B;}
        .ar-display svg{width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2;}
        .err{background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:14px;}
        .suc{background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:14px;}
        @media(max-width:768px){.al{display:none;}.ar{width:100%;padding:32px 20px;}}
    </style>
</head>
<body>

<div class="al">
    <div class="al-brand">
        <div class="al-logo">
            <div class="al-logo-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            </div>
            <span class="al-logo-text">Medique</span>
        </div>
        <div class="al-tagline">Built for Health Services</div>
    </div>
    <div class="al-content">
        <div class="al-h1">Sistem Antrian<br>Rumah Sakit Modern</div>
        <div class="al-desc">Kelola antrian pasien secara digital. Efisien, transparan, dan mudah digunakan oleh seluruh pihak.</div>
        <div class="al-feat">
            <div class="al-feat-item"><svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>Booking antrian kapan saja</div>
            <div class="al-feat-item"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>Pantau posisi antrian real-time</div>
            <div class="al-feat-item"><svg viewBox="0 0 24 24"><polygon points="11,5 6,9 2,9 2,15 6,15 11,19 11,5"/><path d="M15.54 8.46a5 5 0 010 7.07"/></svg>Panggilan suara otomatis</div>
            <div class="al-feat-item"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>Multi rumah sakit</div>
        </div>
    </div>
    <div style="font-size:12px;color:rgba(255,255,255,.35);position:relative;z-index:1;">© {{ date('Y') }} Medique</div>
</div>

<div class="ar">
    <div class="ar-wrap">
        <div class="ar-title">Selamat Datang 👋</div>
        <div class="ar-sub">Masuk ke akun Medique Anda</div>

        <div class="demo-grid">
            <div class="demo-chip" onclick="fill('admin@medique.test','password')">
                <span class="demo-chip-icon">🛡️</span>
                <span class="demo-chip-label">Admin</span>
                <span class="demo-chip-hint">demo</span>
            </div>
            <div class="demo-chip" onclick="fill('doctor@medique.test','password')">
                <span class="demo-chip-icon">👨‍⚕️</span>
                <span class="demo-chip-label">Dokter</span>
                <span class="demo-chip-hint">demo</span>
            </div>
            <div class="demo-chip" onclick="fill('patient@medique.test','password')">
                <span class="demo-chip-icon">🧑</span>
                <span class="demo-chip-label">Pasien</span>
                <span class="demo-chip-hint">demo</span>
            </div>
        </div>

        @if($errors->any())
            <div class="err">Email atau password salah. Silakan coba lagi.</div>
        @endif
        @if(session('success'))
            <div class="suc">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="f-group">
                <div class="f-label">Email</div>
                <input type="email" name="email" id="fe" value="{{ old('email') }}"
                       class="f-input" placeholder="email@contoh.com" autocomplete="email" autofocus>
            </div>
            <div class="f-group">
                <div class="f-label">
                    <span>Password</span>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Lupa password?</a>
                    @endif
                </div>
                <div class="f-pw-wrap">
                    <input type="password" name="password" id="fp"
                           class="f-input" placeholder="••••••••" autocomplete="current-password">
                    <button type="button" class="f-pw-btn" onclick="tgl('fp')">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>
            <div class="f-group">
                <label class="f-check"><input type="checkbox" name="remember"><span>Ingat saya 30 hari</span></label>
            </div>
            <button type="submit" class="btn-login">Masuk ke Akun</button>
        </form>

        <div class="divider"><span>atau</span></div>
        <div class="ar-footer">Pasien baru? <a href="{{ route('patient.register') }}">Daftar gratis</a></div>
        <div class="ar-display">
            <a href="{{ route('queue.display') }}" target="_blank">
                <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                Lihat Display Antrian Live
            </a>
        </div>
    </div>
</div>

<script>
function fill(e,p){document.getElementById('fe').value=e;document.getElementById('fp').value=p;}
function tgl(id){const i=document.getElementById(id);i.type=i.type==='password'?'text':'password';}
</script>
</body>
</html>