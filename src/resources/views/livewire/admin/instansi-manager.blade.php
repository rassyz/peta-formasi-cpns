<div>
    <div>
        <h1 class="text-2xl font-semibold tracking-tight">Kelola Instansi</h1>
        <p class="mt-1 max-w-2xl text-ink-2">
            Master instansi sumber formasi, beserta checklist berkas default yang diwarisi semua
            formasinya. Instansi yang sudah memiliki formasi tidak bisa dihapus.
        </p>
    </div>

    {{-- Formulir tambah / ubah instansi --}}
    <form wire:submit="simpan" class="mt-6 rounded-2xl border border-line bg-surface p-5 shadow-card">
        <h2 class="text-base font-semibold">{{ $editId ? 'Ubah Instansi' : 'Tambah Instansi' }}</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="instansi-nama" class="mb-1.5 block text-sm font-medium">Nama instansi</label>
                <input id="instansi-nama" type="text" wire:model="nama" placeholder="mis. Kementerian Keuangan"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30">
                @error('nama') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="instansi-slug" class="mb-1.5 block text-sm font-medium">Slug</label>
                <input id="instansi-slug" type="text" wire:model="slug" placeholder="otomatis dari nama"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 font-mono text-[15px] outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30">
                <p class="mt-1.5 text-[13px] text-ink-3">Kosongkan untuk dibuat otomatis dari nama. Dipakai di alamat dan penanda data.</p>
                @error('slug') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="instansi-wilayah" class="mb-1.5 block text-sm font-medium">Wilayah</label>
                <select id="instansi-wilayah" wire:model="wilayah"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30">
                    <option value="pusat">Pusat</option>
                    <option value="daerah">Daerah</option>
                </select>
                <p class="mt-1.5 text-[13px] text-ink-3">Menjadi pil "Pusat"/"Daerah" pada kartu formasi instansi ini.</p>
                @error('wilayah') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="instansi-situs" class="mb-1.5 block text-sm font-medium">Situs resmi <span class="font-normal text-ink-3">(opsional)</span></label>
                <input id="instansi-situs" type="url" wire:model="situsResmi" placeholder="https://…"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30">
                @error('situsResmi') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="instansi-link" class="mb-1.5 block text-sm font-medium">Link pengumuman <span class="font-normal text-ink-3">(opsional)</span></label>
                <input id="instansi-link" type="url" wire:model="linkPengumuman" placeholder="https://…"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30">
                @error('linkPengumuman') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="mt-5 flex flex-wrap gap-2">
            <button type="submit" class="inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800">
                {{ $editId ? 'Simpan Perubahan' : 'Tambah Instansi' }}
            </button>
            @if ($editId)
                <button type="button" wire:click="batalEdit" class="inline-flex h-11 items-center rounded-lg border border-line-strong px-5 text-sm font-medium transition-colors hover:bg-sunken">Batal</button>
            @endif
        </div>
    </form>

    {{-- Daftar instansi --}}
    <div class="mt-6 grid gap-4">
        @foreach ($daftarInstansi as $i)
            <section class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base font-semibold">{{ $i->nama }}</h2>
                            @if ($i->wilayah === 'pusat')
                                <x-ui.badge variant="accent">Pusat</x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral">Daerah</x-ui.badge>
                            @endif
                        </div>
                        <p class="mt-1 text-[13px] text-ink-3">
                            <span class="font-mono">{{ $i->slug }}</span>
                            · {{ $i->formasi_count }} formasi
                            · {{ $i->checklistDefault->count() }} dokumen checklist default
                            @if ($i->situs_resmi)
                                · <a href="{{ $i->situs_resmi }}" target="_blank" rel="noopener" class="text-accent hover:underline">Situs resmi</a>
                            @endif
                        </p>
                        @if ($i->formasi_count > 0)
                            <p class="mt-1 text-[13px] text-ink-3">Tidak bisa dihapus — sudah memiliki formasi.</p>
                        @endif
                    </div>
                    <div class="flex shrink-0 flex-wrap gap-2">
                        <button type="button" wire:click="toggleChecklist({{ $i->id }})"
                            class="inline-flex h-9 items-center rounded-lg border border-line-strong px-3.5 text-sm font-medium transition-colors hover:bg-sunken">
                            {{ $checklistUntukId === $i->id ? 'Tutup Checklist' : 'Checklist' }}
                        </button>
                        <button type="button" wire:click="edit({{ $i->id }})"
                            class="inline-flex h-9 items-center rounded-lg border border-line-strong px-3.5 text-sm font-medium transition-colors hover:bg-sunken">Ubah</button>
                        @if ($i->formasi_count === 0)
                            <button type="button"
                                wire:click="mintaKonfirmasi('hapus', [{{ $i->id }}], 'Hapus instansi?', 'Instansi {{ $i->nama }} beserta checklist defaultnya akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.', true, 'Ya, hapus')"
                                class="inline-flex h-9 items-center rounded-lg border border-danger/40 px-3.5 text-sm font-medium text-danger transition-colors hover:border-danger/60 hover:bg-danger-soft">Hapus</button>
                        @endif
                    </div>
                </div>

                @if ($checklistUntukId === $i->id)
                    <div class="mt-4 border-t border-line pt-4">
                        <h3 class="text-sm font-semibold">Checklist Berkas Default</h3>
                        <p class="mt-0.5 text-[13px] text-ink-3">Diwarisi semua formasi instansi ini, kecuali formasi yang memiliki baris pengganti sendiri.</p>

                        @if ($i->checklistDefault->isEmpty())
                            <p class="mt-3 text-sm text-ink-2">Belum ada dokumen — detail formasi instansi ini akan menampilkan checklist kosong. Tambahkan baris pertama di bawah.</p>
                        @else
                            <ul class="mt-2 divide-y divide-line">
                                @foreach ($i->checklistDefault as $c)
                                    <li class="flex items-center gap-3 py-2.5 text-sm">
                                        <span class="w-6 shrink-0 text-ink-3">{{ $c->urutan }}.</span>
                                        <span class="min-w-0 flex-1">
                                            <span class="font-medium">{{ $c->nama_dokumen }}</span>
                                            <span class="text-ink-3">
                                                @if ($c->format)
                                                    · {{ $c->format }}
                                                @endif
                                                @if ($c->ukuran_maks_kb)
                                                    · maks {{ number_format($c->ukuran_maks_kb, 0, ',', '.') }} KB
                                                @endif
                                            </span>
                                        </span>
                                        @if ($c->wajib)
                                            <x-ui.badge variant="accent">Wajib</x-ui.badge>
                                        @endif
                                        <button type="button"
                                            wire:click="mintaKonfirmasi('hapusChecklist', [{{ $c->id }}], 'Hapus dokumen?', 'Dokumen {{ $c->nama_dokumen }} akan dihapus dari checklist default {{ $i->nama }}.', true, 'Ya, hapus')"
                                            class="shrink-0 text-[13px] font-medium text-danger hover:underline">Hapus</button>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <form wire:submit="tambahChecklist({{ $i->id }})" class="mt-4">
                            <div class="grid gap-3 sm:grid-cols-[1fr_110px_130px_auto]">
                                <input type="text" wire:model="dokumenBaru" placeholder="Nama dokumen, mis. KTP"
                                    class="h-10 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-sm outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30">
                                <input type="text" wire:model="formatBaru" placeholder="Format (PDF)"
                                    class="h-10 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-sm outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30">
                                <input type="number" wire:model="ukuranBaru" placeholder="Maks KB" min="1"
                                    class="h-10 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-sm outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/30">
                                <button type="submit" class="inline-flex h-10 items-center rounded-lg bg-ink px-4 text-sm font-medium text-white transition-colors hover:bg-stone-800">Tambah</button>
                            </div>
                            @error('dokumenBaru') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                            @error('formatBaru') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                            @error('ukuranBaru') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                        </form>
                    </div>
                @endif
            </section>
        @endforeach
    </div>

    @include('livewire.admin._modal-konfirmasi')
</div>
