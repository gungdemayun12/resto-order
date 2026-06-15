@props([
    'size' => 'md',
])

@php
    $sizeClasses = match ($size) {
        'xs' => 'h-8 w-auto max-w-[110px] sm:h-9 sm:max-w-[130px]',
        'nav' => 'h-12 w-auto max-w-[160px] sm:h-[52px] sm:max-w-[180px]',
        'menu-header' => 'h-14 w-auto max-w-[190px] sm:h-[3.25rem] sm:max-w-[210px]',
        'sm' => 'h-10 w-auto max-w-[130px] md:h-11 md:max-w-[150px]',
        'md' => 'h-14 w-auto max-w-[170px] md:h-16 md:max-w-[200px]',
        'lg' => 'h-16 w-auto max-w-[190px] md:h-20 md:max-w-[230px]',
        'xl' => 'h-20 w-auto max-w-[210px] md:h-24 md:max-w-[270px]',
        'hero' => 'h-24 w-auto max-w-[240px] md:h-32 md:max-w-[300px]',
        'receipt' => 'h-16 w-auto max-w-[180px] mx-auto',
        'sidebar' => 'h-12 w-auto max-w-[140px] sm:h-[52px] sm:max-w-[160px]',
        default => 'h-14 w-auto max-w-[170px] md:h-16 md:max-w-[200px]',
    };
@endphp

<img
    src="{{ asset('storage/logo.png') }}"
    alt="Resto Nusantara"
    {{ $attributes->merge(['class' => "object-contain {$sizeClasses}"]) }}
    loading="lazy"
    decoding="async"
/>
