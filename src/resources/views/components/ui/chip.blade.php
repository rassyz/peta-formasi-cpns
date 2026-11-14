@props([
    'active' => false,
])

@php
    $classes = $active
        ? 'bg-ink text-white border-ink'
        : 'bg-surface text-ink-2 border-line-strong hover:border-ink-3 hover:text-ink';
@endphp

<button type="button" aria-pressed="{{ $active ? 'true' : 'false' }}"
    {{ $attributes->merge(['class' => 'inline-flex h-9 items-center gap-1.5 rounded-full border px-3.5 text-sm font-medium transition-colors ' . $classes]) }}>
    {{ $slot }}
</button>
