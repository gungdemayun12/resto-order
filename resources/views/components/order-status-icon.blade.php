@props(['icon' => 'clock', 'class' => 'w-5 h-5'])

@php
    $stroke = $attributes->get('stroke-width', '2');
@endphp

<svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24', 'aria-hidden' => 'true']) }}>
    @switch($icon)
        @case('wallet')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $stroke }}"
                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            @break
        @case('check-circle')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $stroke }}"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            @break
        @case('currency')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $stroke }}"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            @break
        @case('clock')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $stroke }}"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            @break
        @case('fire')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $stroke }}"
                d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $stroke }}"
                d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
            @break
        @case('bell')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $stroke }}"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            @break
        @case('sparkles')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $stroke }}"
                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            @break
        @case('qr')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $stroke }}"
                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            @break
        @case('check')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $stroke }}" d="M5 13l4 4L19 7" />
            @break
        @default
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $stroke }}"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    @endswitch
</svg>
