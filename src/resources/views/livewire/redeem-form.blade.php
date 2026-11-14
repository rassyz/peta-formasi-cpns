<div class="mx-auto max-w-xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Redeem Kode Akses</h1>
    <p class="mt-2 leading-7 text-ink-2">
        Masukkan kode unik yang kamu terima setelah membeli pass di Lynk.id.
        Pass langsung aktif di akun ini — satu kode hanya berlaku untuk satu akun.
    </p>

    @if ($hasil)
        <div class="mt-6 rounded-2xl border border-line bg-surface p-5 sm:p-6">
            <x-ui.badge variant="success">Pass Aktif</x-ui.badge>
            <p class="mt-3 text-[15px] leading-7">
                <span class="font-semibold">{{ $hasil['tipe'] }} · {{ $hasil['musim'] }}</span> sekarang terbuka untuk akunmu.
                @if ($hasil['hingga'])
                    Berlaku sampai {{ $hasil['hingga'] }}.
                @else
                    Berlaku tanpa batas tanggal selama musim ini diterbitkan.
                @endif
            </p>
            <div class="mt-5 flex flex-wrap gap-2.5">
                <a href="{{ route('akun.pass') }}" class="inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800">Lihat Pass Saya</a>
                <a href="{{ route('formasi.index') }}" class="inline-flex h-11 items-center rounded-lg border border-line-strong px-5 text-sm font-medium transition-colors hover:bg-sunken">Cari Formasi</a>
            </div>
        </div>
    @else
        <form wire:submit="redeem" class="mt-6 rounded-2xl border border-line bg-surface p-5 sm:p-6">
            <label for="kode-akses" class="mb-1.5 block text-sm font-medium">Kode akses</label>
            <input id="kode-akses" type="text" wire:model="kode" placeholder="Mis. HIST-2024-XXXX-XXXX"
                autocomplete="off" autocapitalize="characters" spellcheck="false"
                class="h-12 w-full rounded-lg border border-line-strong bg-surface px-3.5 font-mono text-[15px] uppercase tracking-wide focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
            @error('kode') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror

            @if ($galat)
                <p class="mt-4 rounded-lg bg-danger-soft px-3.5 py-2.5 text-sm leading-6 text-danger">{{ $galat }}</p>
            @endif

            <button type="submit" wire:loading.attr="disabled"
                class="mt-5 inline-flex h-11 w-full items-center justify-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800 disabled:opacity-60 sm:w-auto">
                <span wire:loading.remove wire:target="redeem">Aktifkan Pass</span>
                <span wire:loading wire:target="redeem">Memeriksa kode…</span>
            </button>
        </form>

        <p class="mt-4 text-sm leading-6 text-ink-2">
            Belum punya kode? Pass dijual sekali bayar per musim mulai Rp10.000 —
            <a href="{{ route('harga') }}" class="font-medium text-accent hover:underline">lihat harga &amp; isi tiap pass</a>.
        </p>
    @endif
</div>
