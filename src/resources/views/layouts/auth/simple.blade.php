<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-canvas bg-kontur text-ink antialiased">
        {{-- Paksa tampilan terang: skrip penampilan Flux di partials.head dapat
             menambahkan kelas .dark ke <html> (dari preferensi lama starter kit),
             yang membalik token aksen Flux menjadi putih. Produk ini terang saja. --}}
        <script>
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
            try {
                localStorage.setItem('appearance', 'light');
                localStorage.setItem('flux.appearance', 'light');
            } catch (e) {}
        </script>
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-6">
                <a href="{{ route('home') }}" class="flex items-center justify-center gap-2" wire:navigate>
                    <svg class="h-7 w-7 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span class="text-lg font-bold tracking-tight">PetaFormasi</span>
                </a>
                <div class="rounded-2xl border border-line bg-surface p-6 shadow-card sm:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
