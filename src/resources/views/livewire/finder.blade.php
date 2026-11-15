<div class="mx-auto max-w-7xl px-4 pt-8 pb-24 sm:px-6 lg:px-8 lg:pb-8">
    {{-- Kepala Finder --}}
    <div class="border-b border-line pb-5">
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Cari Formasi</h1>
        <p class="mt-1.5 text-[15px] text-ink-2">
            Semua fungsi daftar ini gratis — telusuri formasinya, lalu persempit menurut jurusanmu.
        </p>
    </div>

    <div x-data="{ filterTerbuka: false }">
        {{-- Backdrop bottom sheet filter (mobile saja) --}}
        <div x-show="filterTerbuka" x-cloak @click="filterTerbuka = false" class="fixed inset-0 z-40 bg-ink/40 lg:hidden"></div>

        <div class="items-start gap-6 lg:mt-6 lg:grid lg:grid-cols-[280px_minmax(0,1fr)]">
            {{-- Sidebar: cek kelayakan + semua filter --}}
            <aside :class="filterTerbuka ? 'max-lg:fixed max-lg:inset-x-3 max-lg:bottom-3 max-lg:z-50 max-lg:mt-0 max-lg:max-h-[82vh] max-lg:overflow-y-auto max-lg:shadow-float' : 'max-lg:hidden'"
                class="mt-4 rounded-2xl border border-line bg-surface p-5 shadow-card lg:mt-0 lg:block"
                aria-label="Filter formasi">
                {{-- Kepala sheet (mobile saja) --}}
                <div class="mb-4 flex items-center justify-between lg:hidden">
                    <span class="flex items-center gap-2 text-base font-semibold">
                        <svg class="h-5 w-5 text-ink-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 5h18l-7 8v5l-4 2v-7L3 5Z"/></svg>
                        Filter
                    </span>
                    <button type="button" @click="filterTerbuka = false" aria-label="Tutup filter"
                        class="flex h-9 w-9 items-center justify-center rounded-full text-ink-2 transition-colors hover:bg-sunken hover:text-ink">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <div>
                    {{-- Pemilih jurusan --}}
                    <div wire:key="combobox-{{ $jurusan?->id ?? 'none' }}" class="relative" x-data="{
                        open: false,
                        query: @js($jurusan?->nama ?? ''),
                        items: @js($jurusanItems),
                        get filtered() {
                            const q = this.query.trim().toLowerCase();
                            if (!q) return this.items;
                            return this.items.filter(i => i.label.toLowerCase().includes(q));
                        }
                    }" @click.away="open = false" @keydown.escape="open = false">
                        <label for="finder_jurusan_input" class="mb-1.5 block text-sm font-semibold">Jurusan kamu</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                            <input id="finder_jurusan_input" type="text" x-model="query" @focus="open = true" @input="open = true"
                                placeholder="Ketik nama jurusan kamu…"
                                autocomplete="off" role="combobox" :aria-expanded="open"
                                class="h-10 w-full rounded-lg border border-line-strong bg-surface pl-11 pr-10 text-sm text-ink placeholder:text-ink-3 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent">
                            <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-3 transition-transform" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                        <ul x-show="open" x-cloak class="absolute inset-x-0 top-full z-30 mt-2 max-h-72 overflow-auto rounded-xl border border-line bg-surface py-1.5 shadow-float" role="listbox">
                            <template x-for="item in filtered" :key="item.value">
                                <li>
                                    <button type="button" @click="query = item.label; open = false; $wire.set('jurusanSlug', item.value); filterTerbuka = false"
                                        class="flex w-full items-center justify-between gap-3 px-3.5 py-2.5 text-left text-[15px] hover:bg-sunken" role="option">
                                        <span x-text="item.label"></span>
                                        <span class="text-xs text-ink-2" x-text="item.jenjang"></span>
                                    </button>
                                </li>
                            </template>
                            <li x-show="filtered.length === 0" class="px-3.5 py-3 text-sm text-ink-2">Jurusan tidak ditemukan. Coba kata kunci lain.</li>
                        </ul>
                    </div>
                </div>

                {{-- Musim --}}
                <div class="mt-6 border-t border-line pt-5">
                    <label for="finder_musim" class="mb-1.5 block text-sm font-semibold">Musim</label>
                    <select id="finder_musim" wire:model.live="musimId" class="h-10 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                        @foreach ($musimList as $m)
                            <option value="{{ $m->id }}">{{ $m->label }} · {{ ucfirst($m->status) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Wilayah --}}
                <div class="mt-6 border-t border-line pt-5">
                    <h3 class="text-sm font-semibold">Wilayah</h3>
                    <div class="mt-2.5 flex flex-col gap-1" role="group" aria-label="Wilayah">
                        @foreach (['' => 'Semua wilayah', 'pusat' => 'Pusat', 'daerah' => 'Daerah'] as $nilai => $label)
                            <button type="button" wire:click="$set('wilayah', '{{ $nilai }}')"
                                class="rounded-lg px-3 py-2 text-left text-sm transition-colors {{ $wilayah === $nilai ? 'bg-ink font-medium text-white' : 'text-ink-2 hover:bg-sunken hover:text-ink' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Instansi --}}
                <div class="mt-6 border-t border-line pt-5">
                    <label for="finder_instansi" class="mb-1.5 block text-sm font-semibold">Instansi</label>
                    <select id="finder_instansi" wire:model.live="instansiId" class="h-10 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                        <option value="">Semua instansi</option>
                        @foreach ($instansiList as $instansi)
                            <option value="{{ $instansi->id }}">{{ $instansi->nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Penempatan --}}
                <div class="mt-6 border-t border-line pt-5">
                    <label for="finder_penempatan" class="mb-1.5 block text-sm font-semibold">Penempatan</label>
                    <select id="finder_penempatan" wire:model.live="penempatan" class="h-10 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                        <option value="">Semua penempatan</option>
                        @foreach ($penempatanList as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Jenis kebutuhan --}}
                <div class="mt-6 border-t border-line pt-5">
                    <h3 class="text-sm font-semibold">Jenis Kebutuhan</h3>
                    <div class="mt-2.5 flex flex-col gap-1" role="group" aria-label="Jenis kebutuhan">
                        <button type="button" wire:click="$set('jenis', '')"
                            class="rounded-lg px-3 py-2 text-left text-sm transition-colors {{ $jenis === '' ? 'bg-ink font-medium text-white' : 'text-ink-2 hover:bg-sunken hover:text-ink' }}">
                            Semua kebutuhan
                        </button>
                        @foreach ($jenisList as $j)
                            <button type="button" wire:click="$set('jenis', '{{ $j }}')"
                                class="rounded-lg px-3 py-2 text-left text-sm transition-colors {{ $jenis === $j ? 'bg-ink font-medium text-white' : 'text-ink-2 hover:bg-sunken hover:text-ink' }}">
                                {{ ucfirst($j) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Hanya 0 pelamar --}}
                <div class="mt-6 border-t border-line pt-5">
                    <button type="button" wire:click="$toggle('hanyaNolPelamar')"
                        class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2 text-left text-sm transition-colors hover:bg-sunken"
                        aria-pressed="{{ $hanyaNolPelamar ? 'true' : 'false' }}">
                        <span class="{{ $hanyaNolPelamar ? 'font-medium text-ink' : 'text-ink-2' }}">Hanya 0 pelamar</span>
                        <span class="relative h-6 w-11 shrink-0 rounded-full transition-colors {{ $hanyaNolPelamar ? 'bg-ink' : 'bg-line-strong' }}">
                            <span class="absolute top-0.5 h-5 w-5 rounded-full bg-white shadow transition-all {{ $hanyaNolPelamar ? 'left-5.5' : 'left-0.5' }}"></span>
                        </span>
                    </button>
                </div>
            </aside>

            {{-- Area hasil --}}
            <main class="mt-6 min-w-0 lg:mt-0">
                {{-- Baris atas: pencarian nama + urutan --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative min-w-0 flex-1">
                        <input id="finder_nama_input" type="text" wire:model.live.debounce.300ms="kataKunci"
                            placeholder="Cari nama formasi, penempatan, atau instansi…"
                            autocomplete="off" aria-label="Cari nama formasi"
                            class="h-10 w-full rounded-lg border border-line-strong bg-surface pl-3.5 pr-18 text-sm text-ink placeholder:text-ink-3 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                        @if (trim($kataKunci) !== '')
                            <button type="button" wire:click="$set('kataKunci', '')" aria-label="Bersihkan pencarian nama"
                                class="absolute right-12 top-1/2 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full text-ink-3 transition-colors hover:bg-sunken hover:text-ink">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                        @endif
                        <span class="pointer-events-none absolute right-0 top-0 flex h-full w-11 items-center justify-center border-l border-line-strong text-ink-2" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                        </span>
                    </div>
                    <label class="flex h-10 items-center gap-2 rounded-lg border border-line-strong bg-surface px-3 sm:w-60">
                        <span class="shrink-0 text-sm text-ink-2">Urutkan:</span>
                        <select wire:model.live="urut" class="w-full min-w-0 bg-transparent text-sm font-medium text-ink focus:outline-none" aria-label="Urutkan hasil">
                            <option value="peluang">Peluang tertinggi</option>
                            <option value="gaji">Gaji tertinggi</option>
                            <option value="kuota">Kuota terbesar</option>
                        </select>
                    </label>
                </div>

                {{-- Chip filter aktif (muncul hanya bila ada saringan yang menyala) --}}
                @php
                    $chipInstansi = $instansiId !== '' ? $instansiList->firstWhere('id', (int) $instansiId) : null;
                    $adaFilterAktif = $jurusan || trim($kataKunci) !== '' || $wilayah !== '' || $chipInstansi || $penempatan !== '' || $jenis !== '' || $hanyaNolPelamar;
                @endphp
                @if ($adaFilterAktif)
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @if ($jurusan)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sunken py-1.5 pl-3.5 pr-2 text-sm">
                                {{ $jurusan->jenjang }} {{ $jurusan->nama }}
                                <button type="button" wire:click="$set('jurusanSlug', '')" aria-label="Hapus filter jurusan"
                                    class="flex h-5 w-5 items-center justify-center rounded-full text-ink-2 transition-colors hover:bg-line hover:text-ink">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                </button>
                            </span>
                        @endif
                        @if (trim($kataKunci) !== '')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sunken py-1.5 pl-3.5 pr-2 text-sm">
                                &ldquo;{{ trim($kataKunci) }}&rdquo;
                                <button type="button" wire:click="$set('kataKunci', '')" aria-label="Hapus kata kunci pencarian"
                                    class="flex h-5 w-5 items-center justify-center rounded-full text-ink-2 transition-colors hover:bg-line hover:text-ink">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                </button>
                            </span>
                        @endif
                        @if ($wilayah !== '')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sunken py-1.5 pl-3.5 pr-2 text-sm">
                                {{ ucfirst($wilayah) }}
                                <button type="button" wire:click="$set('wilayah', '')" aria-label="Hapus filter wilayah"
                                    class="flex h-5 w-5 items-center justify-center rounded-full text-ink-2 transition-colors hover:bg-line hover:text-ink">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                </button>
                            </span>
                        @endif
                        @if ($chipInstansi)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sunken py-1.5 pl-3.5 pr-2 text-sm">
                                {{ $chipInstansi->nama }}
                                <button type="button" wire:click="$set('instansiId', '')" aria-label="Hapus filter instansi"
                                    class="flex h-5 w-5 items-center justify-center rounded-full text-ink-2 transition-colors hover:bg-line hover:text-ink">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                </button>
                            </span>
                        @endif
                        @if ($penempatan !== '')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sunken py-1.5 pl-3.5 pr-2 text-sm">
                                {{ $penempatan }}
                                <button type="button" wire:click="$set('penempatan', '')" aria-label="Hapus filter penempatan"
                                    class="flex h-5 w-5 items-center justify-center rounded-full text-ink-2 transition-colors hover:bg-line hover:text-ink">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                </button>
                            </span>
                        @endif
                        @if ($jenis !== '')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sunken py-1.5 pl-3.5 pr-2 text-sm">
                                {{ ucfirst($jenis) }}
                                <button type="button" wire:click="$set('jenis', '')" aria-label="Hapus filter jenis kebutuhan"
                                    class="flex h-5 w-5 items-center justify-center rounded-full text-ink-2 transition-colors hover:bg-line hover:text-ink">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                </button>
                            </span>
                        @endif
                        @if ($hanyaNolPelamar)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sunken py-1.5 pl-3.5 pr-2 text-sm">
                                0 pelamar
                                <button type="button" wire:click="$set('hanyaNolPelamar', false)" aria-label="Matikan saringan 0 pelamar"
                                    class="flex h-5 w-5 items-center justify-center rounded-full text-ink-2 transition-colors hover:bg-line hover:text-ink">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                </button>
                            </span>
                        @endif
                        <button type="button" wire:click="resetFilter" class="px-1 text-sm font-medium text-accent transition-colors hover:text-accent-hover hover:underline">
                            Reset Filter
                        </button>
                    </div>
                @endif

                @if ($jurusan || $modeNama || $modeSemua)
                    {{-- Ringkasan hasil --}}
                    <p class="mt-3.5 text-sm text-ink-2" wire:loading.class="opacity-50">
                        <span class="tnum font-semibold text-ink">{{ number_format($hasil->total(), 0, ',', '.') }}</span>
                        @if ($jurusan)
                            formasi dari <span class="tnum font-semibold text-ink">{{ number_format($totalMusim, 0, ',', '.') }}</span> formasi
                            untuk <span class="font-semibold text-ink">{{ $jurusan->jenjang }} {{ $jurusan->nama }}</span>
                            pada {{ $musimAktif?->label }}
                        @elseif ($modeNama)
                            formasi dari <span class="tnum font-semibold text-ink">{{ number_format($totalMusim, 0, ',', '.') }}</span> formasi
                            cocok dengan kata kunci <span class="font-semibold text-ink">&ldquo;{{ trim($kataKunci) }}&rdquo;</span>
                            pada {{ $musimAktif?->label }} — lintas semua jurusan
                        @elseif ($hasil->total() < $totalMusim)
                            formasi dari <span class="tnum font-semibold text-ink">{{ number_format($totalMusim, 0, ',', '.') }}</span> formasi
                            pada {{ $musimAktif?->label }}
                        @else
                            formasi pada {{ $musimAktif?->label }} — tampil semua, pilih jurusanmu untuk mempersempit
                        @endif
                        @if ($jumlahNol > 0)
                            · <span class="tnum font-semibold text-ink">{{ number_format($jumlahNol, 0, ',', '.') }}</span> di antaranya berakhir 0 pelamar
                        @endif
                    </p>

                    @if ($musimAktif?->status === 'historis')
                        <p class="mt-3 rounded-lg bg-warning-soft px-3 py-2 text-[13px] text-warning">
                            Data {{ $musimAktif->label }} bersifat historis — referensi strategi dari musim sebelumnya, bukan formasi yang sedang dibuka.
                        </p>
                    @endif

                    @if ($jurusan)
                {{-- Panel Cek Kelayakan (Modul B) --}}
                <section class="mt-4 rounded-xl border border-accent/25 bg-accent-soft p-4 sm:p-5" aria-labelledby="judul-cek-kelayakan">
                    @if (! $bolehCek)
                        <div class="flex flex-wrap items-center gap-x-5 gap-y-3">
                            <div class="min-w-0 flex-1 basis-64">
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <h2 id="judul-cek-kelayakan" class="text-base font-semibold tracking-tight">Cek Kelayakan</h2>
                                    <x-ui.badge variant="accent">Pass Strategi</x-ui.badge>
                                </div>
                                <p class="mt-1 text-sm leading-6 text-ink-2">
                                    Nilai profilmu terhadap setiap formasi di daftar ini — khusus pemegang
                                    <span class="font-medium text-ink">Pass Strategi {{ $musimAktif?->label }}</span>.
                                    Pass Historis membuka detail, tanpa cek kelayakan.
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2.5">
                                @guest
                                    <a href="{{ route('login') }}" class="inline-flex h-10 items-center gap-2 rounded-lg bg-ink px-4 text-sm font-medium text-white transition-colors hover:bg-stone-800"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="m10 17 5-5-5-5"/><path d="M15 12H3"/></svg>Masuk</a>
                                    <a href="{{ route('harga') }}" class="inline-flex h-10 items-center rounded-lg border border-line-strong bg-surface px-4 text-sm font-medium transition-colors hover:bg-sunken">Lihat Pass Strategi</a>
                                @else
                                    @if (Route::has('akun.redeem'))
                                        <a href="{{ route('akun.redeem') }}" class="inline-flex h-10 items-center gap-2 rounded-lg bg-ink px-4 text-sm font-medium text-white transition-colors hover:bg-stone-800"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 11v2"/><path d="M13 17v2"/></svg>Redeem Kode Strategi</a>
                                    @endif
                                    <a href="{{ route('harga') }}" class="inline-flex h-10 items-center rounded-lg border border-line-strong bg-surface px-4 text-sm font-medium transition-colors hover:bg-sunken">Lihat Pass</a>
                                @endguest
                            </div>
                        </div>
                    @else
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h2 id="judul-cek-kelayakan" class="text-lg font-semibold tracking-tight">Cek Kelayakan</h2>
                            <x-ui.badge variant="accent">Pass Strategi</x-ui.badge>
                            @if ($profilAktif && $bolehCek)
                                <x-ui.badge variant="success">Sedang aktif</x-ui.badge>
                            @endif
                        </div>
                        <p class="mt-2 max-w-2xl text-[15px] leading-7 text-ink-2">
                            Isi profilmu sekali, lalu setiap kartu dinilai memakai tanggal acuan usia {{ $musimAktif?->label }}.
                            Profil <span class="font-medium text-ink">tidak disimpan di database</span> — hanya hidup di sesi browser ini.
                        </p>

                        @if ($pesanProfil)
                            <p class="mt-4 rounded-lg bg-surface px-3.5 py-2.5 text-sm font-medium text-ink">{{ $pesanProfil }}</p>
                        @endif

                        <form wire:submit="jalankanCek" class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label for="profil-tanggal-lahir" class="mb-1.5 block text-sm font-medium">Tanggal lahir</label>
                                <input id="profil-tanggal-lahir" type="date" wire:model="profilTanggalLahir"
                                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                                @error('profilTanggalLahir') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="profil-ipk" class="mb-1.5 block text-sm font-medium">IPK</label>
                                <input id="profil-ipk" type="text" inputmode="decimal" wire:model="profilIpk" placeholder="Mis. 3,20"
                                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                                @error('profilIpk') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="profil-jenis" class="mb-1.5 block text-sm font-medium">Jenis kebutuhan yang dilamar</label>
                                <select id="profil-jenis" wire:model="profilJenis"
                                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                                    @foreach ($jenisProfilList as $jenisProfil)
                                        <option value="{{ $jenisProfil }}">{{ ucfirst($jenisProfil) }}</option>
                                    @endforeach
                                </select>
                                @error('profilJenis') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="profil-dokumen" class="mb-1.5 block text-sm font-medium">Dokumen kelulusan saat ini</label>
                                <select id="profil-dokumen" wire:model="profilDokumen"
                                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                                    <option value="ijazah">Sudah punya ijazah</option>
                                    <option value="skl">Baru punya SKL</option>
                                </select>
                                @error('profilDokumen') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="profil-akreditasi" class="mb-1.5 block text-sm font-medium">Akreditasi prodi saat lulus</label>
                                <select id="profil-akreditasi" wire:model="profilAkreditasi"
                                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                                    <option value="terakreditasi">Terakreditasi</option>
                                    <option value="tidak">Tidak terakreditasi</option>
                                    <option value="tidak_tahu">Tidak tahu</option>
                                </select>
                                @error('profilAkreditasi') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex items-end gap-2.5">
                                <button type="submit" wire:loading.attr="disabled"
                                    class="inline-flex h-11 flex-1 items-center justify-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800 disabled:opacity-60">
                                    <span wire:loading.remove wire:target="jalankanCek">{{ $profilAktif ? 'Perbarui Cek' : 'Jalankan Cek' }}</span>
                                    <span wire:loading wire:target="jalankanCek">Menilai…</span>
                                </button>
                                @if ($profilAktif)
                                    <button type="button" wire:click="hapusProfil"
                                        class="inline-flex h-11 items-center rounded-lg border border-line-strong bg-surface px-4 text-sm font-medium transition-colors hover:bg-sunken">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </form>

                        @if ($profilAktif)
                            <label class="mt-5 flex cursor-pointer items-start gap-2.5 text-sm font-medium">
                                <input type="checkbox" wire:model.live="hanyaLolos" class="mt-0.5 h-4 w-4 rounded border-line-strong accent-stone-900">
                                <span>
                                    Hanya tampilkan yang lolos syarat saya
                                    <span class="block text-[13px] font-normal leading-5 text-ink-2">Daftar asli tidak diubah permanen — matikan saringan ini untuk melihat semua formasi lagi.</span>
                                </span>
                            </label>
                        @endif
                    @endif
                </section>
                @endif


                    @if ($modeNama && ! $jurusan)
                    {{-- Strip info mode nama: cek kelayakan mensyaratkan jurusan terpilih --}}
                    <section class="mt-5 rounded-2xl border border-line bg-surface p-5">
                        <h2 class="text-lg font-semibold tracking-tight">Kamu mencari berdasarkan nama formasi</h2>
                        <p class="mt-2 max-w-2xl text-[15px] leading-7 text-ink-2">
                            Hasil di bawah adalah semua formasi yang namanya cocok pada musim ini, lintas jurusan — kartu tetap bisa
                            dibuka seperti biasa. Untuk menyalakan <span class="font-medium text-ink">cek kelayakan</span> dan badge
                            syarat pada kartu, pilih jurusanmu di panel filter; kata kunci nama ini tetap berlaku sebagai saringan.
                        </p>
                    </section>
                    @endif

                    {{-- Hasil --}}
                    <div wire:loading.class="opacity-60" class="transition-opacity">
                        @if ($hasil->isEmpty())
                            <div class="mt-6 rounded-2xl border border-line bg-surface px-5 py-10 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-sunken text-ink-2">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                                </span>
                                <p class="mt-4 text-[15px] font-semibold">Tidak ada formasi yang cocok dengan filter ini</p>
                                <p class="mx-auto mt-1.5 max-w-md text-sm leading-6 text-ink-2">
                                    Coba longgarkan filter, atau kirim request formasi di bawah — request terbanyak diprioritaskan untuk didata berikutnya.
                                </p>
                                <a href="{{ route('formasi.index') }}" class="mt-5 inline-flex h-10 items-center rounded-lg border border-line-strong px-4 text-sm font-medium transition-colors hover:bg-sunken">
                                    Mulai ulang pencarian
                                </a>
                            </div>
                        @else
                            <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach ($hasil as $f)
                                    @php
                                        $utama = $f->kuota->firstWhere('jenis_kebutuhan', $jenis !== '' ? $jenis : 'umum') ?? $f->kuota->sortByDesc('jumlah')->first();
                                    @endphp
                                    @php $cekKartu = $kelayakanPeta[$f->id] ?? null; @endphp
                                    <x-ui.kartu-formasi
                                        :jabatan="$f->jabatan"
                                        :instansi="$f->instansi->nama"
                                        :penempatan="$f->penempatan"
                                        :wilayah="ucfirst($f->instansi->wilayah)"
                                        :kebutuhan="$utama ? ucfirst($utama->jenis_kebutuhan) : null"
                                        :kuota="$utama?->jumlah"
                                        :pelamar-ms="($bolehLihatPelamar || $f->kartu_contoh) ? $utama?->snapshotTerakhir?->jumlah_ms : null"
                                        :pelamar-terkunci="! $bolehLihatPelamar && ! $f->kartu_contoh && $utama?->snapshotTerakhir !== null"
                                        :musim-label="$f->musim->label . ' · ' . ucfirst($f->musim->status)"
                                        :tampilkan-musim="false"
                                        :status="$cekKartu['status'] ?? null"
                                        :alasan="$cekKartu['alasan'] ?? null"
                                        :contoh="$f->kartu_contoh"
                                        :href="route('formasi.show', $f->slug)"
                                    />
                                @endforeach
                            </div>

                            <div class="mt-6">
                                {{ $hasil->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </main>
        </div>

        {{-- Tombol filter melayang (mobile saja) --}}
        <button type="button" x-show="!filterTerbuka" x-cloak @click="filterTerbuka = true"
            class="fixed bottom-5 left-1/2 z-30 flex h-12 -translate-x-1/2 items-center gap-2 rounded-full bg-accent px-6 text-[15px] font-semibold text-white shadow-float transition-colors hover:bg-accent-hover lg:hidden">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 5h18l-7 8v5l-4 2v-7L3 5Z"/></svg>
            Filter
        </button>
    </div>

    {{-- Request formasi --}}
    <div class="mt-10">
        <livewire:request-formasi-form :musim-id="$musimId" />
    </div>
</div>
