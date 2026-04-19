<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Display Antrian — {{ optional($clinic)->name ?? 'Medique' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand:       #0F6E56;
            --brand-light: #E1F5EE;
            --accent:      #D85A30;
            --bg:          #0D1117;
            --surface:     #161B22;
            --surface2:    #21262D;
            --border:      rgba(255,255,255,0.08);
            --text:        #E6EDF3;
            --text2:       #8B949E;
            --text3:       #6E7681;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow: hidden;
        }

        /* ── HEADER ── */
        .disp-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 62px;
        }
        .disp-logo { display: flex; align-items: center; gap: 10px; }
        .disp-logo-icon {
            width: 32px; height: 32px;
            background: var(--brand);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .disp-logo-icon svg { width: 15px; height: 15px; stroke: white; fill: none; stroke-width: 2; }
        .disp-logo-name {
            font-family: 'Syne', sans-serif;
            font-size: 18px; font-weight: 800; color: var(--text);
        }
        .disp-logo-name span { color: var(--brand); }
        .disp-clinic { font-size: 12px; color: var(--text2); margin-top: 1px; }
        .disp-right { display: flex; align-items: center; gap: 16px; }
        .live-pill {
            display: flex; align-items: center; gap: 6px;
            background: rgba(220,38,38,0.12);
            color: #F87171;
            border: 1px solid rgba(220,38,38,0.25);
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
            letter-spacing: 0.5px;
        }
        .live-dot {
            width: 6px; height: 6px;
            border-radius: 50%; background: #F87171;
            animation: blink 1.5s infinite;
        }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.2;} }
        .clock-wrap { text-align: right; }
        .clock-time {
            font-family: 'Syne', sans-serif;
            font-size: 20px; font-weight: 800; color: var(--text); line-height: 1;
        }
        .clock-date { font-size: 11px; color: var(--text2); margin-top: 2px; }

        /* ── MAIN GRID ── */
        .disp-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            padding: 16px 20px;
            height: calc(100vh - 62px - 36px);
        }

        /* ── CURRENT PANEL ── */
        .current-card {
            background: var(--brand);
            border-radius: 18px;
            padding: 36px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .current-card::before {
            content: '';
            position: absolute; right: -60px; top: -60px;
            width: 250px; height: 250px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .current-card::after {
            content: '';
            position: absolute; right: 50px; bottom: -70px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .now-label {
            font-size: 11px; font-weight: 600;
            opacity: 0.7; text-transform: uppercase;
            letter-spacing: 1px; margin-bottom: 10px;
            position: relative; z-index: 1;
        }
        .now-number {
            font-family: 'Syne', sans-serif;
            font-size: clamp(72px, 11vw, 128px);
            font-weight: 800; line-height: 1;
            color: white; margin-bottom: 10px;
            position: relative; z-index: 1;
        }
        .now-name {
            font-size: 20px; font-weight: 600;
            color: rgba(255,255,255,0.9);
            margin-bottom: 4px;
            position: relative; z-index: 1;
        }
        .now-spec {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            margin-bottom: 28px;
            position: relative; z-index: 1;
        }
        .call-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: white; color: var(--brand);
            border: none; padding: 11px 22px;
            border-radius: 10px;
            font-size: 14px; font-weight: 700;
            cursor: pointer; font-family: inherit;
            transition: all 0.15s;
            width: fit-content;
            position: relative; z-index: 1;
        }
        .call-btn:hover { background: #f0fdf4; transform: translateY(-1px); }
        .call-btn:active { transform: scale(0.98); }
        .call-btn svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2; }
        .call-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .calling-state {
            display: none; align-items: center; gap: 10px;
            color: white; font-size: 13px; font-weight: 600;
            position: relative; z-index: 1;
        }
        .sound-bars { display: flex; align-items: flex-end; gap: 3px; height: 18px; }
        .sound-bars span {
            width: 4px; border-radius: 2px; background: white;
            animation: soundwave 0.8s ease-in-out infinite alternate;
        }
        .sound-bars span:nth-child(1) { height: 5px;  animation-delay: 0.0s; }
        .sound-bars span:nth-child(2) { height: 13px; animation-delay: 0.1s; }
        .sound-bars span:nth-child(3) { height: 9px;  animation-delay: 0.2s; }
        .sound-bars span:nth-child(4) { height: 16px; animation-delay: 0.3s; }
        .sound-bars span:nth-child(5) { height: 7px;  animation-delay: 0.4s; }
        @keyframes soundwave {
            from { transform: scaleY(0.4); }
            to   { transform: scaleY(1.2); }
        }

        .empty-queue {
            color: rgba(255,255,255,0.45);
            font-size: 15px;
            position: relative; z-index: 1;
        }

        /* ── RIGHT PANEL ── */
        .right-col { display: flex; flex-direction: column; gap: 14px; }

        /* ── STATS ROW ── */
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .stat-block {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
            text-align: center;
        }
        .stat-block-val {
            font-family: 'Syne', sans-serif;
            font-size: 30px; font-weight: 700; line-height: 1;
        }
        .stat-block-label {
            font-size: 10px; color: var(--text3);
            text-transform: uppercase; letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* ── NEXT LIST ── */
        .next-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .next-head {
            padding: 12px 18px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .next-head-title {
            font-size: 12px; font-weight: 600;
            color: var(--text2); text-transform: uppercase; letter-spacing: 0.5px;
        }
        .next-head-count { font-size: 11px; color: var(--text3); }
        .next-list { padding: 6px; overflow-y: auto; flex: 1; }
        .next-row {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: 9px;
            transition: background 0.15s;
        }
        .next-row:hover { background: var(--surface2); }
        .next-num {
            font-family: 'Syne', sans-serif;
            font-size: 22px; font-weight: 700;
            color: var(--brand); min-width: 52px;
        }
        .next-info { flex: 1; }
        .next-patient { font-size: 13px; font-weight: 600; color: var(--text); }
        .next-sub { font-size: 11px; color: var(--text3); margin-top: 1px; }
        .status-pill {
            font-size: 10px; font-weight: 600;
            padding: 2px 9px; border-radius: 10px; white-space: nowrap;
        }
        .sp-waiting  { background: rgba(217,119,6,0.12);  color: #FCD34D; }
        .sp-called   { background: rgba(37,99,235,0.12);  color: #93C5FD; }
        .sp-progress { background: rgba(124,58,237,0.12); color: #C4B5FD; }

        /* ── TICKER ── */
        .ticker {
            position: fixed; bottom: 0; left: 0; right: 0;
            height: 36px;
            background: var(--surface);
            border-top: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 20px; gap: 16px;
            overflow: hidden;
        }
        .ticker-badge {
            background: var(--brand); color: white;
            padding: 2px 10px; border-radius: 4px;
            font-size: 10px; font-weight: 700;
            white-space: nowrap; flex-shrink: 0;
            letter-spacing: 0.5px;
        }
        .ticker-track { overflow: hidden; flex: 1; }
        .ticker-inner {
            display: flex; gap: 60px;
            white-space: nowrap;
            animation: tickermove 40s linear infinite;
            font-size: 12px; color: var(--text2);
        }
        @keyframes tickermove {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }

        /* ── VOICE MODAL ── */
        .voice-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 99;
            background: rgba(0,0,0,0.75);
            align-items: center; justify-content: center;
        }
        .voice-overlay.show { display: flex; }
        .voice-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px 32px;
            text-align: center;
            max-width: 340px; width: 100%;
        }
        .voice-rings-wrap {
            width: 90px; height: 90px;
            margin: 0 auto 20px;
            position: relative;
            display: flex; align-items: center; justify-content: center;
        }
        .voice-ring {
            position: absolute; border-radius: 50%;
            border: 2px solid var(--brand);
            animation: ringpulse 2s ease-out infinite;
        }
        .voice-ring:nth-child(1) { width: 90px; height: 90px; animation-delay: 0.0s; }
        .voice-ring:nth-child(2) { width: 66px; height: 66px; animation-delay: 0.4s; }
        .voice-ring:nth-child(3) { width: 44px; height: 44px; animation-delay: 0.8s; }
        @keyframes ringpulse {
            0%   { opacity: 1; transform: scale(0.6); }
            100% { opacity: 0; transform: scale(1.4); }
        }
        .voice-icon-center {
            width: 42px; height: 42px;
            background: var(--brand);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            position: relative; z-index: 1;
        }
        .voice-icon-center svg { width: 18px; height: 18px; stroke: white; fill: none; stroke-width: 2; }
        .voice-card-title {
            font-family: 'Syne', sans-serif;
            font-size: 17px; font-weight: 700;
            color: var(--text); margin-bottom: 6px;
        }
        .voice-card-num {
            font-family: 'Syne', sans-serif;
            font-size: 52px; font-weight: 800;
            color: var(--brand); line-height: 1; margin: 8px 0;
        }
        .voice-card-name { font-size: 14px; color: var(--text2); }
        .voice-close {
            margin-top: 18px;
            background: var(--surface2);
            color: var(--text2);
            border: 1px solid var(--border);
            padding: 8px 22px; border-radius: 8px;
            cursor: pointer; font-family: inherit;
            font-size: 13px; font-weight: 500;
        }
        .voice-close:hover { background: var(--border); }
    </style>
</head>
<body>

{{-- HEADER --}}
<header class="disp-header">
    <div class="disp-logo">
        <div class="disp-logo-icon">
            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        </div>
        <div>
            <div class="disp-logo-name">Medi<span>que</span></div>
            <div class="disp-clinic">{{ optional($clinic)->name ?? 'Semua Poliklinik' }}</div>
        </div>
    </div>
    <div class="disp-right">
        {{-- Clinic Selector --}}
        @if(isset($clinics) && $clinics->count() > 1)
            <select onchange="window.location='/display/'+this.value"
                    style="background:var(--surface2);border:1px solid var(--border);color:var(--text2);padding:5px 10px;border-radius:7px;font-size:12px;font-family:inherit;outline:none;cursor:pointer;">
                <option value="">Semua Poli</option>
                @foreach($clinics as $c)
                    <option value="{{ $c->id }}" {{ optional($clinic)->id === $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        @endif
        <div class="live-pill">
            <div class="live-dot"></div>
            LIVE
        </div>
        <div class="clock-wrap">
            <div class="clock-time" id="clockTime">--:--:--</div>
            <div class="clock-date" id="clockDate">—</div>
        </div>
    </div>
</header>

{{-- MAIN --}}
<div class="disp-grid">

    {{-- LEFT: CURRENT --}}
    <div class="current-card">
        <div class="now-label">🔔 Sedang Dipanggil</div>

        @if($currentQueue)
            <div class="now-number" id="dispNum">#{{ $currentQueue->queue_number }}</div>
            <div class="now-name"  id="dispName">{{ optional($currentQueue->patient)->name ?? '—' }}</div>
            <div class="now-spec"  id="dispSpec">
                {{ optional(optional($currentQueue->schedule)->doctor)->specialization ?? '' }}
            </div>

            <button class="call-btn"
                    id="callBtn"
                    onclick="callPatient(
                        '{{ $currentQueue->queue_number }}',
                        '{{ addslashes(optional($currentQueue->patient)->name ?? '') }}',
                        '{{ $currentQueue->id }}'
                    )">
                <svg viewBox="0 0 24 24"><polygon points="11,5 6,9 2,9 2,15 6,15 11,19 11,5"/><path d="M15.54 8.46a5 5 0 010 7.07"/><path d="M19.07 4.93a10 10 0 010 14.14"/></svg>
                Panggil dengan Suara
            </button>

            <div class="calling-state" id="callingState">
                <div class="sound-bars">
                    <span></span><span></span><span></span><span></span><span></span>
                </div>
                Sedang memanggil...
            </div>

        @else
            <div class="now-number" style="opacity:0.2;">—</div>
            <div class="empty-queue">Belum ada antrian aktif hari ini</div>
        @endif
    </div>

    {{-- RIGHT --}}
    <div class="right-col">

        {{-- STATS --}}
        <div class="stats-row">
            <div class="stat-block">
                <div class="stat-block-val" style="color:#FCD34D;" id="statWaiting">{{ $stats['waiting'] ?? 0 }}</div>
                <div class="stat-block-label">Menunggu</div>
            </div>
            <div class="stat-block">
                <div class="stat-block-val" style="color:#6EE7B7;" id="statDone">{{ $stats['done'] ?? 0 }}</div>
                <div class="stat-block-label">Selesai</div>
            </div>
            <div class="stat-block">
                <div class="stat-block-val" style="color:var(--text);" id="statTotal">{{ $stats['total'] ?? 0 }}</div>
                <div class="stat-block-label">Total</div>
            </div>
        </div>

        {{-- NEXT LIST --}}
        <div class="next-card">
            <div class="next-head">
                <span class="next-head-title">Antrian Berikutnya</span>
                <span class="next-head-count" id="nextCount">{{ $nextQueues->count() }} antrian</span>
            </div>
            <div class="next-list" id="nextList">
                @forelse($nextQueues as $queue)
                    @php
                        $pillClass = match($queue->status) {
                            'called'      => 'sp-called',
                            'in_progress' => 'sp-progress',
                            default       => 'sp-waiting',
                        };
                        $pillLabel = match($queue->status) {
                            'called'      => 'Dipanggil',
                            'in_progress' => 'Dilayani',
                            default       => 'Menunggu',
                        };
                    @endphp
                    <div class="next-row">
                        <div class="next-num">#{{ $queue->queue_number }}</div>
                        <div class="next-info">
                            <div class="next-patient">{{ optional($queue->patient)->name ?? '—' }}</div>
                            <div class="next-sub">Booking {{ $queue->booking_date->format('d/m/Y') }}</div>
                        </div>
                        <span class="status-pill {{ $pillClass }}">{{ $pillLabel }}</span>
                    </div>
                @empty
                    <div style="text-align:center;padding:36px 24px;color:var(--text3);font-size:13px;">
                        Tidak ada antrian berikutnya
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- TICKER --}}
<div class="ticker">
    <span class="ticker-badge">INFO</span>
    <div class="ticker-track">
        <div class="ticker-inner">
            @php $msg = 'Harap perhatikan layar antrian dan tetap berada di area tunggu. &nbsp;•&nbsp; Pasien yang dipanggil 3 kali tidak hadir akan dilewati. &nbsp;•&nbsp; Bawa kartu identitas dan kartu BPJS/asuransi. &nbsp;•&nbsp; Dilarang merokok di area fasilitas kesehatan. &nbsp;•&nbsp; Terima kasih atas kesabaran Anda. &nbsp;&nbsp;&nbsp;&nbsp;'; @endphp
            <span>{!! $msg !!}</span>
            <span>{!! $msg !!}</span>
        </div>
    </div>
</div>

{{-- VOICE MODAL --}}
<div class="voice-overlay" id="voiceOverlay">
    <div class="voice-card">
        <div class="voice-rings-wrap">
            <div class="voice-ring"></div>
            <div class="voice-ring"></div>
            <div class="voice-ring"></div>
            <div class="voice-icon-center">
                <svg viewBox="0 0 24 24"><polygon points="11,5 6,9 2,9 2,15 6,15 11,19 11,5"/><path d="M15.54 8.46a5 5 0 010 7.07"/></svg>
            </div>
        </div>
        <div class="voice-card-title">Memanggil Pasien</div>
        <div class="voice-card-num" id="voiceNum">#0</div>
        <div class="voice-card-name" id="voiceName">—</div>
        <button class="voice-close" onclick="closeVoice()">Tutup</button>
    </div>
</div>

<script>
// ═══════════════════
// LIVE CLOCK
// ═══════════════════
const DAYS   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const MONTHS = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

function tick() {
    const now = new Date();
    const pad = n => String(n).padStart(2,'0');
    document.getElementById('clockTime').textContent =
        pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
    document.getElementById('clockDate').textContent =
        DAYS[now.getDay()] + ', ' + now.getDate() + ' ' + MONTHS[now.getMonth()] + ' ' + now.getFullYear();
}
tick();
setInterval(tick, 1000);

// ═══════════════════
// VOICE CALL
// ═══════════════════
let speaking = false;

function callPatient(num, name, queueId) {
    if (speaking) return;
    speaking = true;

    // Show modal
    document.getElementById('voiceNum').textContent  = '#' + num;
    document.getElementById('voiceName').textContent = name;
    document.getElementById('voiceOverlay').classList.add('show');

    // UI state
    const btn  = document.getElementById('callBtn');
    const anim = document.getElementById('callingState');
    if (btn)  btn.disabled = true;
    if (anim) anim.style.display = 'flex';

    // Speech
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();

        const scripts = [
            `Nomor antrian ${num}, ${name}, silakan masuk ke ruang pemeriksaan.`,
            `Nomor antrian ${num}, ${name}.`,
            `Nomor antrian ${num}.`,
        ];

        let idx = 0;
        function speak() {
            if (idx >= scripts.length) {
                finishCall(btn, anim);
                return;
            }
            const u = new SpeechSynthesisUtterance(scripts[idx]);
            u.lang   = 'id-ID';
            u.rate   = 0.88;
            u.pitch  = 1.0;
            u.volume = 1.0;
            u.onend  = () => { idx++; setTimeout(speak, 900); };
            u.onerror = () => finishCall(btn, anim);
            window.speechSynthesis.speak(u);
        }
        speak();
    } else {
        alert('Browser tidak mendukung Text-to-Speech. Gunakan Chrome atau Edge.');
        finishCall(btn, anim);
    }

    // Update status via fetch
    fetch('/doctor/queues/' + queueId + '/call', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    }).catch(() => {});
}

function finishCall(btn, anim) {
    speaking = false;
    if (btn)  btn.disabled = false;
    if (anim) anim.style.display = 'none';
}

function closeVoice() {
    document.getElementById('voiceOverlay').classList.remove('show');
    window.speechSynthesis && window.speechSynthesis.cancel();
    const btn  = document.getElementById('callBtn');
    const anim = document.getElementById('callingState');
    finishCall(btn, anim);
}

// ═══════════════════
// AUTO REFRESH (setiap 20 detik)
// ═══════════════════
function refreshDisplay() {
    fetch(window.location.href, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => {
        const doc = new DOMParser().parseFromString(html, 'text/html');

        const ids = ['nextList','statWaiting','statDone','statTotal','nextCount'];
        ids.forEach(id => {
            const fresh = doc.getElementById(id);
            const curr  = document.getElementById(id);
            if (fresh && curr) curr.innerHTML = fresh.innerHTML;
        });
    })
    .catch(() => {});
}
setInterval(refreshDisplay, 20000);
</script>

</body>
</html>