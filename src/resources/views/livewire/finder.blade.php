<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    {{-- Kepala Finder --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Cari Formasi</h1>
            <p class="mt-1.5 text-[15px] text-ink-2">
                Semua fungsi daftar ini gratis — pilih jurusanmu, formasinya langsung tampil.
            </p>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <span class="font-medium text-ink-2">Musim</span>
            <select wire:model.live="musimId" class="h-10 rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                @foreach ($musimList as $m)
                    <option value="{{ $m->id }}">{{ $m->label }} · {{ ucfirst($m->status) }}</option>
                @endforeach
            </select>
        </label>
    </div>

    {{-- Pemilih jurusan --}}
    <div class="relative mt-6 max-w-2xl" x-data="{
        open: false,
        query: @js($jurusan?->nama ?? ''),
        items: @js($jurusanItems),
        get filtered() {
            const q = this.query.trim().toLowerCase();
            if (!q) return this.items;
            return this.items.filter(i => i.label.toLowerCase().includes(q));
        }
    }" @click.away="open = false" @keydown.escape="open = false">
        <label for="finder_jurusan_input" class="mb-1.5 block text-sm font-medium">Jurusan kamu</label>
        <div class="relative">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input id="finder_jurusan_input" type="text" x-model="query" @focus="open = true" @input="open = true"
                placeholder="Ketik nama jurusan kamu…"
                autocomplete="off" role="combobox" :aria-expanded="open"
                class="h-13 w-full rounded-lg border border-line-strong bg-surface pl-11 pr-10 text-base text-ink placeholder:text-ink-3 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent">
            <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-3 transition-transform" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
        </div>
        <ul x-show="open" x-cloak class="absolute inset-x-0 top-full z-30 mt-2 max-h-72 overflow-auto rounded-xl border border-line bg-surface py-1.5 shadow-float" role="listbox">
            <template x-for="item in filtered" :key="item.value">
                <li>
                    <button type="button" @click="query = item.label; open = false; $wire.set('jurusanSlug', item.value)"
                        class="flex w-full items-center justify-between gap-3 px-3.5 py-2.5 text-left text-[15px] hover:bg-sunken" role="option">
                        <span x-text="item.label"></span>
                        <span class="text-xs text-ink-2" x-text="item.jenjang"></span>
                    </button>
                </li>
            </template>
            <li x-show="filtered.length === 0" class="px-3.5 py-3 text-sm text-ink-2">Jurusan tidak ditemukan. Coba kata kunci lain.</li>
        </ul>
    </div>

    {{-- Pencarian nama formasi (amendemen FR-A3): berdiri sendiri atau menyaring hasil jurusan --}}
    <div class="mt-4 max-w-2xl">
        <label for="finder_nama_input" class="mb-1.5 block text-sm font-medium">Cari nama formasi</label>
        <div class="relative">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input id="finder_nama_input" type="text" wire:model.live.debounce.300ms="kataKunci"
                placeholder="Mis. Pemeriksa, Pranata Komputer, Auditor…"
                autocomplete="off"
                class="h-11 w-full rounded-lg border border-line-strong bg-surface pl-11 pr-10 text-[15px] text-ink placeholder:text-ink-3 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
            @if (trim($kataKunci) !== '')
                <button type="button" wire:click="$set('kataKunci', '')" aria-label="Bersihkan pencarian nama"
                    class="absolute right-2.5 top-1/2 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full text-ink-3 transition-colors hover:bg-sunken hover:text-ink">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            @endif
        </div>
        <p class="mt-1.5 text-[13px] text-ink-3">Mencari pada nama jabatan, penempatan, dan instansi — bisa berdiri sendiri tanpa memilih jurusan, atau menjadi saringan tambahan pada hasil jurusanmu.</p>
    </div>

    @if ($jurusan || $modeNama)
        {{-- Ringkasan hasil --}}
        <p class="mt-6 text-[15px] text-ink-2" wire:loading.class="opacity-50">
            <span class="tnum font-semibold text-ink">{{ number_format($hasil->total(), 0, ',', '.') }}</span>
            @if ($jurusan)
                formasi untuk <span class="font-semibold text-ink">{{ $jurusan->jenjang }} {{ $jurusan->nama }}</span>
                pada {{ $musimAktif?->label }}
            @else
                formasi cocok dengan kata kunci <span class="font-semibold text-ink">&ldquo;{{ trim($kataKunci) }}&rdquo;</span>
                pada {{ $musimAktif?->label }} — lintas semua jurusan
            @endif
            @if ($jumlahNol > 0)
                · <span class="tnum font-semibold text-ink">{{ number_format($jumlahNol, 0, ',', '.') }}</span> di antaranya berakhir 0 pelamar
            @endif
        </p>

        {{-- Bilah filter --}}
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <div class="flex flex-wrap gap-1.5" role="group" aria-label="Wilayah">
                @foreach (['' => 'Semua wilayah', 'pusat' => 'Pusat', 'daerah' => 'Daerah'] as $nilai => $label)
                    <button type="button" wire:click="$set('wilayah', '{{ $nilai }}')"
                        class="rounded-full border px-3.5 py-1.5 text-sm font-medium transition-colors {{ $wilayah === $nilai ? 'border-ink bg-ink text-white' : 'border-line-strong hover:bg-sunken' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <select wire:model.live="instansiId" class="h-9 rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30" aria-label="Instansi">
                <option value="">Semua instansi</option>
                @foreach ($instansiList as $instansi)
                    <option value="{{ $instansi->id }}">{{ $instansi->nama }}</option>
                @endforeach
            </select>

            <div class="flex flex-wrap gap-1.5" role="group" aria-label="Jenis kebutuhan">
                <button type="button" wire:click="$set('jenis', '')"
                    class="rounded-full border px-3.5 py-1.5 text-sm font-medium transition-colors {{ $jenis === '' ? 'border-ink bg-ink text-white' : 'border-line-strong hover:bg-sunken' }}">
                    Semua kebutuhan
                </button>
                @foreach ($jenisList as $j)
                    <button type="button" wire:click="$set('jenis', '{{ $j }}')"
                        class="rounded-full border px-3.5 py-1.5 text-sm font-medium transition-colors {{ $jenis === $j ? 'border-ink bg-ink text-white' : 'border-line-strong hover:bg-sunken' }}">
                        {{ ucfirst($j) }}
                    </button>
                @endforeach
            </div>

            <button type="button" wire:click="$toggle('hanyaNolPelamar')"
                class="rounded-full border px-3.5 py-1.5 text-sm font-medium transition-colors {{ $hanyaNolPelamar ? 'border-ink bg-ink text-white' : 'border-line-strong hover:bg-sunken' }}"
                aria-pressed="{{ $hanyaNolPelamar ? 'true' : 'false' }}">
                0 pelamar
            </button>

            <label class="ml-auto flex items-center gap-2 text-sm">
                <span class="font-medium text-ink-2">Urutkan</span>
                <select wire:model.live="urut" class="h-9 rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    <option value="peluang">Peluang tertinggi</option>
                    <option value="gaji">Gaji tertinggi</option>
                    <option value="kuota">Kuota terbesar</option>
                </select>
            </label>
        </div>

        @if ($musimAktif?->status === 'historis')
            <p class="mt-4 rounded-lg bg-warning-soft px-3.5 py-2.5 text-sm text-warning">
                Data {{ $musimAktif->label }} bersifat historis — referensi strategi dari musim sebelumnya, bukan formasi yang sedang dibuka.
            </p>
        @endif

        @if ($jurusan)
        {{-- Panel Cek Kelayakan (Modul B) --}}
        <section class="mt-5 rounded-2xl border border-accent/25 bg-accent-soft p-5 sm:p-6" aria-labelledby="judul-cek-kelayakan">
            <div class="flex flex-wrap items-center gap-2.5">
                <h2 id="judul-cek-kelayakan" class="text-lg font-semibold tracking-tight">Cek Kelayakan</h2>
                <x-ui.badge variant="accent">Pass Strategi</x-ui.badge>
                @if ($profilAktif && $bolehCek)
                    <x-ui.badge variant="success">Sedang aktif</x-ui.badge>
                @endif
            </div>

            @if (! $bolehCek)
                <p class="mt-2 max-w-2xl text-[15px] leading-7 text-ink-2">
                    Nilai usia, jurusan, IPK, dokumen kelulusan, akreditasi, dan jenis kebutuhanmu terhadap setiap formasi dalam daftar ini.
                    Fitur ini khusus pemegang <span class="font-medium text-ink">Pass Strategi untuk {{ $musimAktif?->label }}</span>;
                    Pass Historis membuka detail, tetapi tidak termasuk cek kelayakan.
                </p>
                <div class="mt-4 flex flex-wrap gap-2.5">
                    @guest
                        <a href="{{ route('login') }}" class="inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800">Masuk</a>
                        <a href="{{ route('harga') }}" class="inline-flex h-11 items-center rounded-lg border border-line-strong bg-surface px-5 text-sm font-medium transition-colors hover:bg-sunken">Lihat Pass Strategi</a>
                    @else
                        @if (Route::has('akun.redeem'))
                            <a href="{{ route('akun.redeem') }}" class="inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800">Redeem Kode Strategi</a>
                        @endif
                        <a href="{{ route('harga') }}" class="inline-flex h-11 items-center rounded-lg border border-line-strong bg-surface px-5 text-sm font-medium transition-colors hover:bg-sunken">Lihat Pass</a>
                    @endguest
                </div>
            @else
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
        @elseif ($modeNama)
        {{-- Strip info mode nama: cek kelayakan mensyaratkan jurusan terpilih --}}
        <section class="mt-5 rounded-2xl border border-line bg-surface p-5 sm:p-6">
            <h2 class="text-lg font-semibold tracking-tight">Kamu mencari berdasarkan nama formasi</h2>
            <p class="mt-2 max-w-2xl text-[15px] leading-7 text-ink-2">
                Hasil di bawah adalah semua formasi yang namanya cocok pada musim ini, lintas jurusan — kartu tetap bisa
                dibuka seperti biasa. Untuk menyalakan <span class="font-medium text-ink">cek kelayakan</span> dan badge
                syarat pada kartu, pilih jurusanmu di kolom paling atas; kata kunci nama ini tetap berlaku sebagai saringan.
            </p>
        </section>
        @endif

        {{-- Hasil --}}
        <div wire:loading.class="opacity-60" class="transition-opacity">
            @if ($hasil->isEmpty())
                <div class="mt-8 rounded-2xl border border-line bg-surface px-5 py-10 text-center">
                    <p class="text-[15px] font-semibold">Tidak ada formasi yang cocok dengan filter ini</p>
                    <p class="mx-auto mt-1.5 max-w-md text-sm leading-6 text-ink-2">
                        Coba longgarkan filter, atau kirim request formasi di bawah — request terbanyak diprioritaskan untuk didata berikutnya.
                    </p>
                </div>
            @else
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
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
                            :musim-label="$f->musim->label . ' · ' . ucfirst($f->musim->status)"
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
    @else
        <div class="mt-8 rounded-2xl border border-line bg-surface px-5 py-10 text-center">
            <p class="text-[15px] font-semibold">Mulai dari jurusanmu</p>
            <p class="mx-auto mt-1.5 max-w-md text-sm leading-6 text-ink-2">
                Ketik nama jurusan di atas — semua formasi yang menerimanya pada musim terpilih langsung tampil, tanpa pertanyaan lain.
                Sudah tahu nama formasinya? Pakai kolom pencarian nama formasi tepat di bawahnya.
            </p>
        </div>
    @endif

    {{-- Request formasi --}}
    <div class="mt-10">
        <livewire:request-formasi-form :musim-id="$musimId" />
    </div>
</div>
