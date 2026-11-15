<div>
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Kelola Musim</h1>
            <p class="mt-1.5 max-w-2xl text-[15px] text-ink-2">
                Buat musim seleksi baru, perbaiki tanggalnya, dan ganti status mengikuti siklus
                <span class="font-medium text-ink">Dibuat → Aktif → Historis → Arsip</span> (hanya maju, maksimal satu musim Aktif).
                Ingat: tanggal acuan usia menentukan hasil cek kelayakan, dan tanggal tutup pendaftaran
                menentukan masa berlaku Pass Strategi.
            </p>
        </div>
        @unless ($tampilForm)
            <button type="button" wire:click="bukaBuat"
                class="inline-flex h-10 items-center rounded-lg bg-ink px-4 text-sm font-medium text-white transition-colors hover:bg-stone-800">
                Buat Musim Baru
            </button>
        @endunless
    </div>

    {{-- Form buat/edit --}}
    @if ($tampilForm)
        <form wire:submit="simpan" class="mt-6 rounded-xl border border-line bg-surface p-5">
            <h2 class="text-base font-semibold tracking-tight">{{ $editId ? 'Edit Musim' : 'Musim Baru' }}</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="musim-tahun" class="mb-1.5 block text-sm font-medium">Tahun</label>
                    <input id="musim-tahun" type="number" min="2000" max="2100" wire:model="tahun" placeholder="2026"
                        class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    @error('tahun') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="musim-label" class="mb-1.5 block text-sm font-medium">Label tampil</label>
                    <input id="musim-label" type="text" wire:model="label" placeholder="CPNS 2026"
                        class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    @error('label') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="musim-acuan" class="mb-1.5 block text-sm font-medium">Tanggal acuan usia</label>
                    <input id="musim-acuan" type="date" wire:model="acuanUsia"
                        class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    <p class="mt-1.5 text-[13px] leading-5 text-ink-3">Usia peserta dihitung per tanggal ini pada cek kelayakan (R-01/R-02) — biasanya hari pertama pendaftaran.</p>
                    @error('acuanUsia') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="musim-pengumuman" class="mb-1.5 block text-sm font-medium">Tanggal pengumuman <span class="font-normal text-ink-3">(boleh kosong)</span></label>
                    <input id="musim-pengumuman" type="date" wire:model="tanggalPengumuman"
                        class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    @error('tanggalPengumuman') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="musim-mulai" class="mb-1.5 block text-sm font-medium">Pendaftaran mulai <span class="font-normal text-ink-3">(boleh kosong)</span></label>
                    <input id="musim-mulai" type="date" wire:model="pendaftaranMulai"
                        class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    @error('pendaftaranMulai') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="musim-tutup" class="mb-1.5 block text-sm font-medium">Pendaftaran tutup <span class="font-normal text-ink-3">(boleh kosong)</span></label>
                    <input id="musim-tutup" type="date" wire:model="pendaftaranTutup"
                        class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    <p class="mt-1.5 text-[13px] leading-5 text-ink-3">Pass Strategi berlaku sampai tanggal ini + 30 hari. Periksa dua kali sebelum menyimpan.</p>
                    @error('pendaftaranTutup') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-5 flex gap-2">
                <button type="submit" class="inline-flex h-10 items-center rounded-lg bg-ink px-4 text-sm font-medium text-white transition-colors hover:bg-stone-800">
                    {{ $editId ? 'Simpan Perubahan' : 'Buat Musim (status: Dibuat)' }}
                </button>
                <button type="button" wire:click="batal" class="inline-flex h-10 items-center rounded-lg border border-line-strong px-4 text-sm font-medium transition-colors hover:bg-sunken">
                    Batal
                </button>
            </div>
        </form>
    @endif

    {{-- Daftar musim --}}
    @php
        $langkah = [
            'dibuat' => ['aktif', 'Aktifkan', 'Aktifkan musim ini? Ia menjadi musim tampil utama di situs.'],
            'aktif' => ['historis', 'Jadikan Historis', 'Ubah musim ini menjadi Historis? Situs akan menampilkan musim aktif lain, atau musim historis terbaru.'],
            'historis' => ['arsip', 'Arsipkan', 'Arsipkan musim ini? Ia tidak lagi jadi musim tampil cadangan.'],
        ];
    @endphp
    <div class="mt-6 overflow-hidden rounded-xl border border-line bg-surface">
        @forelse ($daftarMusim as $m)
            <div class="flex flex-wrap items-start gap-x-4 gap-y-3 border-b border-line px-5 py-4 last:border-b-0" wire:key="musim-{{ $m->id }}">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <p class="font-medium">{{ $m->label }}</p>
                        @if ($m->status === 'aktif')
                            <x-ui.badge variant="success">Aktif</x-ui.badge>
                        @elseif ($m->status === 'historis')
                            <x-ui.badge variant="neutral">Historis</x-ui.badge>
                        @elseif ($m->status === 'dibuat')
                            <x-ui.badge variant="warning">Dibuat</x-ui.badge>
                        @else
                            <x-ui.badge variant="locked">{{ ucfirst($m->status) }}</x-ui.badge>
                        @endif
                        <p class="tnum text-sm text-ink-2">{{ number_format($m->formasi_count, 0, ',', '.') }} formasi</p>
                    </div>
                    <p class="mt-1.5 text-sm leading-6 text-ink-2">
                        Acuan usia: {{ $m->tanggal_acuan_usia?->translatedFormat('d F Y') ?? '—' }}
                        @if ($m->pendaftaran_mulai && $m->pendaftaran_tutup)
                            · Pendaftaran: {{ $m->pendaftaran_mulai->translatedFormat('d M Y') }} – {{ $m->pendaftaran_tutup->translatedFormat('d M Y') }}
                        @endif
                        @if ($m->tanggal_pengumuman)
                            · Pengumuman: {{ $m->tanggal_pengumuman->translatedFormat('d F Y') }}
                        @endif
                    </p>
                </div>
                <div class="ml-auto flex flex-wrap items-center gap-2">
                    <button type="button" wire:click="bukaEdit({{ $m->id }})"
                        class="inline-flex h-9 items-center rounded-lg border border-line-strong px-3.5 text-sm font-medium transition-colors hover:bg-sunken">
                        Edit
                    </button>
                    @if (isset($langkah[$m->status]))
                        @php [$ke, $teks, $konfirmasi] = $langkah[$m->status]; @endphp
                        <button type="button" wire:click="mintaKonfirmasi('transisi', [{{ $m->id }}, '{{ $ke }}'], 'Ubah status musim?', '{{ $konfirmasi }}', false, '{{ $teks }}')"
                            class="inline-flex h-9 items-center rounded-lg border border-line-strong px-3.5 text-sm font-medium transition-colors hover:bg-sunken">
                            {{ $teks }}
                        </button>
                    @endif
                    @if ($m->status === 'dibuat')
                        <button type="button" wire:click="mintaKonfirmasi('hapus', [{{ $m->id }}], 'Hapus musim?', 'Hapus musim {{ $m->label }}? Hanya bisa bila musim ini belum memiliki data apa pun.', true, 'Ya, hapus')"
                            class="inline-flex h-9 items-center rounded-lg border border-line-strong px-3.5 text-sm font-medium text-danger transition-colors hover:bg-danger-soft">
                            Hapus
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <p class="px-5 py-10 text-center text-ink-2">Belum ada musim terdaftar.</p>
        @endforelse
    </div>

    @include('livewire.admin._modal-konfirmasi')
</div>
