@props([
    'variant' => 'primary', // primary | secondary | ghost
    'size' => 'md',          // sm | md | lg
    'href' => null,
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none';
    $sizes = [
        'sm' => 'h-9 px-3.5 text-sm rounded-lg',
        'md' => 'h-11 px-[18px] text-[15px] rounded-lg',
        'lg' => 'h-[52px] px-6 text-base rounded-lg',
    ];
    $variants = [
        'primary' => 'bg-ink text-white hover:bg-stone-800',
        'secondary' => 'bg-surface text-ink border border-line-strong hover:bg-sunken',
        'ghost' => 'text-ink-2 hover:bg-sunken hover:text-ink',
    ];
    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
