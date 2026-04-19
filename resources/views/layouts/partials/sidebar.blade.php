<aside class="sidebar">

    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}" class="logo-mark">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            </div>
            <span class="logo-text">Medi<span>que</span></span>
        </a>
    </div>

    <div class="sidebar-user">
        <div class="user-pill">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">

        @if(auth()->user()->hasRole('admin'))
            <div class="nav-section">
                <span class="nav-label">Overview</span>
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
            </div>
            <div class="nav-section">
                <span class="nav-label">Manajemen</span>
                <a href="{{ route('admin.clinics.index') }}"
                   class="nav-item {{ request()->routeIs('admin.clinics.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
                    Poliklinik
                </a>
                <a href="{{ route('admin.doctors.index') }}"
                   class="nav-item {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Dokter
                </a>
                <a href="{{ route('admin.schedules.index') }}"
                   class="nav-item {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Jadwal
                </a>
                <a href="{{ route('admin.queues.index') }}"
                   class="nav-item {{ request()->routeIs('admin.queues.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
                    Antrian
                    @php $wq = \App\Models\Queue::where('status','waiting')->count(); @endphp
                    @if($wq > 0)
                        <span class="nav-badge">{{ $wq }}</span>
                    @endif
                </a>
            </div>
        @endif

        @if(auth()->user()->hasRole('doctor'))
            <div class="nav-section">
                <span class="nav-label">Menu Dokter</span>
                <a href="{{ route('doctor.dashboard') }}"
                   class="nav-item {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('doctor.queues.index') }}"
                   class="nav-item {{ request()->routeIs('doctor.queues.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                    Antrian Hari Ini
                    @php
                        $myDoc = auth()->user()->doctor;
                        $mq = $myDoc ? \App\Models\Queue::whereHas('schedule', fn($q) => $q->where('doctor_id',$myDoc->id))->where('booking_date',today())->whereIn('status',['waiting','called'])->count() : 0;
                    @endphp
                    @if($mq > 0)
                        <span class="nav-badge green">{{ $mq }}</span>
                    @endif
                </a>
                <a href="{{ route('doctor.schedules.index') }}"
                   class="nav-item {{ request()->routeIs('doctor.schedules.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Jadwal Saya
                </a>
            </div>
        @endif

        @if(auth()->user()->hasRole('patient'))
            <div class="nav-section">
                <span class="nav-label">Menu Pasien</span>
                <a href="{{ route('patient.dashboard') }}"
                   class="nav-item {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('patient.queues.index') }}"
                   class="nav-item {{ request()->routeIs('patient.queues.index') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                    Antrian Saya
                </a>
                <a href="{{ route('patient.queues.create') }}"
                   class="nav-item {{ request()->routeIs('patient.queues.create') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Booking Antrian
                </a>
            </div>
        @endif

    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item">
                <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Logout
            </button>
        </form>
    </div>

</aside>