<x-app-layout title="Dashboard Saya">
    <x-slot name="header"><div class="topbar-title">Dashboard</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('patient.queues.create') }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Booking
        </a>
    </x-slot>

    {{-- POPUP DIPANGGIL --}}
    @if(($stats['active_queue'] ?? null) && in_array(optional($stats['active_queue'])->status, ['called','in_progress']))
        @php $aq = $stats['active_queue']; @endphp
        <div style="display:flex;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.65);align-items:center;justify-content:center;padding:20px;" id="calledPopup">
            <div style="background:white;border-radius:20px;padding:32px;text-align:center;max-width:360px;width:100%;animation:popIn 0.3s ease;">
                <div style="width:72px;height:72px;background:#E1F5EE;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;animation:pulse 1.2s ease infinite;">
                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#0F6E56" stroke-width="2"><polygon points="11,5 6,9 2,9 2,15 6,15 11,19 11,5"/><path d="M15.54 8.46a5 5 0 010 7.07"/><path d="M19.07 4.93a10 10 0 010 14.14"/></svg>
                </div>
                <div style="font-size:18px;font-weight:800;color:#1A1D23;margin-bottom:6px;">🎉 Anda Dipanggil!</div>
                <div style="font-size:48px;font-weight:800;color:#0F6E56;line-height:1;margin:8px 0;">{{ $aq->queue_number }}</div>
                <div style="font-size:13px;color:#6B7280;line-height:1.7;margin-bottom:16px;">
                    Menuju: <strong>{{ optional(optional(optional($aq->schedule)->doctor)->clinic)->name ?? 'Ruang Pemeriksaan' }}</strong><br>
                    Dokter: <strong>{{ optional(optional($aq->schedule)->doctor)->user->name ?? '—' }}</strong>
                </div>
                <div style="font-size:12px;color:#6B7280;margin-bottom:4px;">Antrian hangus dalam</div>
                <div style="font-size:28px;font-weight:800;color:#D85A30;" id="popTimer">10:00</div>
                <div style="height:4px;border-radius:2px;background:#F0F0F0;margin:8px 0 16px;overflow:hidden;"><div id="popBar" style="height:100%;border-radius:2px;background:#059669;transition:width 1s linear;width:100%;"></div></div>
                <button onclick="document.getElementById('calledPopup').style.display='none'"
                        style="width:100%;padding:11px;background:#0F6E56;color:white;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
                    Saya Sudah Menuju Ruangan
                </button>
            </div>
        </div>
        <style>
            @keyframes popIn { from{transform:scale(0.85);opacity:0;} to{transform:scale(1);opacity:1;} }
            @keyframes pulse { 0%,100%{transform:scale(1);} 50%{transform:scale(1.08);} }
        </style>
        <script>
        (function(){
            const total = 600;
            @php $calledLog = optional($aq->logs->where('action','called')->last()); @endphp
            @if($calledLog && $calledLog->timestamp)
            const start = new Date('{{ $calledLog->timestamp->toIso8601String() }}').getTime();
            function tick(){
                const left = Math.max(0, total - Math.floor((Date.now()-start)/1000));
                const m = String(Math.floor(left/60)).padStart(2,'0');
                const s = String(left%60).padStart(2,'0');
                const tEl = document.getElementById('popTimer');
                const bEl = document.getElementById('popBar');
                if(tEl) tEl.textContent = m+':'+s;
                if(bEl){
                    const pct = (left/total)*100;
                    bEl.style.width = pct+'%';
                    bEl.style.background = pct>50?'#059669':pct>20?'#D97706':'#DC2626';
                }
                if(left>0) setTimeout(tick,1000);
                else if(tEl) tEl.textContent='HANGUS';
            }
            tick();
            @endif
        })();
        </script>
    @endif

    {{-- GREETING --}}
    <div style="background:linear-gradient(135deg,#0F6E56,#1D9E75);border-radius:16px;padding:24px;color:white;margin-bottom:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;right:-30px;top:-30px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
        <div style="position:relative;z-index:1;">
            <div style="font-size:11px;opacity:0.7;text-transform:uppercase;letter-spacing:0.8px;font-weight:700;margin-bottom:4px;">Selamat Datang</div>
            <div style="font-size:20px;font-weight:800;margin-bottom:2px;">{{ auth()->user()->name }}</div>
            <div style="font-size:12px;opacity:0.7;">
                {{ optional($currentHospital ?? null)->name ?? '' }}
            </div>
            <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('patient.queues.create') }}" style="background:white;color:#0F6E56;border:none;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
                    + Booking Antrian
                </a>
                <a href="{{ route('profile.show') }}" style="background:rgba(255,255,255,0.15);color:white;border:1px solid rgba(255,255,255,0.3);padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;">
                    Profil Saya
                </a>
            </div>
        </div>
    </div>

    {{-- ANTRIAN AKTIF --}}
    @if($stats['active_queue'] ?? null)
        @php
            $aq = $stats['active_queue'];
            $statusColors = ['waiting'=>'#D97706','called'=>'#2563EB','in_progress'=>'#7C3AED','done'=>'#059669','cancelled'=>'#DC2626'];
            $statusBgs    = ['waiting'=>'#FEF3C7','called'=>'#DBEAFE','in_progress'=>'#EDE9FE','done'=>'#D1FAE5','cancelled'=>'#FEE2E2'];
        @endphp
        <div style="background:white;border-radius:14px;border:2px solid #E1F5EE;padding:20px;margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">Antrian Aktif</div>
            <div style="display:flex;align-items:center;gap:16px;">
                <div style="text-align:center;">
                    <div style="font-size:42px;font-weight:800;color:#0F6E56;line-height:1;">{{ $aq->queue_number }}</div>
                    <div style="font-size:9px;color:#9CA3AF;font-weight:700;text-transform:uppercase;margin-top:2px;">Nomor Antrian</div>
                </div>
                <div style="flex:1;border-left:1px solid #F0F0F0;padding-left:16px;">
                    <div style="margin-bottom:8px;">
                        <div style="font-size:10px;color:#9CA3AF;font-weight:600;margin-bottom:2px;">Dokter</div>
                        <div style="font-size:13px;font-weight:700;color:#1A1D23;">{{ optional(optional($aq->schedule)->doctor)->user->name ?? '—' }}</div>
                    </div>
                    <div style="margin-bottom:8px;">
                        <div style="font-size:10px;color:#9CA3AF;font-weight:600;margin-bottom:2px;">Ruangan</div>
                        <div style="font-size:13px;font-weight:700;color:#2563EB;">{{ optional(optional(optional($aq->schedule)->doctor)->clinic)->name ?? '—' }}</div>
                    </div>
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $statusBgs[$aq->status] ?? '#F0F0F0' }};color:{{ $statusColors[$aq->status] ?? '#6B7280' }};">
                        <span style="width:5px;height:5px;border-radius:50%;background:{{ $statusColors[$aq->status] ?? '#6B7280' }};"></span>
                        {{ $aq->status_label }}
                    </span>
                </div>
            </div>

            @if($aq->status === 'waiting')
                @php
                    $pos = \App\Models\Queue::where('schedule_id',$aq->schedule_id)->where('booking_date',$aq->booking_date)->where('status','waiting')->where('queue_number','<',$aq->queue_number)->count();
                @endphp
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid #F0F0F0;display:flex;align-items:center;justify-content:space-between;">
                    <div style="font-size:12px;color:#6B7280;">Estimasi tunggu: ~{{ $pos * 10 }} menit ({{ $pos }} antrian lagi)</div>
                    <form method="POST" action="{{ route('patient.queues.cancel', $aq) }}" onsubmit="return confirm('Batalkan antrian?')">
                        @csrf @method('PATCH')
                        <button type="submit" style="background:#FEE2E2;color:#991B1B;border:none;padding:5px 12px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit;">Batalkan</button>
                    </form>
                </div>
            @endif
        </div>
    @else
        <div style="background:white;border:2px dashed rgba(0,0,0,0.08);border-radius:14px;padding:32px;text-align:center;margin-bottom:20px;">
            <div style="font-size:32px;margin-bottom:10px;">📋</div>
            <div style="font-size:14px;font-weight:700;color:#1A1D23;margin-bottom:4px;">Tidak ada antrian aktif</div>
            <div style="font-size:12px;color:#9CA3AF;margin-bottom:14px;">Booking antrian untuk berobat sekarang</div>
            <a href="{{ route('patient.queues.create') }}" style="display:inline-block;background:#0F6E56;color:white;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">+ Booking Sekarang</a>
        </div>
    @endif

    {{-- STATS ROW --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px;">
        <div style="background:white;border-radius:12px;border:1px solid rgba(0,0,0,0.07);padding:14px;">
            <div style="font-size:11px;color:#9CA3AF;font-weight:600;margin-bottom:4px;">Total Booking</div>
            <div style="font-size:26px;font-weight:800;color:#1A1D23;">{{ $stats['total_bookings'] ?? 0 }}</div>
        </div>
        <div style="background:#E1F5EE;border-radius:12px;border:1px solid rgba(15,110,86,0.15);padding:14px;">
            <div style="font-size:11px;color:#0F6E56;font-weight:600;margin-bottom:4px;">Kunjungan Selesai</div>
            <div style="font-size:26px;font-weight:800;color:#0F6E56;">
                {{ \App\Models\Queue::where('patient_id',auth()->id())->where('status','done')->count() }}
            </div>
        </div>
    </div>

    {{-- RIWAYAT --}}
    <div style="background:white;border-radius:14px;border:1px solid rgba(0,0,0,0.07);overflow:hidden;">
        <div style="padding:14px 18px;border-bottom:1px solid rgba(0,0,0,0.06);display:flex;align-items:center;justify-content:space-between;">
            <div style="font-size:13px;font-weight:700;color:#1A1D23;">Riwayat Antrian</div>
            <a href="{{ route('patient.queues.index') }}" style="font-size:12px;color:#0F6E56;font-weight:600;text-decoration:none;">Lihat Semua →</a>
        </div>
        @forelse($stats['recent_queues'] ?? [] as $q)
            @php
                $sc = ['waiting'=>'#FEF3C7:#92400E','called'=>'#DBEAFE:#1E40AF','in_progress'=>'#EDE9FE:#5B21B6','done'=>'#D1FAE5:#065F46','cancelled'=>'#FEE2E2:#991B1B'];
                [$bg,$fg] = explode(':',$sc[$q->status]??'#F0F0F0:#6B7280');
            @endphp
            <div style="padding:12px 18px;border-bottom:1px solid rgba(0,0,0,0.05);display:flex;align-items:center;gap:12px;">
                <div style="width:38px;height:38px;background:#F0FDF4;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#0F6E56;flex-shrink:0;">
                    {{ $q->queue_number }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:#1A1D23;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ optional(optional(optional($q->schedule)->doctor)->user)->name ?? '—' }}
                    </div>
                    <div style="font-size:11px;color:#9CA3AF;">
                        {{ optional(optional(optional($q->schedule)->doctor)->clinic)->name ?? '—' }} ·
                        {{ $q->booking_date->format('d/m/Y') }}
                    </div>
                </div>
                <span style="padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;background:{{ $bg }};color:{{ $fg }};white-space:nowrap;">
                    {{ $q->status_label }}
                </span>
            </div>
        @empty
            <div style="text-align:center;padding:28px;color:#9CA3AF;font-size:13px;">Belum ada riwayat</div>
        @endforelse
    </div>

    {{-- Auto-refresh jika masih waiting --}}
    @if(($stats['active_queue'] ?? null) && optional($stats['active_queue'])->status === 'waiting')
        <script>setInterval(()=>{fetch(location.href,{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.text()).then(h=>{if(new DOMParser().parseFromString(h,'text/html').getElementById('calledPopup'))location.reload();}).catch(()=>{});},15000);</script>
    @endif
</x-app-layout>