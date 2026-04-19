<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Medique') }} — {{ $title ?? 'Dashboard' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="app-layout">

    @include('layouts.partials.sidebar')

    <div class="main-content">

        {{-- TOPBAR --}}
        <header class="topbar">
            <div>
                @isset($header)
                    {{ $header }}
                @else
                    <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
                @endisset
            </div>
            <div class="topbar-right">
                @isset($actions)
                    {{ $actions }}
                @endisset
                <span style="font-size:11px;color:var(--text3);">
                    {{ now()->format('d/m/Y') }}
                </span>
            </div>
        </header>

        {{-- FLASH MESSAGES --}}
        <main class="page-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <svg viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{ $slot }}
        </main>

    </div>
</div>

</body>
</html>