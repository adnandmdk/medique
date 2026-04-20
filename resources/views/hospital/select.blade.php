<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Rumah Sakit — Medique</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #F0FDF4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .top-bar {
            background: #0F6E56;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .top-bar-logo {
            font-size: 20px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.3px;
        }
        .top-bar-logo span { color: #9FE1CB; }
        .top-bar-sub { font-size: 11px; color: rgba(255,255,255,0.6); margin-top: 1px; }
        .main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .wrap { width: 100%; max-width: 760px; }
        .heading { text-align: center; margin-bottom: 36px; }
        .heading h1 { font-size: 26px; font-weight: 800; color: #1A1D23; margin-bottom: 6px; }
        .heading p  { font-size: 14px; color: #6B7280; }
        .hospital-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 14px;
        }
        .hospital-form { display: contents; }
        .hospital-card {
            background: white;
            border: 2px solid rgba(0,0,0,0.07);
            border-radius: 16px;
            padding: 24px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.18s;
            position: relative;
        }
        .hospital-card:hover {
            border-color: #0F6E56;
            box-shadow: 0 0 0 4px rgba(15,110,86,0.1);
            transform: translateY(-2px);
        }
        .hospital-card input[type="radio"] {
            position: absolute; opacity: 0; width: 0; height: 0;
        }
        .hospital-card input:checked + .hospital-card-inner {
            /* handled via JS */
        }
        .hospital-avatar {
            width: 60px; height: 60px;
            border-radius: 14px;
            background: #E1F5EE;
            color: #0F6E56;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800;
            margin: 0 auto 14px;
        }
        .hospital-name { font-size: 15px; font-weight: 800; color: #1A1D23; margin-bottom: 4px; }
        .hospital-addr { font-size: 12px; color: #9CA3AF; line-height: 1.4; }
        .hospital-tag  { display: inline-block; margin-top: 10px; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 10px; background: #E1F5EE; color: #0F6E56; text-transform: uppercase; letter-spacing: 0.5px; }
        .no-hospital { text-align: center; padding: 40px; color: #9CA3AF; font-size: 14px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #9CA3AF; }

        /* Alert */
        .alert { padding: 12px 16px; border-radius: 9px; font-size: 13px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-danger { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }

        @media (max-width: 480px) {
            .hospital-grid { grid-template-columns: 1fr 1fr; }
            .hospital-card { padding: 18px 12px; }
            .hospital-avatar { width: 48px; height: 48px; font-size: 16px; }
            .hospital-name { font-size: 13px; }
        }
    </style>
</head>
<body>

<div class="top-bar">
    <div>
        <div class="top-bar-logo">Medi<span>que</span></div>
        <div class="top-bar-sub">Health Services Queue System</div>
    </div>
</div>

<div class="main">
    <div class="wrap">

        <div class="heading">
            <h1>🏥 Pilih Rumah Sakit</h1>
            <p>Pilih fasilitas kesehatan yang ingin Anda kunjungi</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
        @endif

        @if($hospitals->count() > 0)
            <div class="hospital-grid">
                @foreach($hospitals as $hospital)
                    <form method="POST" action="{{ route('hospital.choose') }}">
                        @csrf
                        <input type="hidden" name="hospital_id" value="{{ $hospital->id }}">
                        <button type="submit" style="all:unset;display:block;width:100%;">
                            <div class="hospital-card">
                                <div class="hospital-avatar">
                                    @if($hospital->logo)
                                        <img src="{{ $hospital->logo }}" alt="{{ $hospital->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
                                    @else
                                        {{ strtoupper(substr($hospital->name, 0, 2)) }}
                                    @endif
                                </div>
                                <div class="hospital-name">{{ $hospital->name }}</div>
                                <div class="hospital-addr">{{ $hospital->address ?? 'Alamat belum tersedia' }}</div>
                                @if($hospital->tagline)
                                    <span class="hospital-tag">{{ $hospital->tagline }}</span>
                                @endif
                            </div>
                        </button>
                    </form>
                @endforeach
            </div>
        @else
            <div class="no-hospital">
                Belum ada rumah sakit yang terdaftar. Hubungi administrator.
            </div>
        @endif

    </div>
</div>

<div class="footer">© {{ date('Y') }} Medique — Multi Hospital Queue System</div>

</body>
</html>