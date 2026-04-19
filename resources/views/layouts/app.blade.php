<!DOCTYPE html>
<html lang="id">
<head <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Medique') }} — {{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="app-layout">

    {{-- Overlay mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    @include('layouts.partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                {{-- Hamburger --}}
                <button class="hamburger" onclick="toggleSidebar()" aria-label="Menu">
                    <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div>
                    @isset($header)
                        {{ $header }}
                    @else
                        <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
                    @endisset
                </div>
            </div>
            <div class="topbar-right">
                @isset($actions) {{ $actions }} @endisset
                <span style="font-size:11px;color:var(--text3);display:none;" class="hide-mobile">
                    {{ now()->format('d/m/Y') }}
                </span>
            </div>
        </header>

        <main class="page-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('show');
    document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
}
function closeSidebar() {
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.remove('open');
    overlay.classList.remove('show');
    document.body.style.overflow = '';
}
// Close on resize to desktop
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) closeSidebar();
});
</script>
</body>
</html>