<div>
    <h1 class="text-2xl font-bold tracking-tight">Kelola Formasi</h1>
    <p class="mt-1.5 max-w-2xl text-[15px] text-ink-2">
        Terbitkan atau tarik formasi, tandai kartu contoh (maks 3 per jurusan), dan catat snapshot pelamar manual.
    </p>

    {{-- Filter --}}
    <div class="mt-6 flex flex-wrap items-center gap-2">
        <div class="relative min-w-56 flex-1 sm:max-w-xs">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.300ms="cariFormasi" placeholder="Cari nama formasi…"
                class="h-10 w-full rounded-lg border border-line-strong bg-surface pl-9 pr-9 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30" aria-label="Cari nama formasi">
            @if (trim($cariFormasi) !== '')
                <button type="button" wire:click="$set('cariFormasi', '')" aria-label="Bersihkan pencarian"
                    class="absolute right-2 top-1/2 flex h-6 w-6 -translate-y-1/2 items-center justify-center rounded-full text-ink-3 transition-colors hover:bg-sunken hover:text-ink">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            @endif
        </div>
        <select wire:model.live="musimId" class="h-10 rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30" aria-label="Musim">
            @foreach ($musimList as $m)
                <option value="{{ $m->id }}">{{ $m->label }} · {{ ucfirst($m->status) }}</option>
            @endforeach
        </select>
        <select wire:model.live="instansiId" class="h-10 rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30" aria-label="Instansi">
            <option value="">Semua instansi</option>
            @foreach ($instansiList as $i)
                <option value="{{ $i->id }}">{{ $i->nama }}</option>
            @endforeach
        </select>
        <select wire:model.live="saringStatus" class="h-10 rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30" aria-label="Status">
            <option value="">Semua status</option>
            <option value="draft">Draft</option>
            <option value="terbit">Terbit</option>
        </select>
        <button type="button" wire:click="mintaKonfirmasi('terbitkanSemuaDraft', [], 'Terbitkan semua draft?', 'Terbitkan semua formasi draft pada saringan ini? Formasi yang terbit langsung tampil ke publik.', false, 'Ya, terbitkan')"
            class="ml-auto inline-flex h-10 items-center rounded-lg bg-ink px-4 text-sm font-medium text-white transition-colors hover:bg-stone-800">
            Terbitkan Semua Draft
        </button>
    </div>

    @if (trim($cariFormasi) !== '')
        <p class="mt-3 text-sm text-ink-2">
            <span class="tnum font-semibold text-ink">{{ number_format($daftar->total(), 0, ',', '.') }}</span>
            formasi cocok dengan &ldquo;{{ trim($cariFormasi) }}&rdquo; pada saringan ini.
        </p>
    @endif

    {{-- Tabel formasi --}}
    <div class="mt-4 overflow-x-auto rounded-2xl border border-line bg-surface">
        <table class="w-full min-w-190 text-left text-sm">
            <thead>
                <tr class="border-b border-line text-ink-2">
                    <th class="px-4 py-3 font-medium">Jabatan — Penempatan</th>
                    <th class="px-4 py-3 font-medium">Instansi</th>
                    <th class="px-4 py-3 font-medium">Kuota</th>
                    <th class="px-4 py-3 font-medium">Jurusan</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftar as $f)
                    <tr class="border-b border-line align-top last:border-b-0">
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $f->jabatan }}</span><br>
                            <span class="text-ink-2">{{ $f->penempatan }}</span>
                            @if ($f->kartu_contoh) <x-ui.badge variant="accent" class="ml-1">Contoh</x-ui.badge> @endif
                        </td>
                        <td class="px-4 py-3">{{ $f->instansi->nama }}</td>
                        <td class="tnum px-4 py-3">{{ number_format($f->total_kuota ?? 0, 0, ',', '.') }}</td>
                        <td class="tnum px-4 py-3">{{ $f->jurusan_count }}</td>
                        <td class="px-4 py-3">
                            @if ($f->status_terbit === 'terbit')
                                <x-ui.badge variant="success">Terbit</x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral">Draft</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-x-3 gap-y-1 whitespace-nowrap">
                                @if ($f->status_terbit === 'draft')
                                    <button type="button" wire:click="terbitkan({{ $f->id }})" class="font-medium text-accent hover:underline">Terbitkan</button>
                                    <button type="button" wire:click="mintaKonfirmasi('hapus', [{{ $f->id }}], 'Hapus formasi draft?', 'Hapus formasi draft ini beserta kuota & kualifikasinya? Tindakan ini tidak bisa dibatalkan.', true, 'Ya, hapus')" class="font-medium text-danger hover:underline">Hapus</button>
                                @else
                                    <button type="button" wire:click="tarik({{ $f->id }})" class="font-medium text-accent hover:underline">Tarik</button>
                                @endif
                                <button type="button" wire:click="toggleContoh({{ $f->id }})" class="font-medium text-accent hover:underline">{{ $f->kartu_contoh ? 'Cabut Contoh' : 'Jadikan Contoh' }}</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-ink-2">Tidak ada formasi pada saringan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $daftar->links() }}</div>

    {{-- Snapshot manual --}}
    <section class="mt-8 rounded-2xl border border-line bg-surface p-5 sm:p-6">
        <h2 class="font-semibold">Catat Snapshot Pelamar (Manual)</h2>
        <p class="mt-1 text-sm leading-6 text-ink-2">Rekam angka pelamar per tanggal dari statistik resmi (SSCASN/instansi). Baris snapshot tidak pernah ditimpa — riwayatnya yang dipakai Radar.</p>

        <form wire:submit="simpanSnapshot" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <label for="snap-formasi" class="mb-1.5 block text-sm font-medium">Formasi</label>
                <select id="snap-formasi" wire:model.live="snapshotFormasiId" class="h-11 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    <option value="">— Pilih formasi —</option>
                    @foreach ($formasiUntukSnapshot as $f)
                        <option value="{{ $f->id }}">{{ $f->jabatan }} — {{ $f->penempatan }}</option>
                    @endforeach
                </select>
                @error('snapshotFormasiId') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="snap-jenis" class="mb-1.5 block text-sm font-medium">Jenis kebutuhan</label>
                <input id="snap-jenis" type="text" wire:model="snapshotJenis" placeholder="umum"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                @error('snapshotJenis') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="snap-tanggal" class="mb-1.5 block text-sm font-medium">Tanggal snapshot</label>
                <input id="snap-tanggal" type="date" wire:model="snapshotTanggal"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                @error('snapshotTanggal') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="snap-ms" class="mb-1.5 block text-sm font-medium">Pelamar MS</label>
                <input id="snap-ms" type="number" min="0" wire:model="snapshotMs"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                @error('snapshotMs') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="snap-total" class="mb-1.5 block text-sm font-medium">Total pelamar <span class="font-normal text-ink-3">(opsional)</span></label>
                <input id="snap-total" type="number" min="0" wire:model="snapshotTotal"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                @error('snapshotTotal') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="snap-tipe" class="mb-1.5 block text-sm font-medium">Tipe</label>
                <select id="snap-tipe" wire:model="snapshotTipe" class="h-11 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    <option value="historis">Historis</option>
                    <option value="live">Live</option>
                </select>
            </div>
            <div class="lg:col-span-2">
                <label for="snap-sumber" class="mb-1.5 block text-sm font-medium">Sumber</label>
                <input id="snap-sumber" type="text" wire:model="snapshotSumber" placeholder="Mis. Statistik SSCASN 2024"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
            </div>
            <div class="flex items-end">
                <button type="submit" class="inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800">Simpan Snapshot</button>
            </div>
        </form>

        @if ($snapshotTerbaru->isNotEmpty())
            <div class="mt-5 border-t border-line pt-4">
                <h3 class="text-sm font-semibold">Snapshot terakhir formasi terpilih</h3>
                <ul class="mt-2 space-y-1 text-sm text-ink-2">
                    @foreach ($snapshotTerbaru as $snap)
                        <li>
                            {{ $snap->tanggal_snapshot?->translatedFormat('d M Y') }} · {{ ucfirst($snap->formasiKuota->jenis_kebutuhan) }} ·
                            MS <span class="tnum font-medium text-ink">{{ number_format($snap->jumlah_ms, 0, ',', '.') }}</span>
                            @if ($snap->jumlah_total !== null) · Total <span class="tnum">{{ number_format($snap->jumlah_total, 0, ',', '.') }}</span> @endif
                            · {{ ucfirst($snap->tipe) }}@if ($snap->sumber) · {{ $snap->sumber }} @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>

    @include('livewire.admin._modal-konfirmasi')
</div>
