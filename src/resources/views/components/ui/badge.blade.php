@props([
    'variant' => 'neutral', // success | danger | warning | neutral | accent | locked
])

@php
    $styles = [
        'success' => 'bg-success-soft text-success',
        'danger' => 'bg-danger-soft text-danger',
        'warning' => 'bg-warning-soft text-warning',
        'neutral' => 'bg-sunken text-ink-2',
        'accent' => 'bg-accent-soft text-accent',
        'locked' => 'bg-sunken text-locked',
    ];
    $icons = [
        'success' => 'M20 6 9 17l-5-5',
        'danger' => 'M18 6 6 18 M6 6l12 12',
        'warning' => 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3ZM12 9v4M12 17h.01',
        'neutral' => 'M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18ZM12 7v5l3 2',
        'accent' => 'M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9L12 3z',
        'locked' => 'M7 11V8a5 5 0 0 1 10 0v3 M5 11h14v10H5z',
    ];
    $style = $styles[$variant] ?? $styles['neutral'];
    $icon = $icons[$variant] ?? $icons['neutral'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ' . $style]) }}>
    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="{{ $icon }}" />
    </svg>
    {{ $slot }}
</span>
