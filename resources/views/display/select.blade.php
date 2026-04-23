<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Poli — Medique</title>

    <style>
        :root{
            --brand:#0F6E56;
            --bg:#0D1117;
            --card:#161B22;
            --border:rgba(255,255,255,.08);
            --text:#E6EDF3;
            --text2:#8B949E;
        }

        *{box-sizing:border-box;margin:0;padding:0}

        body{
            font-family:system-ui,-apple-system,Segoe UI,Roboto;
            background:var(--bg);
            color:var(--text);
            padding:30px;
        }

        .container{
            max-width:1100px;
            margin:auto;
        }

        .title{
            font-size:26px;
            font-weight:800;
            margin-bottom:6px;
        }

        .subtitle{
            font-size:13px;
            color:var(--text2);
            margin-bottom:24px;
        }

        .grid{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
            gap:18px;
        }

        .card{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:16px;
            padding:18px;
            transition:all .2s;
        }

        .card:hover{
            transform:translateY(-4px);
            border-color:rgba(16,185,129,.4);
            box-shadow:0 10px 30px rgba(0,0,0,.4);
        }

        .rs-header{
            display:flex;
            align-items:center;
            gap:12px;
            margin-bottom:14px;
        }

        .rs-logo{
            width:42px;
            height:42px;
            border-radius:10px;
            background:linear-gradient(135deg,#10B981,#059669);
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:800;
            color:white;
            font-size:14px;
        }

        .rs-name{
            font-size:15px;
            font-weight:700;
        }

        .poli-list{
            display:flex;
            flex-direction:column;
            gap:8px;
        }

        .poli-btn{
            display:flex;
            justify-content:space-between;
            align-items:center;
            text-decoration:none;
            padding:10px 12px;
            border-radius:10px;
            background:#0F172A;
            color:var(--text);
            font-size:13px;
            transition:all .15s;
            border:1px solid transparent;
        }

        .poli-btn:hover{
            background:rgba(16,185,129,.1);
            border-color:rgba(16,185,129,.3);
            color:#6EE7B7;
        }

        .arrow{
            font-size:12px;
            opacity:.6;
        }

        .empty{
            text-align:center;
            padding:40px;
            color:var(--text2);
        }

    </style>
</head>
<body>

<div class="container">

    <div class="title">Pilih Poliklinik</div>
    <div class="subtitle">Silakan pilih rumah sakit dan poli untuk menampilkan antrian</div>

    <div class="grid">

        @forelse($hospitals as $hospital)
            <div class="card">

                {{-- Header RS --}}
                <div class="rs-header">
                    <div class="rs-logo">
                        {{ strtoupper(substr($hospital->name,0,2)) }}
                    </div>
                    <div class="rs-name">
                        {{ $hospital->name }}
                    </div>
                </div>

                {{-- List Poli --}}
                <div class="poli-list">
                    @forelse($hospital->clinics as $clinic)
                        <a href="{{ url('/display/'.$clinic->id) }}" class="poli-btn">
                            <span>{{ $clinic->name }}</span>
                            <span class="arrow">→</span>
                        </a>
                    @empty
                        <div style="font-size:12px;color:var(--text2);">
                            Tidak ada poli aktif
                        </div>
                    @endforelse
                </div>

            </div>
        @empty
            <div class="empty">
                Tidak ada rumah sakit tersedia
            </div>
        @endforelse

    </div>

</div>

</body>
</html>