<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name','Medique') }} — {{ $title ?? 'Dashboard' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<div class="app-layout">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    @include('layouts.partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <button class="hamburger" onclick="toggleSidebar()">
                    <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div>
                    @isset($header){{ $header }}@else<div class="topbar-title">{{ $title ?? '' }}</div>@endisset
                </div>
            </div>
            <div class="topbar-right">
                @isset($actions){{ $actions }}@endisset
            </div>
        </header>

        <main class="page-body">
            @if(session('success'))
                <div class="alert alert-success"><svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg><span>{{ session('success') }}</span></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg><span>{{ session('error') }}</span></div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>

<script>
function toggleSidebar(){
    const s=document.getElementById('appSidebar');
    const o=document.getElementById('sidebarOverlay');
    const open=s.classList.toggle('open');
    o.classList.toggle('show',open);
    document.body.style.overflow=open?'hidden':'';
}
function closeSidebar(){
    document.getElementById('appSidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
    document.body.style.overflow='';
}
window.addEventListener('resize',()=>{ if(window.innerWidth>768) closeSidebar(); });
</script>
</body>
</html>