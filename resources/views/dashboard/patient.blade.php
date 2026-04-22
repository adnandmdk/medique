<x-app-layout title="Beranda">
    <x-slot name="header"><div class="topbar-title">Beranda</div></x-slot>
    <x-slot name="actions">
        <a href="{{ route('patient.queues.create') }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Booking
        </a>
    </x-slot>

    {{-- POPUP DIPANGGIL --}}
    @if($activeQueue && in_array($activeQueue->status, ['called','in_progress']))
        <div id="calledPopup" style="display:flex;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.6);align-items:center;justify-content:center;padding:20px;">
            <div style="background:white;border-radius:20px;padding:32px;text-align:center;max-width:360px;width:100%;animation:popIn .3s ease;">
                <div style="width:72px;height:72px;background:#ECFDF5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;animation:ring 1s ease infinite;">
                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#059669" stroke-width="2"><polygon points="11,5 6,9 2,9 2,15 6,15 11,19 11,5"/><path d="M15.54 8.46a5 5 0 010 7.07"/><path d="M19.07 4.93a10 10 0 010 14.14"/></svg>
                </div>
                <div style="font-size:18px;font-weight:800;color:#0F172A;margin-bottom:4px;">🎉 Anda Dipanggil!</div>
                <div style="font-size:44px;font-weight:800;color:#0F6E56;line-height:1;margin:8px 0;">{{ $activeQueue->queue_number }}</div>
                <div style="font-size:13px;color:#64748B;line-height:1.7;margin-bottom:16px;">
                    Menuju: <strong>{{ optional(optional(optional($activeQueue->schedule)->doctor)->clinic)->name ?? 'Ruangan' }}</strong><br>
                    Dokter: <strong>{{ optional(optional($activeQueue->schedule)->doctor)->user->name ?? '—' }}</strong>
                </div>
                <div style="font-size:12px;color:#64748B;margin-bottom:4px;">Hangus dalam</div>
                <div style="font-size:28px;font-weight:800;color:#F59E0B;" id="countdown">10:00</div>
                <div style="height:4px;border-radius:2px;background:#F1F5F9;margin:8px 0 16px;overflow:hidden;">
                    <div id="countBar" style="height:100%;border-radius:2px;background:#059669;width:100%;transition:width 1s linear;"></div>
                </div>
                <button onclick="document.getElementById('calledPopup').style.display='none'"
                        style="width:100%;padding:11px;background:#0F6E56;color:white;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
                    Saya Menuju Ruangan
                </button>
            </div>
        </div>
        <style>
            @keyframes popIn{from{transform:scale(.85);opacity:0}to{transform:scale(1);opacity:1}}
            @keyframes ring{0%,100%{transform:scale(1)}50%{transform:scale(1.1)}}
        </style>
        <script>
        (function(){
            const total=600;
            @php $log=$activeQueue->logs->where('action','called')->last(); @endphp
            @if($log && $log->timestamp)
            const start=new Date('{{ $log->timestamp->toIso8601String() }}').getTime();
            (function tick(){
                const left=Math.max(0,total-Math.floor((Date.now()-start)/1000));
                const m=String(Math.floor(left/60)).padStart(2,'0');
                const s=String(left%60).padStart(2,'0');
                const e=document.getElementById('countdown');
                const b=document.getElementById('countBar');
                if(e) e.textContent=left>0?m+':'+s:'HANGUS';
                const p=(left/total)*100;
                if(b){b.style.width=p+'%';b.style.background=p>50?'#059669':p>20?'#F59E0B':'#EF4444';}
                if(left>0) setTimeout(tick,1000);
            })();
            @endif
        })();
        </script>
    @endif

    {{-- GREETING --}}
    <div style="background:linear-gradient(135deg,#0F6E56,#10B981);border-radius:14px;padding:22px;color:white;margin-bottom:14px;position:relative;overflow:hidden;">
        <div style="position:absolute;right:-30px;top:-30px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,.05);"></div>
        <div style="position:relative;z-index:1;">
            <div style="font-size:11px;opacity:.7;font-weight:600;text-transform:uppercase;letter-spacing:.7px;margin-bottom:3px;">Selamat Datang</div>
            <div style="font-size:18px;font-weight:800;margin-bottom:12px;">{{ auth()->user()->name }}</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('patient.queues.create') }}" style="background:white;color:#0F6E56;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
                    + Booking Antrian
                </a>
                <a href="{{ route('profile.show') }}" style="background:rgba(255,255,255,.15);color:white;border:1px solid rgba(255,255,255,.3);padding:8px 14px;border-radius:8px;font-size:12px;text-decoration:none;">
                    Profil Saya
                </a>
            </div>
        </div>
    </div>

    {{-- ANTRIAN AKTIF --}}
    @if($activeQueue)
        @php
            $colors=['waiting'=>['#FFFBEB','#92400E','#D97706'],'called'=>['#EFF6FF','#1E40AF','#3B82F6'],'in_progress'=>['#F5F3FF','#5B21B6','#8B5CF6'],'done'=>['#ECFDF5','#065F46','#059669'],'cancelled'=>['#FEF2F2','#991B1B','#EF4444']];
            [$bg,$fg,$dot]=$colors[$activeQueue->status]??['#F1F5F9','#475569','#94A3B8'];
        @endphp
        <div style="background:white;border-radius:14px;border:1.5px solid #E2E8F0;padding:18px;margin-bottom:14px;">
            <div style="font-size:10px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">Antrian Aktif</div>
            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                <div style="text-align:center;flex-shrink:0;">
                    <div style="font-size:40px;font-weight:800;color:#0F6E56;line-height:1;">{{ $activeQueue->queue_number }}</div>
                    <div style="font-size:9px;color:#94A3B8;font-weight:700;text-transform:uppercase;margin-top:2px;">No. Antrian</div>
                </div>
                <div style="flex:1;min-width:140px;border-left:1px solid #F1F5F9;padding-left:14px;">
                    <div style="margin-bottom:6px;">
                        <div style="font-size:10px;color:#94A3B8;font-weight:600;margin-bottom:1px;">Dokter</div>
                        <div style="font-size:13px;font-weight:700;">{{ optional(optional($activeQueue->schedule)->doctor)->user->name ?? '—' }}</div>
                    </div>
                    <div style="margin-bottom:8px;">
                        <div style="font-size:10px;color:#94A3B8;font-weight:600;margin-bottom:1px;">Ruangan</div>
                        <div style="font-size:13px;font-weight:700;color:#2563EB;">{{ optional(optional(optional($activeQueue->schedule)->doctor)->clinic)->name ?? '—' }}</div>
                    </div>
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $bg }};color:{{ $fg }};">
                        <span style="width:5px;height:5px;border-radius:50%;background:{{ $dot }};"></span>
                        {{ $activeQueue->status_label }}
                    </span>
                </div>
            </div>

            @if($activeQueue->status === 'waiting' && $position !== null)
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                    <div style="font-size:12px;color:#64748B;">
                        ⏱ ~{{ $position * 10 }} menit lagi ({{ $position }} antrian sebelum Anda)
                    </div>
                    <form method="POST" action="{{ route('patient.queues.cancel', $activeQueue) }}" onsubmit="return confirm('Batalkan antrian?')">
                        @csrf @method('PATCH')
                        <button type="submit" style="background:#FEF2F2;color:#991B1B;border:none;padding:5px 11px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit;">Batalkan</button>
                    </form>
                </div>
            @endif
        </div>
    @else
        <div style="background:white;border:2px dashed #E2E8F0;border-radius:14px;padding:28px;text-align:center;margin-bottom:14px;">
            <div style="font-size:30px;margin-bottom:8px;">📋</div>
            <div style="font-size:14px;font-weight:700;color:#0F172A;margin-bottom:3px;">Tidak ada antrian aktif</div>
            <div style="font-size:12px;color:#94A3B8;margin-bottom:12px;">Booking untuk berobat sekarang</div>
            <a href="{{ route('patient.queues.create') }}" style="display:inline-block;background:#0F6E56;color:white;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">+ Booking Sekarang</a>
        </div>
    @endif

    {{-- STATS --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
        <div style="background:white;border-radius:12px;border:1px solid #E2E8F0;padding:14px;">
            <div style="font-size:11px;font-weight:600;color:#94A3B8;margin-bottom:3px;">Total Booking</div>
            <div style="font-size:24px;font-weight:800;color:#0F172A;">{{ $totalBookings }}</div>
        </div>
        <div style="background:#ECFDF5;border-radius:12px;border:1px solid #A7F3D0;padding:14px;">
            <div style="font-size:11px;font-weight:600;color:#059669;margin-bottom:3px;">Selesai</div>
            <div style="font-size:24px;font-weight:800;color:#0F6E56;">{{ $totalDone }}</div>
        </div>
    </div>

    {{-- RIWAYAT --}}
    <div style="background:white;border-radius:14px;border:1px solid #E2E8F0;overflow:hidden;">
        <div style="padding:13px 16px;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;">
            <div style="font-size:13px;font-weight:700;color:#0F172A;">Riwayat Antrian</div>
            <a href="{{ route('patient.queues.index') }}" style="font-size:12px;color:#0F6E56;font-weight:600;text-decoration:none;">Lihat Semua →</a>
        </div>
        @forelse($recentQueues as $q)
            @php $colors=['waiting'=>'#FFFBEB:#92400E','called'=>'#EFF6FF:#1E40AF','in_progress'=>'#F5F3FF:#5B21B6','done'=>'#ECFDF5:#065F46','cancelled'=>'#FEF2F2:#991B1B']; [$qbg,$qfg]=explode(':',$colors[$q->status]??'#F1F5F9:#475569'); @endphp
            <div style="padding:11px 16px;border-bottom:1px solid #F8FAFC;display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:9px;background:#ECFDF5;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#0F6E56;flex-shrink:0;">{{ $q->queue_number }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ optional(optional(optional($q->schedule)->doctor)->user)->name ?? '—' }}</div>
                    <div style="font-size:11px;color:#94A3B8;">{{ optional(optional(optional($q->schedule)->doctor)->clinic)->name ?? '—' }} · {{ $q->booking_date->format('d/m/Y') }}</div>
                </div>
                <span style="padding:3px 8px;border-radius:20px;font-size:10px;font-weight:700;background:{{ $qbg }};color:{{ $qfg }};white-space:nowrap;">{{ $q->status_label }}</span>
            </div>
        @empty
            <div style="text-align:center;padding:24px;color:#94A3B8;font-size:13px;">Belum ada riwayat</div>
        @endforelse
    </div>

    {{-- Poll setiap 15 detik jika masih waiting --}}
    @if($activeQueue && $activeQueue->status === 'waiting')
        <script>
        setInterval(()=>{
            fetch(location.href,{headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(r=>r.text())
            .then(h=>{
                const doc=new DOMParser().parseFromString(h,'text/html');
                if(doc.getElementById('calledPopup')) location.reload();
            }).catch(()=>{});
        },15000);
        </script>
    @endif
</x-app-layout>