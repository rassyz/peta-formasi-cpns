<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Formasi CPNS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-canvas text-ink font-sans antialiased">
    @php
        // Navigasi admin (DESIGN.md §11): satu definisi dipakai sidebar desktop
        // dan panel mobile. 'pola' = pola routeIs untuk status aktif.
        $navigasi = [
            ['pola' => 'admin.beranda', 'url' => route('admin.beranda'), 'label' => 'Beranda',
             'ikon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>'],
            ['pola' => 'admin.musim', 'url' => route('admin.musim'), 'label' => 'Musim',
             'ikon' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>'],
            ['pola' => 'admin.impor', 'url' => route('admin.impor'), 'label' => 'Impor Formasi',
             'ikon' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M17 8l-5-5-5 5"/><path d="M12 3v12"/>'],
            ['pola' => 'admin.formasi', 'url' => route('admin.formasi'), 'label' => 'Kelola Formasi',
             'ikon' => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>'],
            ['pola' => 'admin.kode-akses', 'url' => route('admin.kode-akses'), 'label' => 'Kode Akses',
             'ikon' => '<path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4"/>'],
            ['pola' => 'admin.peserta', 'url' => route('admin.peserta'), 'label' => 'Peserta',
             'ikon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
        ];
        $kelasNav = fn (bool $aktif) => $aktif
            ? 'flex items-center gap-3 rounded-lg bg-sunken px-3 py-2.5 text-sm font-semibold text-ink'
            : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-ink-2 transition-colors hover:bg-sunken hover:text-ink';
    @endphp

    <div class="flex min-h-screen">
        {{-- Sidebar desktop (lg ke atas): tetap terlihat, lebar 256px --}}
        <aside class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col border-r border-line bg-surface lg:flex">
            <div class="flex items-center gap-2 px-5 pt-6 pb-5">
                <svg class="h-6 w-6 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                </svg>
                <span class="text-[17px] font-bold tracking-tight">PetaFormasi</span>
                <span class="rounded-md bg-sunken px-1.5 py-0.5 text-[11px] font-semibold tracking-wide text-ink-2 uppercase">Admin</span>
            </div>

            <nav class="flex flex-col gap-1 px-3">
                @foreach ($navigasi as $item)
                    <a href="{{ $item['url'] }}" class="{{ $kelasNav(request()->routeIs($item['pola'])) }}">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item['ikon'] !!}</svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto border-t border-line px-3 py-4">
                <div class="px-3 pb-3">
                    <p class="truncate text-sm font-medium text-ink">{{ auth()->user()?->name }}</p>
                    <p class="truncate text-[13px] text-ink-3">{{ auth()->user()?->email }}</p>
                </div>
                <a href="{{ url('/') }}" class="{{ $kelasNav(false) }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14L21 3"/></svg>
                    Lihat Situs
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-ink-2 transition-colors hover:bg-danger-soft hover:text-danger">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Topbar mobile/tablet (< lg): brand + burger membuka panel nav --}}
            <header class="sticky top-0 z-40 border-b border-line bg-surface/95 backdrop-blur lg:hidden"
                x-data="{ menuTerbuka: false }" @keydown.escape="menuTerbuka = false" @click.away="menuTerbuka = false">
                <div class="flex h-13 items-center justify-between gap-4 px-4 sm:px-6">
                    <a href="{{ route('admin.beranda') }}" class="flex items-center gap-2">
                        <svg class="h-6 w-6 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span class="text-[17px] font-bold tracking-tight">PetaFormasi</span>
                        <span class="rounded-md bg-sunken px-1.5 py-0.5 text-[11px] font-semibold tracking-wide text-ink-2 uppercase">Admin</span>
                    </a>
                    <button type="button" @click="menuTerbuka = ! menuTerbuka" :aria-expanded="menuTerbuka" aria-label="Buka atau tutup menu admin"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-ink transition-colors hover:bg-sunken">
                        <svg x-show="! menuTerbuka" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                        <svg x-show="menuTerbuka" x-cloak class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
                <div x-show="menuTerbuka" x-cloak class="border-t border-line px-4 py-3 sm:px-6">
                    <nav class="flex flex-col gap-1">
                        @foreach ($navigasi as $item)
                            <a href="{{ $item['url'] }}" class="{{ $kelasNav(request()->routeIs($item['pola'])) }}">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item['ikon'] !!}</svg>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                    <div class="mt-3 flex flex-col gap-2 border-t border-line pt-3">
                        <a href="{{ url('/') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-line-strong px-5 text-sm font-medium transition-colors hover:bg-sunken">Lihat Situs</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800">Keluar</button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Konten: lebar penuh area kerja (tabel admin butuh ruang), padding konsisten --}}
            <main class="flex-1 px-4 py-8 sm:px-6 lg:px-10">
                {{ $slot }}
            </main>

            <footer class="border-t border-line px-4 py-4 sm:px-6 lg:px-10">
                <p class="text-[13px] text-ink-3">Panel admin PetaFormasi — seluruh data bersumber dari pengumuman resmi instansi.</p>
            </footer>
        </div>
    </div>

    @livewireScripts
</body>
</html>
