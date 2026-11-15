<x-layouts.public>
    <x-slot:title>Formasi CPNS untuk {{ $jurusan->jenjang }} {{ $jurusan->nama }} — Peta Formasi</x-slot:title>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <p class="text-sm text-ink-2">
            <a href="{{ route('formasi.index') }}" class="font-medium text-accent hover:underline">Cari Formasi</a>
            <span aria-hidden="true"> / </span> Jurusan
        </p>

        <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">
            Formasi untuk {{ $jurusan->jenjang }} {{ $jurusan->nama }}
        </h1>

        @if ($musim)
            <p class="mt-2 max-w-2xl text-[15px] leading-7 text-ink-2">
                <span class="tnum font-semibold text-ink">{{ number_format($formasi->count(), 0, ',', '.') }}</span>
                formasi menerima jurusan ini pada {{ $musim->label }} ({{ ucfirst($musim->status) }}).
                Daftar di bawah selalu gratis — detail syarat tiap formasi dibuka dengan pass.
                Untuk menyaring dan mengurutkan (0 pelamar, peluang, gaji), buka di
                <a href="{{ route('formasi.index', ['jurusan' => $jurusan->slug]) }}" class="font-medium text-accent hover:underline">halaman pencarian</a>.
            </p>

            @if ($musim->status === 'historis')
                <p class="mt-4 max-w-2xl rounded-lg bg-warning-soft px-3.5 py-2.5 text-sm text-warning">
                    Data {{ $musim->label }} bersifat historis — referensi strategi dari musim sebelumnya, bukan formasi yang sedang dibuka.
                </p>
            @endif

            @if ($formasi->isEmpty())
                <div class="mt-8 rounded-2xl border border-line bg-surface px-5 py-10 text-center">
                    <p class="text-[15px] font-semibold">Belum ada formasi terdata untuk jurusan ini</p>
                    <p class="mx-auto mt-1.5 max-w-md text-sm leading-6 text-ink-2">
                        Cakupan data bertambah instansi demi instansi. Kirim request lewat halaman pencarian agar jurusan ini diprioritaskan.
                    </p>
                </div>
            @else
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($formasi as $f)
                        @php $utama = $f->kuota->firstWhere('jenis_kebutuhan', 'umum') ?? $f->kuota->sortByDesc('jumlah')->first(); @endphp
                        <x-ui.kartu-formasi
                            :jabatan="$f->jabatan"
                            :instansi="$f->instansi->nama"
                            :penempatan="$f->penempatan"
                            :kebutuhan="$utama ? ucfirst($utama->jenis_kebutuhan) : null"
                            :kuota="$utama?->jumlah"
                            :total-kuota="$f->kuota->sum('jumlah')"
                            :musim-label="$f->musim->label . ' · ' . ucfirst($f->musim->status)"
                            :contoh="$f->kartu_contoh"
                            :href="route('formasi.show', $f->slug)"
                        />
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</x-layouts.public>
