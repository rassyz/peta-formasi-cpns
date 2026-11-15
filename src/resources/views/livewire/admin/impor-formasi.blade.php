<div>
    <h1 class="text-2xl font-bold tracking-tight">Impor Formasi</h1>
    <p class="mt-1.5 max-w-2xl text-[15px] leading-7 text-ink-2">
        Unggah CSV hasil ekstraksi pengumuman (satu instansi, satu musim). Sistem memeriksa tiap baris,
        mencocokkan jurusan ke master, mendeteksi duplikat, dan memverifikasi total kuota terhadap angka
        resmi pengumuman sebelum apa pun diterapkan — hasil impor selalu masuk sebagai <span class="font-medium text-ink">draft</span>.
    </p>

    <form wire:submit="analisis" class="mt-6 rounded-2xl border border-line bg-surface p-5 sm:p-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="imp-musim" class="mb-1.5 block text-sm font-medium">Musim</label>
                <select id="imp-musim" wire:model="musimId" class="h-11 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    @foreach ($musimList as $m)
                        <option value="{{ $m->id }}">{{ $m->label }} · {{ ucfirst($m->status) }}</option>
                    @endforeach
                </select>
                @error('musimId') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="imp-instansi" class="mb-1.5 block text-sm font-medium">Instansi</label>
                <select id="imp-instansi" wire:model="instansiId" class="h-11 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    @foreach ($instansiList as $i)
                        <option value="{{ $i->id }}">{{ $i->nama }}</option>
                    @endforeach
                </select>
                @error('instansiId') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="imp-target" class="mb-1.5 block text-sm font-medium">Total kuota resmi <span class="font-normal text-ink-3">(checksum)</span></label>
                <input id="imp-target" type="number" min="0" wire:model="targetKuota" placeholder="Mis. 154"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                @error('targetKuota') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="imp-berkas" class="mb-1.5 block text-sm font-medium">Berkas CSV</label>
                <input id="imp-berkas" type="file" wire:model="berkas" accept=".csv,text/csv"
                    class="block w-full text-sm file:mr-3 file:h-11 file:rounded-lg file:border file:border-line-strong file:bg-surface file:px-4 file:text-sm file:font-medium hover:file:bg-sunken">
                @error('berkas') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button type="submit" wire:loading.attr="disabled"
                class="inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800 disabled:opacity-60">
                <span wire:loading.remove wire:target="analisis">Analisis Berkas</span>
                <span wire:loading wire:target="analisis">Menganalisis…</span>
            </button>
            <a href="{{ route('admin.impor.template') }}" class="text-sm font-medium text-accent hover:underline">Unduh template CSV</a>
            <span wire:loading wire:target="berkas" class="text-sm text-ink-2">Mengunggah berkas…</span>
        </div>
    </form>

    @if ($laporan)
        {{-- Panel checksum --}}
        @if ($laporan['checksum_ok'] === true)
            <div class="mt-6 rounded-2xl bg-success-soft px-5 py-4 text-success">
                <p class="font-semibold">Checksum COCOK — total kuota berkas <span class="tnum">{{ number_format($laporan['total_kuota_file'], 0, ',', '.') }}</span> = target resmi <span class="tnum">{{ number_format($laporan['target_kuota'], 0, ',', '.') }}</span>.</p>
            </div>
        @elseif ($laporan['checksum_ok'] === false)
            <div class="mt-6 rounded-2xl bg-danger-soft px-5 py-4 text-danger">
                <p class="font-semibold">Checksum TIDAK COCOK — total kuota berkas <span class="tnum">{{ number_format($laporan['total_kuota_file'], 0, ',', '.') }}</span>, target resmi <span class="tnum">{{ number_format($laporan['target_kuota'], 0, ',', '.') }}</span> (selisih <span class="tnum">{{ number_format(abs($laporan['total_kuota_file'] - $laporan['target_kuota']), 0, ',', '.') }}</span>). Penerapan diblokir sampai cocok.</p>
            </div>
        @else
            <div class="mt-6 rounded-2xl border border-line bg-surface px-5 py-4">
                <p class="font-semibold">Total kuota berkas: <span class="tnum">{{ number_format($laporan['total_kuota_file'], 0, ',', '.') }}</span> <span class="font-normal text-ink-2">(target checksum tidak diisi — gerbang checksum nonaktif)</span></p>
            </div>
        @endif

        {{-- Jurusan hilang --}}
        @if ($laporan['jurusan_hilang'] !== [])
            <div class="mt-4 rounded-2xl bg-warning-soft px-5 py-4">
                <p class="font-semibold text-warning">{{ count($laporan['jurusan_hilang']) }} jurusan belum ada di master:</p>
                <p class="mt-1 text-sm text-warning">{{ implode(' · ', $laporan['jurusan_hilang']) }}</p>
                <button type="button" wire:click="tambahJurusanHilang"
                    class="mt-3 inline-flex h-10 items-center rounded-lg bg-ink px-4 text-sm font-medium text-white transition-colors hover:bg-stone-800">
                    Tambahkan Semua ke Master &amp; Analisis Ulang
                </button>
            </div>
        @endif

        {{-- Tabel baris --}}
        <div class="mt-4 overflow-x-auto rounded-2xl border border-line bg-surface">
            <table class="w-full min-w-170 text-left text-sm">
                <thead>
                    <tr class="border-b border-line text-ink-2">
                        <th class="px-4 py-3 font-medium">Baris</th>
                        <th class="px-4 py-3 font-medium">Jabatan — Penempatan</th>
                        <th class="px-4 py-3 font-medium">Kuota</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporan['baris'] as $baris)
                        <tr class="border-b border-line align-top last:border-b-0">
                            <td class="tnum px-4 py-3 text-ink-2">{{ $baris['nomor'] }}</td>
                            <td class="px-4 py-3">{{ $baris['jabatan'] }} — {{ $baris['penempatan'] }}</td>
                            <td class="tnum px-4 py-3">{{ number_format($baris['total_kuota'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @if ($baris['galat'] === [])
                                    <x-ui.badge variant="success">Siap</x-ui.badge>
                                @else
                                    <x-ui.badge variant="danger">Bermasalah</x-ui.badge>
                                    <ul class="mt-1.5 list-disc space-y-0.5 pl-4 text-[13px] text-danger">
                                        @foreach ($baris['galat'] as $g)
                                            <li>{{ $g }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button type="button" wire:click="terapkan" @disabled(! $laporan['valid'])
                class="inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800 disabled:cursor-not-allowed disabled:opacity-50">
                Terapkan {{ $laporan['jumlah_siap'] }} Formasi sebagai Draft
            </button>
            <p class="text-sm text-ink-2">{{ $laporan['jumlah_siap'] }} dari {{ $laporan['jumlah_baris'] }} baris siap. Penerapan bersifat menyeluruh: semua baris harus siap dan checksum harus cocok — perbaiki berkasnya lalu analisis ulang bila masih ada yang bermasalah.</p>
        </div>
    @endif
</div>
