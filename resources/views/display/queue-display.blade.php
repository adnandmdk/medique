<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Display Antrian — {{ $clinic->name ?? 'Medique' }}</title>
    <style>
        :root {
            --brand: #0F6E56; --brand-light: #E1F5EE;
            --bg: #0D1117; --surface: #161B22; --surface2: #21262D;
            --border: rgba(255,255,255,0.08); --text: #E6EDF3;
            --text2: #8B949E; --text3: #6E7681;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: var(--bg); color: var(--text);
            height: 100vh; overflow: hidden; display: flex; flex-direction: column;
        }

        /* HEADER */
        .tv-header {
            background: var(--surface); border-bottom: 1px solid var(--border);
            padding: 12px 28px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
        }
        .tv-logo { display: flex; align-items: center; gap: 10px; }
        .tv-logo-icon { width: 32px; height: 32px; background: var(--brand); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .tv-logo-icon svg { width: 15px; height: 15px; stroke: white; fill: none; stroke-width: 2; }
        .tv-logo-name { font-size: 18px; font-weight: 800; }
        .tv-logo-name span { color: var(--brand); }
        .tv-clinic { font-size: 12px; color: var(--text2); margin-top: 2px; }
        .live-pill { display: flex; align-items: center; gap: 6px; background: rgba(220,38,38,0.12); color: #F87171; border: 1px solid rgba(220,38,38,0.25); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.5px; }
        .live-dot { width: 6px; height: 6px; border-radius: 50%; background: #F87171; animation: blink 1.5s infinite; }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.2;} }
        .clock-time { font-size: 20px; font-weight: 800; text-align: right; line-height: 1; }
        .clock-date { font-size: 11px; color: var(--text2); margin-top: 2px; text-align: right; }

        /* GRID */
        .tv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 16px 20px; flex: 1; min-height: 0; }

        /* CURRENT */
        .current-panel {
            background: var(--brand); border-radius: 18px; padding: 32px 36px;
            display: flex; flex-direction: column; justify-content: center;
            position: relative; overflow: hidden;
        }
        .current-panel::before { content: ''; position: absolute; right: -60px; top: -60px; width: 250px; height: 250px; border-radius: 50%; background: rgba(255,255,255,0.05); }
        .current-panel::after  { content: ''; position: absolute; right: 50px; bottom: -70px; width: 160px; height: 160px; border-radius: 50%; background: rgba(255,255,255,0.04); }
        .now-label { font-size: 11px; font-weight: 800; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; position: relative; z-index: 1; }
        .now-num { font-size: clamp(72px, 10vw, 120px); font-weight: 800; line-height: 1; color: white; margin-bottom: 8px; position: relative; z-index: 1; }
        .now-name { font-size: 20px; font-weight: 700; color: rgba(255,255,255,0.9); margin-bottom: 4px; position: relative; z-index: 1; }

        /* Info ruangan + dokter + jam */
        .now-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 20px; position: relative; z-index: 1; }
        .now-meta-item { background: rgba(255,255,255,0.12); border-radius: 10px; padding: 10px 14px; }
        .now-meta-label { font-size: 9px; font-weight: 800; opacity: 0.6; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
        .now-meta-val { font-size: 14px; font-weight: 800; color: white; }

        .call-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: white; color: var(--brand); border: none;
            padding: 11px 22px; border-radius: 10px;
            font-size: 13px; font-weight: 800; cursor: pointer;
            font-family: inherit; transition: all 0.15s;
            width: fit-content; margin-top: 20px; position: relative; z-index: 1;
        }
        .call-btn:hover { background: #f0fdf4; }
        .call-btn:disabled { opacity: 0.6; cursor: not-allowed; }
        .call-btn svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2; }
        .calling-state { display: none; align-items: center; gap: 10px; color: white; font-size: 13px; font-weight: 700; position: relative; z-index: 1; margin-top: 16px; }
        .sound-bars { display: flex; align-items: flex-end; gap: 3px; height: 18px; }
        .sound-bars span { width: 4px; border-radius: 2px; background: white; animation: sw 0.8s ease-in-out infinite alternate; }
        .sound-bars span:nth-child(1){height:5px;animation-delay:0s;} .sound-bars span:nth-child(2){height:13px;animation-delay:.1s;} .sound-bars span:nth-child(3){height:9px;animation-delay:.2s;} .sound-bars span:nth-child(4){height:16px;animation-delay:.3s;} .sound-bars span:nth-child(5){height:7px;animation-delay:.4s;}
        @keyframes sw { from{transform:scaleY(0.4);} to{transform:scaleY(1.2);} }

        /* RIGHT */
        .right-col { display: flex; flex-direction: column; gap: 14px; }
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .stat-blk { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 14px; text-align: center; }
        .stat-blk-val { font-size: 30px; font-weight: 800; line-height: 1; }
        .stat-blk-lbl { font-size: 10px; color: var(--text3); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }

        /* NEXT LIST */
        .next-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; flex: 1; display: flex; flex-direction: column; }
        .next-head { padding: 12px 18px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .next-head-title { font-size: 11px; font-weight: 800; color: var(--text2); text-transform: uppercase; letter-spacing: 0.5px; }
        .next-list { padding: 6px; overflow-y: auto; flex: 1; }
        .next-row { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 9px; transition: background 0.15s; }
        .next-row:hover { background: var(--surface2); }
        .next-num { font-size: 22px; font-weight: 800; color: var(--brand); min-width: 52px; }
        .next-info { flex: 1; }
        .next-patient { font-size: 13px; font-weight: 700; color: var(--text); }
        .next-sub { font-size: 11px; color: var(--text3); margin-top: 1px; }
        .spill { font-size: 10px; font-weight: 700; padding: 2px 9px; border-radius: 10px; white-space: nowrap; }
        .sp-w { background: rgba(217,119,6,0.12); color: #FCD34D; }
        .sp-c { background: rgba(37,99,235,0.12); color: #93C5FD; }
        .sp-p { background: rgba(124,58,237,0.12); color: #C4B5FD; }

        /* TICKER */
        .ticker { background: var(--surface); border-top: 1px solid var(--border); padding: 0 20px; height: 36px; display: flex; align-items: center; gap: 16px; flex-shrink: 0; overflow: hidden; }
        .ticker-badge { background: var(--brand); color: white; padding: 2px 10px; border-radius: 4px; font-size: 10px; font-weight: 800; white-space: nowrap; flex-shrink: 0; letter-spacing: 0.5px; }
        .ticker-track { overflow: hidden; flex: 1; }
        .ticker-inner { display: flex; gap: 60px; white-space: nowrap; animation: tickermove 40s linear infinite; font-size: 12px; color: var(--text2); }
        @keyframes tickermove { from{transform:translateX(0);} to{transform:translateX(-50%);} }

        /* VOICE MODAL */
        .vm-overlay { display: none; position: fixed; inset: 0; z-index: 99; background: rgba(0,0,0,0.75); align-items: center; justify-content: center; }
        .vm-overlay.show { display: flex; }
        .vm-card { background: var(--surface); border: 1px solid var(--border); border-radius: 20px; padding: 36px 32px; text-align: center; max-width: 340px; width: 100%; }
        .vm-rings { width: 90px; height: 90px; margin: 0 auto 20px; position: relative; display: flex; align-items: center; justify-content: center; }
        .vm-ring { position: absolute; border-radius: 50%; border: 2px solid var(--brand); animation: rp 2s ease-out infinite; }
        .vm-ring:nth-child(1){width:90px;height:90px;animation-delay:0s;} .vm-ring:nth-child(2){width:66px;height:66px;animation-delay:.4s;} .vm-ring:nth-child(3){width:44px;height:44px;animation-delay:.8s;}
        @keyframes rp { 0%{opacity:1;transform:scale(0.6);} 100%{opacity:0;transform:scale(1.4);} }
        .vm-icon { width: 42px; height: 42px; background: var(--brand); border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1; }
        .vm-icon svg { width: 18px; height: 18px; stroke: white; fill: none; stroke-width: 2; }
        .vm-title { font-size: 17px; font-weight: 800; color: var(--text); margin-bottom: 6px; }
        .vm-num { font-size: 52px; font-weight: 800; color: var(--brand); line-height: 1; margin: 8px 0; }
        .vm-sub { font-size: 13px; color: var(--text2); line-height: 1.6; }
        .vm-close { margin-top: 18px; background: var(--surface2); color: var(--text2); border: 1px solid var(--border); padding: 8px 22px; border-radius: 8px; cursor: pointer; font-family: inherit; font-size: 13px; font-weight: 600; }
    </style>
</head>
<body>

<header class="tv-header">
    <div class="tv-logo">
        <div class="tv-logo-icon"><svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
        <div>
            <div class="tv-logo-name">Medi<span>que</span></div>
            <div class="tv-clinic">{{ $clinic->name ?? 'Semua Poliklinik' }}</div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:14px;">
        @if(isset($clinics) && $clinics->count() > 1)
            <select onchange="window.location='/display/'+this.value" style="background:var(--surface2);border:1px solid var(--border);color:var(--text2);padding:5px 10px;border-radius:7px;font-size:12px;font-family:inherit;outline:none;cursor:pointer;">
                <option value="">Semua</option>
                @foreach($clinics as $c)
                    <option value="{{ $c->id }}" {{ $clinic->id===$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        @endif
        <div class="live-pill"><div class="live-dot"></div>LIVE</div>
        <div>
            <div class="clock-time" id="tvClock">--:--:--</div>
            <div class="clock-date" id="tvDate">—</div>
        </div>
    </div>
</header>

<div class="tv-grid">

    {{-- LEFT: CURRENT --}}
    <div class="current-panel">
        <div class="now-label">🔔 Sedang Dipanggil</div>

        @if(isset($currentQueue) && $currentQueue)
            @php
                $calledLog = optional($currentQueue->logs->where('action','called')->last());
                $calledTime = optional($calledLog)->timestamp;
            @endphp
            <div class="now-num" id="tvNum" style="font-size:60px;">#{{ $currentQueue->queue_number }}</div>
            <div class="now-name" id="tvName">{{ optional($currentQueue->patient)->name ?? '—' }}</div>

            {{-- Meta: Ruangan, Dokter, Jam Dipanggil --}}
            <div class="now-meta">
                <div class="now-meta-item">
                    <div class="now-meta-label">Ruangan</div>
                    <div class="now-meta-val">{{ optional(optional(optional($currentQueue->schedule)->doctor)->clinic)->name ?? '—' }}</div>
                </div>
                <div class="now-meta-item">
                    <div class="now-meta-label">Dokter</div>
                    <div class="now-meta-val">{{ optional(optional($currentQueue->schedule)->doctor)->user->name ?? '—' }}</div>
                </div>
                <div class="now-meta-item">
                    <div class="now-meta-label">Jam Dipanggil</div>
                    <div class="now-meta-val" id="calledTime">{{ $calledTime ? $calledTime->format('H:i') : '--:--' }}</div>
                </div>
                <div class="now-meta-item">
                    <div class="now-meta-label">No. Antrian</div>
                    <div class="now-meta-val">#{{ $currentQueue->queue_number }}</div>
                </div>
            </div>

            <button class="call-btn" id="tvCallBtn"
                onclick="callPatient(
                    '{{ $currentQueue->queue_number }}',
                    '{{ addslashes(optional($currentQueue->patient)->name ?? '') }}',
                    '{{ optional(optional(optional($currentQueue->schedule)->doctor)->clinic)->name ?? 'ruang pemeriksaan' }}',
                    '{{ optional(optional($currentQueue->schedule)->doctor)->user->name ?? 'dokter' }}',
                    '{{ $currentQueue->id }}'
                )">
                <svg viewBox="0 0 24 24"><polygon points="11,5 6,9 2,9 2,15 6,15 11,19 11,5"/><path d="M15.54 8.46a5 5 0 010 7.07"/><path d="M19.07 4.93a10 10 0 010 14.14"/></svg>
                Panggil dengan Suara
            </button>
            <div class="calling-state" id="tvCallingState">
                <div class="sound-bars"><span></span><span></span><span></span><span></span><span></span></div>
                Sedang memanggil...
            </div>
        @else
            <div class="now-num" style="opacity:0.2;">—</div>
            <div style="color:rgba(255,255,255,0.45);font-size:14px;position:relative;z-index:1;">Belum ada antrian aktif hari ini</div>
        @endif
    </div>

    {{-- RIGHT --}}
    <div class="right-col">
        <div class="stats-row">
            <div class="stat-blk"><div class="stat-blk-val" style="color:#FCD34D;" id="tvWaiting">{{ $stats['waiting'] ?? 0 }}</div><div class="stat-blk-lbl">Menunggu</div></div>
            <div class="stat-blk"><div class="stat-blk-val" style="color:#6EE7B7;" id="tvDone">{{ $stats['done'] ?? 0 }}</div><div class="stat-blk-lbl">Selesai</div></div>
            <div class="stat-blk"><div class="stat-blk-val" style="color:var(--text);" id="tvTotal">{{ $stats['total'] ?? 0 }}</div><div class="stat-blk-lbl">Total</div></div>
        </div>

        <div class="next-card">
            <div class="next-head">
                <span class="next-head-title">Antrian Berikutnya</span>
                <span style="font-size:11px;color:var(--text3);" id="tvNextCount">{{ $nextQueues->count() }}</span>
            </div>
            <div class="next-list" id="tvNextList">
                @forelse($nextQueues as $queue)
                    @php $pc = match($queue->status){'called'=>'sp-c','in_progress'=>'sp-p',default=>'sp-w'}; $pl = match($queue->status){'called'=>'Dipanggil','in_progress'=>'Dilayani',default=>'Menunggu'}; @endphp
                    <div class="next-row">
                        <div class="next-num">#{{ $queue->queue_number }}</div>
                        <div class="next-info">
                            <div class="next-patient">{{ optional($queue->patient)->name ?? '—' }}</div>
                            <div class="next-sub">Booking {{ $queue->booking_date->format('d/m/Y') }}</div>
                        </div>
                        <span class="spill {{ $pc }}">{{ $pl }}</span>
                    </div>
                @empty
                    <div style="text-align:center;padding:36px;color:var(--text3);font-size:13px;">Tidak ada antrian</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="ticker">
    <span class="ticker-badge">INFO</span>
    <div class="ticker-track">
        <div class="ticker-inner">
            @php $msg='Harap perhatikan layar antrian. &nbsp;•&nbsp; Pasien yang dipanggil 3 kali tidak hadir akan dilewati. &nbsp;•&nbsp; Bawa kartu identitas dan BPJS. &nbsp;•&nbsp; Dilarang merokok. &nbsp;•&nbsp; Terima kasih. &nbsp;&nbsp;&nbsp;&nbsp;'; @endphp
            <span>{!! $msg !!}</span><span>{!! $msg !!}</span>
        </div>
    </div>
</div>

{{-- VOICE MODAL --}}
<div class="vm-overlay" id="vmOverlay">
    <div class="vm-card">
        <div class="vm-rings"><div class="vm-ring"></div><div class="vm-ring"></div><div class="vm-ring"></div><div class="vm-icon"><svg viewBox="0 0 24 24"><polygon points="11,5 6,9 2,9 2,15 6,15 11,19 11,5"/><path d="M15.54 8.46a5 5 0 010 7.07"/></svg></div></div>
        <div class="vm-title">Memanggil Pasien</div>
        <div class="vm-num" id="vmNum">#0</div>
        <div class="vm-sub" id="vmSub">—</div>
        <button class="vm-close" onclick="closeVM()">Tutup</button>
    </div>
</div>

<script>
// CLOCK
const DAYS=['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const MONS=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
function tick(){
    const n=new Date(), p=x=>String(x).padStart(2,'0');
    document.getElementById('tvClock').textContent=p(n.getHours())+':'+p(n.getMinutes())+':'+p(n.getSeconds());
    document.getElementById('tvDate').textContent=DAYS[n.getDay()]+', '+n.getDate()+' '+MONS[n.getMonth()]+' '+n.getFullYear();
}
tick(); setInterval(tick,1000);

// VOICE
let speaking=false;
function callPatient(num, name, room, docName, queueId){
    if(speaking) return;
    speaking=true;
    document.getElementById('vmNum').textContent='#'+num;
    document.getElementById('vmSub').innerHTML='<strong>'+name+'</strong><br>Menuju: '+room+'<br>Dokter: '+docName;
    document.getElementById('vmOverlay').classList.add('show');
    const btn=document.getElementById('tvCallBtn');
    const anim=document.getElementById('tvCallingState');
    if(btn){btn.disabled=true;}
    if(anim){anim.style.display='flex';}

    if('speechSynthesis' in window){
        window.speechSynthesis.cancel();
        // Panggil 3 kali dengan script lengkap termasuk ruangan
        const scripts=[
            `Nomor antrian ${num}, atas nama ${name}, silakan menuju ${room}, dokter ${docName}.`,
            `Nomor antrian ${num}, ${name}, ${room}.`,
            `Nomor antrian ${num}.`,
        ];
        let idx=0;
        function speak(){
            if(idx>=scripts.length){finish(btn,anim);return;}
            const u=new SpeechSynthesisUtterance(scripts[idx]);
            u.lang='id-ID'; u.rate=0.88; u.pitch=1.0; u.volume=1.0;
            u.onend=()=>{idx++;setTimeout(speak,900);};
            u.onerror=()=>finish(btn,anim);
            window.speechSynthesis.speak(u);
        }
        speak();
    } else {
        alert('Gunakan Chrome/Edge untuk Text-to-Speech.');
        finish(btn,anim);
    }

    fetch('/doctor/queues/'+queueId+'/call',{method:'PATCH',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}}).catch(()=>{});
}
function finish(btn,anim){
    speaking=false;
    if(btn){btn.disabled=false;}
    if(anim){anim.style.display='none';}
}
function closeVM(){
    document.getElementById('vmOverlay').classList.remove('show');
    window.speechSynthesis&&window.speechSynthesis.cancel();
    finish(document.getElementById('tvCallBtn'),document.getElementById('tvCallingState'));
}

// AUTO REFRESH
setInterval(()=>{
    fetch(window.location.href,{headers:{'X-Requested-With':'XMLHttpRequest'}})
    .then(r=>r.text()).then(html=>{
        const doc=new DOMParser().parseFromString(html,'text/html');
        ['tvNextList','tvWaiting','tvDone','tvTotal','tvNextCount'].forEach(id=>{
            const f=doc.getElementById(id),c=document.getElementById(id);
            if(f&&c)c.innerHTML=f.innerHTML;
        });
    }).catch(()=>{});
},20000);

</script>
</body>
</html>