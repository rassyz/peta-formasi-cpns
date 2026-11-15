@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <h1 class="text-2xl font-bold tracking-tight text-ink">{{ $title }}</h1>
    <p class="mt-2 text-[15px] leading-6 text-ink-2">{{ $description }}</p>
</div>
