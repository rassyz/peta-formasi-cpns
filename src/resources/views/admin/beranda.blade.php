<x-layouts.admin>
    <x-slot name="title">Beranda Admin — PetaFormasi</x-slot>

    @php $fmt = fn ($n) => number_format((int) $n, 0, ',', '.'); @endphp

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Beranda Admin</h1>
            <p class="mt-1.5 text-[15px] text-ink-2">
                @if ($musim)
                    Musim yang sedang tampil di situs: <span class="font-medium text-ink">{{ $musim->label }}</span>
                    <span class="text-ink-3">({{ ucfirst($musim->status) }})</span>
                @else
                    Belum ada musim yang bisa ditampilkan.
                @endif
            </p>
        </div>
        <p class="text-sm text-ink-3">Login sebagai {{ auth()->user()?->name }}</p>
    </div>

    {{-- Statistik musim tampil --}}
    <div class="mt-6 grid grid-cols-2 gap-4 xl:grid-cols-4">
        <div class="rounded-xl border border-line bg-surface p-5">
            <p class="text-sm text-ink-2">Formasi Terbit</p>
            <p class="tnum mt-1.5 text-3xl font-bold tracking-tight">{{ $fmt($statistik['formasi_terbit']) }}</p>
            <p class="mt-1 text-[13px] text-ink-3">{{ $musim?->label ?? '—' }}</p>
        </div>
        <div class="rounded-xl border border-line bg-surface p-5">
            <p class="text-sm text-ink-2">Draft Menunggu Terbit</p>
            <p class="tnum mt-1.5 text-3xl font-bold tracking-tight">{{ $fmt($statistik['formasi_draft']) }}</p>
            <p class="mt-1 text-[13px] {{ $statistik['formasi_draft'] > 0 ? 'font-medium text-warning' : 'text-ink-3' }}">
                {{ $statistik['formasi_draft'] > 0 ? 'Perlu ditinjau di Kelola Formasi' : 'Tidak ada draft tertunda' }}
            </p>
        </div>
        <div class="rounded-xl border border-line bg-surface p-5">
            <p class="text-sm text-ink-2">Total Kuota</p>
            <p class="tnum mt-1.5 text-3xl font-bold tracking-tight">{{ $fmt($statistik['total_kuota']) }}</p>
            <p class="mt-1 text-[13px] text-ink-3">Semua jenis kebutuhan, {{ $musim?->label ?? '—' }}</p>
        </div>
        <div class="rounded-xl border border-line bg-surface p-5">
            <p class="text-sm text-ink-2">Kode Akses Belum Dipakai</p>
            <p class="tnum mt-1.5 text-3xl font-bold tracking-tight">{{ $fmt($statistik['kode_belum_dipakai']) }}</p>
            <p class="mt-1 text-[13px] text-ink-3">{{ $fmt($statistik['kode_terpakai']) }} kode sudah terpakai</p>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-2 gap-4 xl:grid-cols-4">
        <a href="{{ route('admin.peserta') }}" class="rounded-xl border border-line bg-surface px-5 py-4 transition-colors hover:border-line-strong">
            <p class="text-sm text-ink-2">Peserta Terdaftar</p>
            <p class="tnum mt-1 text-xl font-bold tracking-tight">{{ $fmt($statistik['peserta']) }} <span class="text-sm font-medium text-accent">Lihat →</span></p>
        </a>
        <div class="rounded-xl border border-line bg-surface px-5 py-4">
            <p class="text-sm text-ink-2">Request Formasi Masuk</p>
            <p class="tnum mt-1 text-xl font-bold tracking-tight">{{ $fmt($statistik['request_masuk']) }}</p>
        </div>
    </div>

    <div class="mt-10 grid gap-10 xl:grid-cols-5">
        {{-- Daftar musim --}}
        <section class="xl:col-span-3">
            <h2 class="text-base font-semibold tracking-tight">Musim</h2>
            <div class="mt-3 overflow-hidden rounded-xl border border-line bg-surface">
                @forelse ($daftarMusim as $m)
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 border-b border-line px-5 py-4 last:border-b-0">
                        <p class="font-medium">{{ $m->label }}</p>
                        @if ($m->status === 'aktif')
                            <x-ui.badge variant="success">Aktif</x-ui.badge>
                        @elseif ($m->status === 'historis')
                            <x-ui.badge variant="neutral">Historis</x-ui.badge>
                        @else
                            <x-ui.badge variant="warning">{{ ucfirst($m->status) }}</x-ui.badge>
                        @endif
                        <p class="tnum text-sm text-ink-2">{{ $fmt($m->formasi_count) }} formasi · {{ $fmt($m->formasi_terbit_count) }} terbit</p>
                        @if ($m->pendaftaran_mulai && $m->pendaftaran_tutup)
                            <p class="w-full text-[13px] text-ink-3">
                                Pendaftaran {{ $m->pendaftaran_mulai->translatedFormat('d M Y') }} – {{ $m->pendaftaran_tutup->translatedFormat('d M Y') }}
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="px-5 py-4 text-sm text-ink-2">Belum ada musim terdaftar.</p>
                @endforelse
            </div>
            <p class="mt-2.5 text-[13px] leading-5 text-ink-3">
                Buat musim baru, perbaiki tanggal, atau ganti status di halaman
                <a href="{{ route('admin.musim') }}" class="font-medium text-accent hover:underline">Kelola Musim →</a>
            </p>
        </section>

        {{-- Jalan pintas modul --}}
        <section class="xl:col-span-2">
            <h2 class="text-base font-semibold tracking-tight">Jalan Pintas</h2>
            <div class="mt-3 flex flex-col gap-3">
                <a href="{{ route('admin.impor') }}" class="group rounded-xl border border-line bg-surface p-5 transition-colors hover:border-line-strong">
                    <p class="font-medium group-hover:text-accent">Impor Formasi →</p>
                    <p class="mt-1 text-sm leading-6 text-ink-2">Unggah CSV hasil ekstraksi pengumuman: analisis baris, checksum kuota, lalu terapkan sebagai draft.</p>
                </a>
                <a href="{{ route('admin.formasi') }}" class="group rounded-xl border border-line bg-surface p-5 transition-colors hover:border-line-strong">
                    <p class="font-medium group-hover:text-accent">Kelola Formasi →</p>
                    <p class="mt-1 text-sm leading-6 text-ink-2">Terbitkan/tarik formasi, tandai kartu contoh, dan catat snapshot pelamar manual.</p>
                </a>
                <a href="{{ route('admin.kode-akses') }}" class="group rounded-xl border border-line bg-surface p-5 transition-colors hover:border-line-strong">
                    <p class="font-medium group-hover:text-accent">Kode Akses →</p>
                    <p class="mt-1 text-sm leading-6 text-ink-2">Buat batch kode untuk Lynk.id, pantau pemakaian, dan cabut kode yang belum terpakai.</p>
                </a>
            </div>
        </section>
    </div>

    {{-- Request formasi terbaru --}}
    @if ($requestTerbaru->isNotEmpty())
        <section class="mt-10">
            <h2 class="text-base font-semibold tracking-tight">Request Formasi Terbaru</h2>
            <div class="mt-3 overflow-hidden rounded-xl border border-line bg-surface">
                @foreach ($requestTerbaru as $req)
                    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-0.5 border-b border-line px-5 py-3.5 last:border-b-0">
                        <p class="font-medium">{{ $req->jabatan_dicari ?: '—' }}</p>
                        <p class="text-sm text-ink-2">{{ $req->instansi_dicari ?: '—' }}</p>
                        <p class="ml-auto text-[13px] text-ink-3">
                            {{ $req->user?->name ?? 'Tamu' }} · {{ $req->created_at?->diffForHumans() }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.admin>
