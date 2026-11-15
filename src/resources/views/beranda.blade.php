<x-layouts.public>
    <x-slot:title>Peta Formasi — Pilih Formasi CPNS yang Syaratnya Benar-Benar Lolos</x-slot:title>

    {{-- ============ Hero (frame 01) ============ --}}
    <section class="border-b border-line bg-kontur">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:grid lg:grid-cols-2 lg:items-center lg:gap-12 lg:px-8 lg:py-20">
            <div>
                <h1 class="text-3xl font-bold leading-tight tracking-tight sm:text-4xl lg:text-[44px] lg:leading-[1.15]">
                    Pilih formasi CPNS yang syaratnya benar-benar kamu penuhi.
                </h1>
                <p class="mt-4 max-w-lg text-[15px] leading-7 text-ink-2 sm:text-base">
                    Mulai dari jurusanmu — lihat semua formasi yang menerimanya, bandingkan peluangnya
                    dari data musim sebelumnya, lalu buka detail syaratnya sebelum mengunci pilihan.
                </p>

                <form method="GET" action="{{ route('formasi.index') }}" class="mt-7 max-w-lg">
                    <x-ui.combobox-jurusan name="jurusan" size="lg" :items="$jurusanItems" placeholder="Ketik nama jurusan kamu…" />
                    <button type="submit" class="mt-3 inline-flex h-12 w-full items-center justify-center rounded-lg bg-ink px-6 text-[15px] font-medium text-white transition-colors hover:bg-stone-800 sm:w-auto">
                        Cari Formasi
                    </button>
                </form>

                @if ($musim)
                    <p class="mt-4 text-[13px] text-ink-2">
                        Menampilkan data <span class="font-medium text-ink">{{ $musim->label }} · {{ ucfirst($musim->status) }}</span>
                        @if ($musim->status === 'historis')
                            — peta referensi strategi sambil menunggu pengumuman musim berikutnya.
                        @endif
                    </p>
                @endif
            </div>

            {{-- Bento statistik musim --}}
            @if ($musim)
                <div class="mt-10 grid grid-cols-2 gap-3 lg:mt-0">
                    <div class="col-span-2 rounded-2xl border border-line bg-surface p-5">
                        <p class="tnum text-4xl font-bold tracking-tight">{{ number_format($statistik['total_formasi'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm text-ink-2">formasi terdata pada {{ $musim->label }}</p>
                    </div>
                    <div class="rounded-2xl border border-accent/25 bg-accent-soft p-5">
                        <p class="tnum text-2xl font-bold tracking-tight text-accent">{{ number_format($statistik['formasi_nol_pelamar'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm font-medium text-ink">formasi berakhir 0 pelamar</p>
                        <p class="mt-0.5 text-[13px] text-ink-2">peluang paling sepi di musim ini</p>
                    </div>
                    <div class="rounded-2xl border border-line bg-surface p-5">
                        <p class="tnum text-2xl font-bold tracking-tight">{{ number_format($statistik['total_kuota'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm text-ink-2">total kuota</p>
                    </div>
                    <div class="rounded-2xl border border-line bg-surface p-5">
                        <p class="tnum text-2xl font-bold tracking-tight">{{ number_format($statistik['total_instansi'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm text-ink-2">instansi tercakup</p>
                    </div>
                    <div class="rounded-2xl border border-line bg-surface p-5">
                        <p class="tnum text-2xl font-bold tracking-tight">{{ number_format($totalJurusan, 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm text-ink-2">jurusan di daftar master</p>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- ============ Cara kerja ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold tracking-tight">Cara kerjanya</h2>
        {{-- Motif rute (DESIGN §12.1): garis putus-putus + pin bernomor, desktop saja --}}
        <div class="relative mt-8 hidden md:block" aria-hidden="true">
            <div class="absolute left-[16.66%] right-[16.66%] top-1/2 border-t-2 border-dashed border-line-strong"></div>
            <div class="relative grid grid-cols-3 gap-4">
                @foreach ([1, 2, 3] as $no)
                    <div class="flex justify-center">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-accent text-sm font-bold text-white ring-8 ring-canvas">{{ $no }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-line bg-surface p-5">
                <p class="text-sm font-semibold text-accent">Langkah 1</p>
                <h3 class="mt-1.5 font-semibold">Temukan formasimu — gratis</h3>
                <p class="mt-1.5 text-sm leading-6 text-ink-2">Pilih jurusan, lalu saring dan urutkan semua formasi yang menerimanya: filter 0 pelamar, pusat/daerah, peluang, gaji, kuota. Tanpa akun.</p>
            </div>
            <div class="rounded-2xl border border-line bg-surface p-5">
                <p class="text-sm font-semibold text-accent">Langkah 2</p>
                <h3 class="mt-1.5 font-semibold">Buka detailnya dengan pass</h3>
                <p class="mt-1.5 text-sm leading-6 text-ink-2">Syarat kunci, syarat khusus asli pengumuman, gaji rinci + sumbernya, dan riwayat pelamar — dibuka per musim dengan satu kode akses.</p>
            </div>
            <div class="rounded-2xl border border-line bg-surface p-5">
                <p class="text-sm font-semibold text-accent">Langkah 3</p>
                <h3 class="mt-1.5 font-semibold">Cek kelayakan &amp; siapkan berkas</h3>
                <p class="mt-1.5 text-sm leading-6 text-ink-2">Uji profilmu terhadap syarat formasi, ikuti checklist berkasnya, dan kompres file ke ukuran unggah dengan Toolkit — semua di satu tempat.</p>
            </div>
        </div>
    </section>

    {{-- ============ Contoh detail terbuka ============ --}}
    @if ($contoh->isNotEmpty())
        <section class="border-y border-line bg-sunken">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight">Contoh detail yang terbuka penuh</h2>
                        <p class="mt-1.5 max-w-xl text-[15px] text-ink-2">Kartu bertanda <span class="font-medium text-accent">Contoh</span> detailnya bisa dibuka siapa pun — persis isi yang didapat pemegang pass.</p>
                    </div>
                    <a href="{{ route('formasi.index') }}" class="text-sm font-medium text-accent hover:underline">Lihat semua formasi →</a>
                </div>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($contoh as $f)
                        @php $utama = $f->kuota->firstWhere('jenis_kebutuhan', 'umum') ?? $f->kuota->sortByDesc('jumlah')->first(); @endphp
                        <x-ui.kartu-formasi
                            :jabatan="$f->jabatan"
                            :instansi="$f->instansi->nama"
                            :penempatan="$f->penempatan"
                            :kebutuhan="$utama ? ucfirst($utama->jenis_kebutuhan) : null"
                            :kuota="$utama?->jumlah"
                            :total-kuota="$f->kuota->sum('jumlah')"
                            :musim-label="$f->musim->label . ' · ' . ucfirst($f->musim->status)"
                            :contoh="true"
                            :href="route('formasi.show', $f->slug)"
                        />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ Toolkit band ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex flex-col items-start justify-between gap-5 rounded-2xl border border-line bg-surface p-6 sm:p-8 lg:flex-row lg:items-center">
            <div class="max-w-xl">
                <h2 class="text-xl font-bold tracking-tight">Berkasmu kegedean untuk diunggah?</h2>
                <p class="mt-1.5 text-[15px] leading-7 text-ink-2">Kompres PDF dan foto ke 200 KB / 500 KB / 1 MB langsung di browser — gratis, tanpa akun, dan file tidak pernah meninggalkan perangkatmu.</p>
            </div>
            <a href="{{ route('toolkit') }}" class="inline-flex h-11 shrink-0 items-center rounded-lg border border-line-strong bg-surface px-5 text-sm font-medium transition-colors hover:bg-sunken">
                Buka Toolkit Berkas
            </a>
        </div>

        <div class="mt-6 flex flex-col items-start justify-between gap-5 rounded-2xl bg-ink p-6 text-white sm:p-8 lg:flex-row lg:items-center">
            <div class="max-w-xl">
                <h2 class="text-xl font-bold tracking-tight">Gratis untuk menemukan. Bayar untuk membuka.</h2>
                <p class="mt-1.5 text-[15px] leading-7 text-stone-300">Pass sekali bayar per musim, mulai Rp10.000 — tanpa langganan. Lihat isi tiap pass dan masa berlakunya di depan, sebelum membeli.</p>
            </div>
            <a href="{{ route('harga') }}" class="inline-flex h-11 shrink-0 items-center rounded-lg bg-white px-5 text-sm font-medium text-ink transition-colors hover:bg-stone-200">
                Lihat Harga Pass
            </a>
        </div>
    </section>
</x-layouts.public>
