<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun — Medique</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body{font-family:'Inter',sans-serif;background:#F8FAFC;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px;margin:0;}
        .wrap{width:100%;max-width:460px;}
        .logo-row{text-align:center;margin-bottom:20px;}
        .logo-row a{display:inline-flex;align-items:center;gap:10px;text-decoration:none;}
        .logo-icon{width:34px;height:34px;background:#0F6E56;border-radius:8px;display:flex;align-items:center;justify-content:center;}
        .logo-icon svg{width:16px;height:16px;stroke:white;fill:none;stroke-width:2;}
        .logo-text{font-size:18px;font-weight:800;color:#0F172A;}
        .logo-text span{color:#0F6E56;}
        .box{background:white;border:1.5px solid rgba(15,23,42,.08);border-radius:14px;padding:28px;}
        .box-title{font-size:18px;font-weight:800;margin-bottom:3px;}
        .box-sub{font-size:13px;color:#64748B;margin-bottom:20px;}
        .f-group{margin-bottom:14px;}
        .f-label{font-size:12px;font-weight:700;color:#0F172A;margin-bottom:5px;display:block;}
        .f-input{width:100%;padding:9px 12px;border:1.5px solid rgba(15,23,42,.1);border-radius:8px;font-size:13px;font-family:inherit;color:#0F172A;background:white;outline:none;transition:all .12s;}
        .f-input:focus{border-color:#0F6E56;box-shadow:0 0 0 3px #ECFDF5;}
        .f-pw-wrap{position:relative;}
        .f-pw-wrap .f-input{padding-right:40px;}
        .f-pw-btn{position:absolute;right:11px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94A3B8;padding:2px;display:flex;}
        .f-pw-btn svg{width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2;}
        .strength-bar{height:3px;border-radius:2px;background:#E2E8F0;margin-top:5px;overflow:hidden;}
        .strength-fill{height:100%;border-radius:2px;transition:width .3s,background .3s;width:0%;}
        .strength-text{font-size:10px;margin-top:3px;color:#94A3B8;}
        .match-ok{font-size:11px;color:#059669;margin-top:3px;}
        .match-err{font-size:11px;color:#EF4444;margin-top:3px;}
        .err-msg{font-size:11px;color:#EF4444;margin-top:3px;}
        .check-label{display:flex;align-items:flex-start;gap:9px;cursor:pointer;font-size:13px;color:#374151;line-height:1.5;}
        .check-label input{width:15px;height:15px;accent-color:#0F6E56;cursor:pointer;margin-top:1px;flex-shrink:0;}
        .btn-submit{width:100%;padding:11px;background:#0F6E56;color:white;border:none;border-radius:8px;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer;transition:all .12s;margin-top:4px;}
        .btn-submit:hover{background:#0D5E48;}
        .footer{text-align:center;margin-top:14px;font-size:13px;color:#64748B;}
        .footer a{color:#0F6E56;font-weight:700;text-decoration:none;}
        .err-box{background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:14px;}
    </style>
</head>
<body>
<div class="wrap">
    <div class="logo-row">
        <a href="{{ route('login') }}">
            <div class="logo-icon"><svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
            <span class="logo-text">Medi<span>que</span></span>
        </a>
    </div>

    <div class="box">
        <div class="box-title">Daftar Akun Pasien</div>
        <div class="box-sub">Buat akun gratis untuk booking antrian online</div>

        @if($errors->any())
            <div class="err-box">
                @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('patient.register.store') }}">
            @csrf

            <div class="f-group">
                <label class="f-label">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="f-input"
                       placeholder="Nama lengkap Anda" autofocus>
            </div>

            <div class="f-group">
                <label class="f-label">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" class="f-input"
                       placeholder="email@contoh.com">
            </div>

            <div class="f-group">
                <label class="f-label">Nomor HP *</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" class="f-input"
                       placeholder="08123456789">
            </div>

            <div class="f-group">
                <label class="f-label">Password *</label>
                <div class="f-pw-wrap">
                    <input type="password" name="password" id="pw" class="f-input"
                           placeholder="Min. 8 karakter" oninput="checkStr(this.value)">
                    <button type="button" class="f-pw-btn" onclick="tgl('pw')">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <div class="strength-bar"><div class="strength-fill" id="sBar"></div></div>
                <div class="strength-text" id="sText">Masukkan password</div>
            </div>

            <div class="f-group">
                <label class="f-label">Konfirmasi Password *</label>
                <div class="f-pw-wrap">
                    <input type="password" name="password_confirmation" id="pwc" class="f-input"
                           placeholder="Ulangi password" oninput="checkMatch()">
                    <button type="button" class="f-pw-btn" onclick="tgl('pwc')">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <div id="matchMsg"></div>
            </div>

            <div class="f-group">
                <label class="check-label">
                    <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                    <span>Saya setuju dengan <a href="#" style="color:#0F6E56;">syarat & ketentuan</a> layanan Medique</span>
                </label>
            </div>

            <button type="submit" class="btn-submit">Buat Akun Pasien</button>
        </form>

        <div class="footer" style="margin-top:14px;">
            Sudah punya akun? <a href="{{ route('login') }}">Login sekarang</a>
        </div>
    </div>
</div>

<script>
function tgl(id){const i=document.getElementById(id);i.type=i.type==='password'?'text':'password';}
function checkStr(v){
    const b=document.getElementById('sBar'),t=document.getElementById('sText');
    let s=0;
    if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
    const l=[
        {w:'0%',c:'#E2E8F0',t:'Masukkan password',tc:'#94A3B8'},
        {w:'25%',c:'#EF4444',t:'Sangat lemah',tc:'#EF4444'},
        {w:'50%',c:'#F59E0B',t:'Lemah',tc:'#F59E0B'},
        {w:'75%',c:'#3B82F6',t:'Cukup kuat',tc:'#3B82F6'},
        {w:'100%',c:'#059669',t:'Kuat ✓',tc:'#059669'},
    ];
    const i=v.length===0?0:Math.min(s,4);
    b.style.width=l[i].w;b.style.background=l[i].c;
    t.textContent=l[i].t;t.style.color=l[i].tc;
}
function checkMatch(){
    const pw=document.getElementById('pw').value;
    const pc=document.getElementById('pwc').value;
    const m=document.getElementById('matchMsg');
    if(!pc){m.innerHTML='';return;}
    m.innerHTML=pw===pc
        ?'<div class="match-ok">✓ Password cocok</div>'
        :'<div class="match-err">✗ Password tidak cocok</div>';
}
</script>
</body>
</html>