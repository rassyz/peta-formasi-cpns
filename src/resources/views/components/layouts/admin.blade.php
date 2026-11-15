<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin — Peta Formasi' }}</title>
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
                <a href="{{ route('admin.pengaturan') }}" class="{{ $kelasNav(request()->routeIs('admin.pengaturan')) }}">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Pengaturan Akun
                </a>
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
                        <a href="{{ route('admin.pengaturan') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-line-strong px-5 text-sm font-medium transition-colors hover:bg-sunken">Pengaturan Akun</a>
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

    {{-- Sistem toast admin (15 Nov 2026): komponen Livewire memancarkan
         $this->dispatch('toast', tipe: 'sukses|galat|peringatan', pesan: '...')
         dan wadah ini menampilkannya melayang — terlihat dari posisi scroll
         mana pun, hilang sendiri setelah 5 detik, bisa ditutup manual. --}}
    <div x-data="{
            daftar: [],
            berikut: 1,
            tambah(e) {
                const id = this.berikut++;
                this.daftar.push({ id, tipe: e.detail.tipe ?? 'sukses', pesan: e.detail.pesan ?? '' });
                setTimeout(() => this.hapus(id), 5000);
            },
            hapus(id) { this.daftar = this.daftar.filter((t) => t.id !== id); },
         }"
        @toast.window="tambah($event)"
        class="pointer-events-none fixed inset-x-3 top-3 z-100 flex flex-col gap-2 sm:inset-x-auto sm:right-4 sm:top-4 sm:w-96"
        aria-live="polite">
        <template x-for="t in daftar" :key="t.id">
            <div x-transition:enter="transition duration-300 ease-out"
                 x-transition:enter-start="translate-x-4 opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition duration-200 ease-in"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="pointer-events-auto flex items-start gap-3 rounded-xl border border-line bg-surface p-4 shadow-float">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
                      :class="t.tipe === 'galat' ? 'bg-danger-soft text-danger' : (t.tipe === 'peringatan' ? 'bg-warning-soft text-warning' : 'bg-success-soft text-success')">
                    <svg x-show="t.tipe === 'sukses'" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                    <svg x-show="t.tipe === 'galat'" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    <svg x-show="t.tipe === 'peringatan'" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3ZM12 9v4M12 17h.01"/></svg>
                </span>
                <p class="min-w-0 flex-1 pt-1.5 text-sm leading-5 text-ink" x-text="t.pesan"></p>
                <button type="button" @click="hapus(t.id)" aria-label="Tutup notifikasi"
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-ink-3 transition-colors hover:bg-sunken hover:text-ink">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    @livewireScripts
</body>
</html>
