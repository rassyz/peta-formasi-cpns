<div>
    <h1 class="text-2xl font-bold tracking-tight">Impor Snapshot Pelamar</h1>
    <p class="mt-1.5 max-w-2xl text-[15px] leading-7 text-ink-2">
        Unggah CSV angka pelamar per formasi dan jenis kebutuhan (satu instansi, satu musim). Sistem
        mencocokkan tiap baris ke formasi dan kuotanya, memeriksa angka dan tanggalnya, dan menolak
        snapshot yang tanggal dan tipenya sudah tercatat — snapshot selalu menjadi
        <span class="font-medium text-ink">baris baru bertanggal</span> dan tidak pernah menimpa snapshot lama.
    </p>

    <form wire:submit="analisis" class="mt-6 rounded-2xl border border-line bg-surface p-5 sm:p-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label for="imps-musim" class="mb-1.5 block text-sm font-medium">Musim</label>
                <select id="imps-musim" wire:model="musimId" class="h-11 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    @foreach ($musimList as $m)
                        <option value="{{ $m->id }}">{{ $m->label }} · {{ ucfirst($m->status) }}</option>
                    @endforeach
                </select>
                @error('musimId') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="imps-instansi" class="mb-1.5 block text-sm font-medium">Instansi</label>
                <select id="imps-instansi" wire:model="instansiId" class="h-11 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    @foreach ($instansiList as $i)
                        <option value="{{ $i->id }}">{{ $i->nama }}</option>
                    @endforeach
                </select>
                @error('instansiId') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="imps-berkas" class="mb-1.5 block text-sm font-medium">Berkas CSV</label>
                <input id="imps-berkas" type="file" wire:model="berkas" accept=".csv,text/csv"
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
            <a href="{{ route('admin.impor-snapshot.template') }}" class="text-sm font-medium text-accent hover:underline">Unduh template CSV</a>
            <span wire:loading wire:target="berkas" class="text-sm text-ink-2">Mengunggah berkas…</span>
        </div>
    </form>

    {{-- Bilah proses: hanya tampil selama salah satu aksi impor berjalan --}}
    <div wire:loading wire:target="analisis, terapkan"
        class="mt-6 rounded-2xl border border-accent/40 bg-accent-soft px-5 py-4">
        <p class="font-semibold text-accent">Proses sedang berjalan…</p>
        <p class="mt-0.5 text-sm text-ink-2">Berkas berisi ribuan baris bisa memakan waktu 1–3 menit. Jangan tutup atau muat ulang halaman ini.</p>
    </div>

    @if ($laporan)
        {{-- Panel ringkasan total (pengganti gerbang checksum: verifikasi visual ke sumber) --}}
        <div class="mt-6 rounded-2xl border border-line bg-surface px-5 py-4">
            <p class="font-semibold">
                Total MS berkas: <span class="tnum">{{ number_format($laporan['total_ms'], 0, ',', '.') }}</span>
                · Total pendaftar berkas: <span class="tnum">{{ number_format($laporan['total_total'], 0, ',', '.') }}</span>
                <span class="font-normal text-ink-2">(dari {{ number_format($laporan['baris_dengan_total'], 0, ',', '.') }} baris yang mengisi jumlah total — cocokkan kedua angka ini dengan dokumen sumber sebelum menerapkan)</span>
            </p>
        </div>

        {{-- Tabel baris: hanya baris bermasalah yang dirender (maks 100) supaya
             berkas ribuan baris tidak membebani peramban; baris siap cukup diringkas. --}}
        @if ($laporan['jumlah_bermasalah'] === 0)
            <div class="mt-4 rounded-2xl border border-line bg-surface px-5 py-4">
                <p class="font-semibold text-success">Semua {{ number_format($laporan['jumlah_baris'], 0, ',', '.') }} baris siap — tidak ada baris bermasalah.</p>
            </div>
        @else
            @php
                $bermasalahTampil = array_slice(array_filter($laporan['baris'], fn ($b) => $b['galat'] !== []), 0, 100);
            @endphp
            <p class="mt-4 text-sm text-ink-2">
                Menampilkan {{ number_format(count($bermasalahTampil), 0, ',', '.') }} dari {{ number_format($laporan['jumlah_bermasalah'], 0, ',', '.') }} baris bermasalah
                · {{ number_format($laporan['jumlah_siap'], 0, ',', '.') }} baris siap tidak ditampilkan.
            </p>
            <div class="mt-2 overflow-x-auto rounded-2xl border border-line bg-surface">
                <table class="w-full min-w-170 text-left text-sm">
                    <thead>
                        <tr class="border-b border-line text-ink-2">
                            <th class="px-4 py-3 font-medium">Baris</th>
                            <th class="px-4 py-3 font-medium">Jabatan — Penempatan</th>
                            <th class="px-4 py-3 font-medium">Jenis</th>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bermasalahTampil as $baris)
                            <tr class="border-b border-line align-top last:border-b-0">
                                <td class="tnum px-4 py-3 text-ink-2">{{ $baris['nomor'] }}</td>
                                <td class="px-4 py-3">{{ $baris['jabatan'] }} — {{ $baris['penempatan'] }}</td>
                                <td class="px-4 py-3">{{ $baris['jenis'] }}</td>
                                <td class="tnum px-4 py-3">{{ $baris['tanggal'] }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge variant="danger">Bermasalah</x-ui.badge>
                                    <ul class="mt-1.5 list-disc space-y-0.5 pl-4 text-[13px] text-danger">
                                        @foreach ($baris['galat'] as $g)
                                            <li>{{ $g }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button type="button" wire:click="terapkan" @disabled(! $laporan['valid']) wire:loading.attr="disabled"
                class="inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800 disabled:cursor-not-allowed disabled:opacity-50">
                <span wire:loading.remove wire:target="terapkan">Terapkan {{ $laporan['jumlah_siap'] }} Snapshot sebagai Baris Baru</span>
                <span wire:loading wire:target="terapkan">Menerapkan…</span>
            </button>
            <p class="text-sm text-ink-2">{{ $laporan['jumlah_siap'] }} dari {{ $laporan['jumlah_baris'] }} baris siap. Penerapan bersifat menyeluruh: semua baris harus siap — perbaiki berkasnya lalu analisis ulang bila masih ada yang bermasalah.</p>
        </div>
    @endif
</div>
