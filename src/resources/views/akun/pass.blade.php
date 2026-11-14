<x-layouts.public>
    <x-slot:title>Pass Saya — Peta Formasi</x-slot:title>

    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Pass Saya</h1>
                <p class="mt-1.5 text-[15px] text-ink-2">Pass yang aktif di akunmu membuka detail formasi pada musimnya, di semua halaman publik seperti biasa.</p>
            </div>
            <a href="{{ route('akun.redeem') }}" class="inline-flex h-10 items-center rounded-lg bg-ink px-4 text-sm font-medium text-white transition-colors hover:bg-stone-800">Redeem Kode</a>
        </div>

        @if ($passes->isEmpty())
            <div class="mt-8 rounded-2xl border border-line bg-surface px-5 py-10 text-center">
                <p class="text-[15px] font-semibold">Belum ada pass di akun ini</p>
                <p class="mx-auto mt-1.5 max-w-md text-sm leading-6 text-ink-2">
                    Pencarian formasi tetap gratis. Bila kamu sudah membeli pass di Lynk.id, aktifkan kodenya lewat tombol Redeem Kode.
                    Belum punya? <a href="{{ route('harga') }}" class="font-medium text-accent hover:underline">Lihat harga pass</a>.
                </p>
            </div>
        @else
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                @foreach ($passes as $pass)
                    @php
                        $kode = $pass->kodeAkses;
                        $musim = $kode->musim;
                        $aktif = $pass->isAktif();
                    @endphp
                    <section class="rounded-2xl border border-line bg-surface p-5">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="font-semibold leading-snug">
                                {{ $kode->tipe_pass === 'strategi' ? 'Pass Strategi' : 'Pass Peta Historis' }}
                                <span class="block text-sm font-normal text-ink-2">{{ $musim->label }}</span>
                            </h2>
                            @if ($pass->status === 'dicabut')
                                <x-ui.badge variant="danger">Dicabut</x-ui.badge>
                            @elseif ($aktif)
                                <x-ui.badge variant="success">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral">Berakhir</x-ui.badge>
                            @endif
                        </div>

                        <dl class="mt-4 space-y-1.5 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-ink-2">Kode</dt>
                                <dd class="font-mono text-[13px]">{{ $kode->kode }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-ink-2">Aktif sejak</dt>
                                <dd class="tnum">{{ $pass->aktif_dari?->translatedFormat('d M Y') }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-ink-2">Berlaku sampai</dt>
                                <dd class="tnum">{{ $pass->aktif_hingga ? $pass->aktif_hingga->translatedFormat('d M Y') : 'Tanpa batas tanggal*' }}</dd>
                            </div>
                        </dl>

                        @if ($aktif)
                            <a href="{{ route('formasi.index', ['musim' => $musim->id]) }}" class="mt-4 inline-flex h-10 items-center rounded-lg border border-line-strong px-4 text-sm font-medium transition-colors hover:bg-sunken">
                                Buka Finder {{ $musim->label }}
                            </a>
                        @endif
                    </section>
                @endforeach
            </div>
            <p class="mt-4 text-[13px] text-ink-2">* Pass historis berlaku selama musimnya diterbitkan di situs ini (PRD §9).</p>
        @endif
    </div>
</x-layouts.public>
