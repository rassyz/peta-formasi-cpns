<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Formasi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-canvas text-ink flex flex-col">
    {{-- Header global (DESIGN.md §4; mobile: baris ramping wireframe + menu burger panel turun) --}}
    <header class="sticky top-0 z-40 bg-surface/95 backdrop-blur border-b border-line"
        x-data="{ menuTerbuka: false }" @keydown.escape="menuTerbuka = false" @click.away="menuTerbuka = false">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-13 items-center justify-between gap-4 md:h-16">
                <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0">
                    <svg class="h-6 w-6 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span class="text-[17px] font-bold tracking-tight">PetaFormasi</span>
                </a>

                <nav class="hidden md:flex items-center gap-1 text-[15px]">
                    <a href="{{ url('/formasi') }}" class="rounded-lg px-3 py-2 text-ink-2 hover:bg-sunken hover:text-ink transition-colors">Cari Formasi</a>
                    <a href="{{ url('/toolkit') }}" class="rounded-lg px-3 py-2 text-ink-2 hover:bg-sunken hover:text-ink transition-colors">Toolkit Berkas</a>
                    <a href="{{ url('/harga') }}" class="rounded-lg px-3 py-2 text-ink-2 hover:bg-sunken hover:text-ink transition-colors">Harga Pass</a>
                </nav>

                {{-- Aksi desktop (sm ke atas): nav lengkap + Masuk/Daftar --}}
                <div class="hidden items-center gap-2 sm:flex">
                    @auth
                        {{-- Menu akun (15 Nov 2026): avatar inisial membuka dropdown berisi
                             identitas + halaman akun + Keluar — menggantikan tombol
                             Pass Saya / Panel Admin / Keluar yang dulu berjajar. --}}
                        @if (! auth()->user()->isAdmin())
                            {{-- Pass Saya SENGAJA di luar menu (15 Nov 2026): ia "lemari
                                 barang yang sudah dibayar" — halaman akun tersering
                                 dikunjungi peserta, harus satu klik & selalu terlihat. --}}
                            <a href="{{ route('akun.pass') }}" class="inline-flex h-10 items-center gap-2 rounded-lg border border-line-strong px-4 text-sm font-medium transition-colors hover:bg-sunken"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 11v2"/><path d="M13 17v2"/></svg>Pass Saya</a>
                        @endif
                        <div class="relative" x-data="{ menuAkun: false }">
                            <button type="button" @click="menuAkun = ! menuAkun" :aria-expanded="menuAkun" aria-haspopup="true" aria-label="Buka menu akun"
                                class="flex h-10 items-center gap-2 rounded-full border border-line-strong py-1 pl-1 pr-4 transition-colors hover:bg-sunken">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-accent-soft text-[13px] font-semibold text-accent">{{ auth()->user()->inisial() }}</span>
                                <span class="hidden max-w-44 truncate text-sm font-medium xl:block">{{ auth()->user()->name }}</span>
                            </button>
                            <div x-show="menuAkun" x-cloak @click.away="menuAkun = false" @keydown.escape.window="menuAkun = false"
                                x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
                                class="absolute right-0 top-full z-50 mt-2 w-72 origin-top-right rounded-2xl border border-line bg-surface py-2 shadow-float">
                                <div class="flex items-center gap-3 border-b border-line px-4 pb-3 pt-2">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-accent-soft text-base font-semibold text-accent">{{ auth()->user()->inisial() }}</span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                                        <p class="truncate text-[13px] text-ink-3">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>
                                <nav class="py-1.5">
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ url('/admin') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-sunken"><svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>Panel Admin</a>
                                        <a href="{{ route('admin.pengaturan') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-sunken"><svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>Pengaturan Akun</a>
                                    @else
                                        <a href="{{ route('akun.redeem') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-sunken"><svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>Redeem Kode</a>
                                        <a href="{{ route('akun.pengaturan') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-sunken"><svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>Pengaturan</a>
                                    @endif
                                </nav>
                                <div class="border-t border-line pt-1.5">
                                    <form method="POST" action="{{ route('logout') }}" x-data="{ keluar: false }" @submit="keluar = true">
                                        @csrf
                                        <button type="submit" :disabled="keluar" class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm text-danger transition-colors hover:bg-danger-soft disabled:cursor-wait disabled:opacity-70">
                                            <svg x-show="! keluar" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg><svg x-show="keluar" x-cloak class="h-5 w-5 shrink-0 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span x-text="keluar ? 'Keluar…' : 'Keluar'">Keluar</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="inline-flex h-10 items-center gap-2 rounded-lg border border-line-strong px-4 text-sm font-medium transition-colors hover:bg-sunken"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="m10 17 5-5-5-5"/><path d="M15 12H3"/></svg>Masuk</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex h-10 items-center gap-2 rounded-lg bg-ink px-4 text-sm font-medium text-white hover:bg-stone-800 transition-colors"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/></svg>Daftar</a>
                        @endif
                    @endauth
                </div>

                {{-- Aksi mobile: tombol burger — membuka panel menu di bawah header --}}
                <div class="flex items-center sm:hidden">
                    <button type="button" @click="menuTerbuka = ! menuTerbuka" :aria-expanded="menuTerbuka" aria-label="Buka atau tutup menu navigasi"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-ink transition-colors hover:bg-sunken">
                        <svg x-show="! menuTerbuka" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                        <svg x-show="menuTerbuka" x-cloak class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
            </div>

            {{-- Panel menu mobile (pola referensi SSCASN): tautan bertumpuk + tombol akun --}}
            <div x-show="menuTerbuka" x-cloak class="pb-4 sm:hidden">
                @php
                    $kelasItem = fn (bool $aktif) => $aktif
                        ? 'rounded-lg bg-sunken px-3.5 py-2.5 font-semibold text-ink'
                        : 'rounded-lg px-3.5 py-2.5 text-ink-2';
                @endphp
                <nav class="flex flex-col gap-1 pt-2 text-[15px]">
                    <a href="{{ url('/') }}" class="{{ $kelasItem(request()->routeIs('home')) }}">Beranda</a>
                    <a href="{{ url('/formasi') }}" class="{{ $kelasItem(request()->routeIs('formasi.*') || request()->routeIs('jurusan.*')) }}">Cari Formasi</a>
                    <a href="{{ url('/toolkit') }}" class="{{ $kelasItem(request()->routeIs('toolkit')) }}">Toolkit Berkas</a>
                    <a href="{{ url('/harga') }}" class="{{ $kelasItem(request()->routeIs('harga')) }}">Harga Pass</a>
                </nav>
                <div class="mt-3 flex flex-col gap-2 border-t border-line pt-4">
                    @auth
                        {{-- Blok akun panel mobile (15 Nov 2026): identitas + baris menu,
                             sepasang dengan dropdown akun di desktop. --}}
                        <div class="flex items-center gap-3 px-1.5 pb-1 pt-1">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-accent-soft text-base font-semibold text-accent">{{ auth()->user()->inisial() }}</span>
                            <div class="min-w-0">
                                <p class="truncate text-[15px] font-semibold">{{ auth()->user()->name }}</p>
                                <p class="truncate text-[13px] text-ink-3">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        @php
                            $kelasAkun = fn (bool $aktif) => $aktif
                                ? 'flex items-center gap-3 rounded-lg bg-sunken px-3.5 py-2.5 text-[15px] font-semibold text-ink'
                                : 'flex items-center gap-3 rounded-lg px-3.5 py-2.5 text-[15px] text-ink-2';
                        @endphp
                        <nav class="flex flex-col gap-1">
                            @if (auth()->user()->isAdmin())
                                <a href="{{ url('/admin') }}" class="{{ $kelasAkun(false) }}"><svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>Panel Admin</a>
                                <a href="{{ route('admin.pengaturan') }}" class="{{ $kelasAkun(request()->routeIs('admin.pengaturan')) }}"><svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>Pengaturan Akun</a>
                            @else
                                <a href="{{ route('akun.pass') }}" class="{{ $kelasAkun(request()->routeIs('akun.pass')) }}"><svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 11v2"/><path d="M13 17v2"/></svg>Pass Saya</a>
                                <a href="{{ route('akun.redeem') }}" class="{{ $kelasAkun(request()->routeIs('akun.redeem')) }}"><svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>Redeem Kode</a>
                                <a href="{{ route('akun.pengaturan') }}" class="{{ $kelasAkun(request()->routeIs('akun.pengaturan')) }}"><svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>Pengaturan</a>
                            @endif
                        </nav>
                        <form method="POST" action="{{ route('logout') }}" x-data="{ keluar: false }" @submit="keluar = true" class="mt-1 border-t border-line pt-3">
                            @csrf
                            <button type="submit" :disabled="keluar" class="flex w-full items-center gap-3 rounded-lg px-3.5 py-2.5 text-left text-[15px] text-danger transition-colors hover:bg-danger-soft disabled:cursor-wait disabled:opacity-70">
                                <svg x-show="! keluar" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg><svg x-show="keluar" x-cloak class="h-5 w-5 shrink-0 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span x-text="keluar ? 'Keluar…' : 'Keluar'">Keluar</span>
                            </button>
                        </form>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/></svg>Daftar</a>
                        @endif
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-line-strong px-5 text-sm font-medium transition-colors hover:bg-sunken"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="m10 17 5-5-5-5"/><path d="M15 12H3"/></svg>Masuk</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer (DESIGN.md §4) --}}
    <footer class="border-t border-line bg-surface bg-kontur">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid gap-8 md:grid-cols-3">
                <div>
                    <p class="flex items-center gap-1.5 text-[15px] font-bold tracking-tight">
                        <svg class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        PetaFormasi
                    </p>
                    <p class="mt-2 text-sm leading-6 text-ink-2">Membantu peserta CPNS memilih formasi yang syaratnya benar-benar lolos dan peluangnya paling besar, lalu menyiapkan berkasnya sampai siap unggah.</p>
                </div>
                <div>
                    <p class="text-sm font-semibold">Jelajahi</p>
                    <ul class="mt-3 space-y-2 text-sm text-ink-2">
                        <li><a href="{{ url('/formasi') }}" class="hover:text-ink">Cari Formasi</a></li>
                        <li><a href="{{ url('/toolkit') }}" class="hover:text-ink">Toolkit Berkas</a></li>
                        <li><a href="{{ url('/harga') }}" class="hover:text-ink">Harga Pass</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-sm font-semibold">Tentang Data</p>
                    <p class="mt-3 text-sm leading-6 text-ink-2">Seluruh data formasi bersumber dari pengumuman resmi instansi dan ditampilkan dengan sumber serta tanggal verifikasinya. Data musim yang sudah selesai bersifat referensi historis, bukan prediksi untuk musim berikutnya.</p>
                </div>
            </div>
            <div class="mt-8 border-t border-line pt-5 text-[13px] text-ink-2">
                <p>&copy; {{ date('Y') }} PetaFormasi. Bukan situs resmi BKN atau instansi pemerintah mana pun.</p>
            </div>
        </div>
    </footer>

    {{-- Sistem toast (15 Nov 2026; hadir juga di layout publik sejak paket menu akun — halaman Pengaturan peserta melapor lewat toast yang sama): komponen Livewire memancarkan
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
