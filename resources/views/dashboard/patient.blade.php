<x-app-layout title="Dashboard Pasien">
    <x-slot name="header"><div class="topbar-title">Dashboard Saya</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('patient.queues.create') }}" class="btn btn-primary btn-sm">+ Booking</a>
    </x-slot>

    {{-- POPUP: Dipanggil --}}
    @if(($stats['active_queue'] ?? null) && in_array(optional($stats['active_queue'])->status, ['called','in_progress']))
        @php $aq = $stats['active_queue']; @endphp
        <div class="called-popup show" id="calledPopup">
            <div class="called-popup-card">
                <div class="called-popup-icon">
                    <svg viewBox="0 0 24 24"><polygon points="11,5 6,9 2,9 2,15 6,15 11,19 11,5"/><path d="M15.54 8.46a5 5 0 010 7.07"/><path d="M19.07 4.93a10 10 0 010 14.14"/></svg>
                </div>
                <div class="called-popup-title">🎉 Anda Dipanggil!</div>
                <div class="called-popup-num">#{{ $aq->queue_number }}</div>
                <div class="called-popup-sub">
                    Silakan menuju <strong>{{ optional(optional(optional($aq->schedule)->doctor)->clinic)->name ?? 'Ruang Pemeriksaan' }}</strong><br>
                    Dokter: <strong>{{ optional(optional($aq->schedule)->doctor)->user->name ?? '—' }}</strong>
                </div>

                {{-- Countdown hangus --}}
                @php $calledAt = optional($aq->logs->where('action','called')->last())->timestamp; @endphp
                @if($calledAt)
                    <div class="called-popup-timer">
                        <div class="called-popup-timer-label">Antrian hangus dalam</div>
                        <div class="called-popup-timer-val" id="popupCountdown">10:00</div>
                        <div class="countdown-bar"><div class="countdown-fill" id="popupBar" style="width:100%;background:#059669;"></div></div>
                    </div>
                @endif

                <button class="btn btn-primary called-popup-btn" onclick="document.getElementById('calledPopup').classList.remove('show')">
                    Saya Sudah Menuju Ruangan
                </button>
            </div>
        </div>
        <script>
        (function(){
            const totalSec = 600; // 10 menit
            @if($calledAt)
            const calledAt = new Date('{{ $calledAt->toIso8601String() }}').getTime();
            function tick(){
                const now   = Date.now();
                const elaps = Math.floor((now - calledAt) / 1000);
                const left  = Math.max(0, totalSec - elaps);
                const m     = String(Math.floor(left/60)).padStart(2,'0');
                const s     = String(left % 60).padStart(2,'0');
                const el    = document.getElementById('popupCountdown');
                const bar   = document.getElementById('popupBar');
                if(el) el.textContent = m + ':' + s;
                const pct = (left / totalSec) * 100;
                if(bar){
                    bar.style.width = pct + '%';
                    bar.style.background = pct > 50 ? '#059669' : pct > 20 ? '#D97706' : '#DC2626';
                }
                if(left <= 0){
                    if(el) el.textContent = 'HANGUS';
                    return;
                }
                setTimeout(tick, 1000);
            }
            tick();
            @endif
        })();
        </script>
    @endif

    {{-- PATIENT HERO --}}
    <div class="patient-hero">
        <div style="position:relative;z-index:1;">
            <div style="font-size:11px;opacity:0.7;text-transform:uppercase;letter-spacing:0.8px;font-weight:700;margin-bottom:6px;">Selamat Datang</div>
            <div style="font-size:22px;font-weight:800;margin-bottom:4px;">{{ auth()->user()->name }}</div>
            <div style="font-size:13px;opacity:0.75;">{{ auth()->user()->email }}</div>
            <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ route('patient.queues.create') }}"
                   style="background:white;color:var(--brand);border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                    + Booking Antrian
                </a>
                <a href="{{ route('patient.queues.index') }}"
                   style="background:rgba(255,255,255,0.15);color:white;border:1px solid rgba(255,255,255,0.3);padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                    Riwayat
                </a>
            </div>
        </div>
    </div>

    {{-- ANTRIAN AKTIF --}}
    @if($stats['active_queue'] ?? null)
        @php $aq = $stats['active_queue']; @endphp
        <div class="patient-info-grid">
            <div class="patient-info-card" style="border-left:4px solid var(--brand);">
                <div class="pic-label">Nomor Antrian</div>
                <div class="pic-value" style="font-size:36px;color:var(--brand);">#{{ $aq->queue_number }}</div>
                <div class="pic-sub">
                    <span class="badge {{ match($aq->status){
                        'waiting'=>'badge-waiting',
                        'called'=>'badge-called',
                        'in_progress'=>'badge-progress',
                        'done'=>'badge-done',
                        default=>'badge-inactive'
                    } }}">{{ $aq->status_label }}</span>
                </div>
            </div>
            <div class="patient-info-card">
                <div class="pic-label">Token Antrian</div>
                <div class="pic-value" style="font-family:'Courier New';letter-spacing:2px;">
                    {{-- Token disensor untuk pasien --}}
                    ****{{ substr($aq->token, -4) }}
                </div>
                <div class="pic-sub">Simpan untuk tracking</div>
            </div>
            <div class="patient-info-card">
                <div class="pic-label">Nama Dokter</div>
                <div class="pic-value">{{ optional(optional($aq->schedule)->doctor)->user->name ?? '—' }}</div>
                <div class="pic-sub">{{ optional(optional($aq->schedule)->doctor)->specialization ?? '' }}</div>
            </div>
            <div class="patient-info-card" style="border-left:4px solid #2563EB;">
                <div class="pic-label">Ruangan</div>
                <div class="pic-value" style="color:#2563EB;">
                    {{ optional(optional(optional($aq->schedule)->doctor)->clinic)->name ?? '—' }}
                </div>
                <div class="pic-sub">Tanggal: {{ $aq->booking_date->format('d/m/Y') }}</div>
            </div>
        </div>

        {{-- Estimasi Waktu --}}
        @if($aq->status === 'waiting')
            @php
                $position = \App\Models\Queue::where('schedule_id', $aq->schedule_id)
                    ->where('booking_date', $aq->booking_date)
                    ->where('status', 'waiting')
                    ->where('queue_number', '<', $aq->queue_number)
                    ->count();
                $estimasiMenit = $position * 10; // asumsi 10 menit per pasien
            @endphp
            <div class="alert alert-brand" style="margin-bottom:20px;">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                <div>
                    <strong>Estimasi Tunggu:</strong>
                    ~{{ $estimasiMenit }} menit
                    ({{ $position }} antrian sebelum Anda)
                </div>
            </div>
        @endif

        {{-- Cancel button --}}
        @if($aq->status === 'waiting')
            <form method="POST" action="{{ route('patient.queues.cancel', $aq) }}" style="margin-bottom:20px;" onsubmit="return confirm('Batalkan antrian ini?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-danger btn-sm">Batalkan Antrian</button>
            </form>
        @endif

    @else
        <div class="card" style="border:2px dashed var(--border);margin-bottom:20px;">
            <div class="card-body" style="text-align:center;padding:36px;">
                <div class="empty-icon" style="margin:0 auto 14px;">
                    <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                </div>
                <div class="empty-title">Tidak ada antrian aktif</div>
                <div class="empty-sub">Booking antrian untuk berobat sekarang</div>
                <a href="{{ route('patient.queues.create') }}" class="btn btn-primary btn-sm">+ Booking Sekarang</a>
            </div>
        </div>
    @endif

    {{-- Kalender Pasien --}}
    <x-calendar :is-admin="false" />

    {{-- Riwayat --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Riwayat Antrian</div>
            <a href="{{ route('patient.queues.index') }}" class="btn btn-secondary btn-sm">Semua →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>No</th><th>Dokter</th><th>Ruangan</th><th>Tanggal</th><th>Status</th></tr></thead>
                <tbody>
                    @php $sm=['waiting'=>'badge-waiting','called'=>'badge-called','in_progress'=>'badge-progress','done'=>'badge-done','cancelled'=>'badge-cancelled']; @endphp
                    @forelse($stats['recent_queues'] ?? [] as $q)
                        <tr>
                            <td><strong>#{{ $q->queue_number }}</strong></td>
                            <td>{{ optional(optional(optional($q->schedule)->doctor)->user)->name ?? '—' }}</td>
                            <td>{{ optional(optional(optional($q->schedule)->doctor)->clinic)->name ?? '—' }}</td>
                            <td style="color:var(--text2);">{{ $q->booking_date->format('d/m/Y') }}</td>
                            <td><span class="badge {{ $sm[$q->status] ?? '' }}">{{ $q->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state" style="padding:24px;"><div class="empty-title">Belum ada riwayat</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Polling: cek status antrian setiap 15 detik --}}
    @if(($stats['active_queue'] ?? null) && in_array(optional($stats['active_queue'])->status, ['waiting']))
        <script>
        setInterval(function(){
            fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                // Reload if status changed to 'called'
                if (doc.getElementById('calledPopup') && doc.getElementById('calledPopup').classList.contains('show')) {
                    window.location.reload();
                }
            }).catch(() => {});
        }, 15000);
        </script>
    @endif
</x-app-layout>