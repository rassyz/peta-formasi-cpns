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
                        @if (auth()->user()->isAdmin())
                            <a href="{{ url('/admin') }}" class="inline-flex h-10 items-center gap-2 rounded-lg bg-ink px-4 text-sm font-medium text-white hover:bg-stone-800 transition-colors">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Panel Admin
                            </a>
                        @endif
                        @if (! auth()->user()->isAdmin())
                            <a href="{{ route('akun.pass') }}" class="inline-flex h-10 items-center gap-2 rounded-lg border border-line-strong px-4 text-sm font-medium transition-colors hover:bg-sunken"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 11v2"/><path d="M13 17v2"/></svg>Pass Saya</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-lg bg-ink px-4 text-sm font-medium text-white transition-colors hover:bg-stone-800">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg> Keluar
                            </button>
                        </form>
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
                        @if (auth()->user()->isAdmin())
                            <a href="{{ url('/admin') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>Panel Admin</a>
                        @endif
                        @if (! auth()->user()->isAdmin())
                            <a href="{{ route('akun.pass') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-line-strong px-5 text-sm font-medium transition-colors hover:bg-sunken"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 11v2"/><path d="M13 17v2"/></svg>Pass Saya</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>Keluar</button>
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

    @livewireScripts
</body>
</html>
