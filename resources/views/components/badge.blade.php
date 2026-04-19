@props(['color' => 'gray'])

@php
$colors = [
    'waiting'     => 'badge-waiting',
    'called'      => 'badge-called',
    'in_progress' => 'badge-progress',
    'done'        => 'badge-done',
    'cancelled'   => 'badge-cancelled',
    'active'      => 'badge-active',
    'inactive'    => 'badge-inactive',
];
$class = $colors[$color] ?? 'badge-inactive';
@endphp

<span {{ $attributes->merge(['class' => "badge $class"]) }}>
    {{ $slot }}
</span>