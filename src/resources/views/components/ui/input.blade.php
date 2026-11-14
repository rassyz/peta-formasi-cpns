@props([
    'label' => null,
    'name',
    'type' => 'text',
    'hint' => null,
])

@php
    $hasError = isset($errors) && $errors->has($name);
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-ink">{{ $label }}</label>
    @endif

    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        {{ $attributes->merge(['class' => 'h-11 w-full rounded-lg border bg-surface px-3.5 text-[15px] text-ink placeholder:text-ink-3 focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent ' . ($hasError ? 'border-danger' : 'border-line-strong')]) }}>

    @if ($hasError)
        <p class="mt-1.5 text-[13px] text-danger">{{ $errors->first($name) }}</p>
    @elseif ($hint)
        <p class="mt-1.5 text-[13px] text-ink-2">{{ $hint }}</p>
    @endif
</div>
