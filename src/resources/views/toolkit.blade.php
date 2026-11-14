<x-layouts.public>
    <x-slot:title>Toolkit Berkas — Peta Formasi</x-slot:title>

    <div id="toolkit-root" class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-titik p-6 sm:p-8">
            <div class="max-w-2xl">
                <h1 class="text-3xl font-bold tracking-tight">Toolkit Berkas</h1>
                <p class="mt-3 leading-7 text-ink-2">
                    Kompres PDF dan foto ke ukuran unggah yang disyaratkan instansi, langsung di browser kamu.
                    <span class="font-medium text-ink">File tidak pernah diunggah ke server</span> — semua proses
                    berjalan di perangkatmu dan file asli tidak berubah.
                </p>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            {{-- ================= Kompres PDF ================= --}}
            <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6" aria-labelledby="judul-pdf">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-accent-soft text-accent">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h6"/></svg>
                    </span>
                    <h2 id="judul-pdf" class="text-lg font-semibold tracking-tight">Kompres PDF</h2>
                </div>
                <p class="mt-2.5 text-sm leading-6 text-ink-2">
                    Cocok untuk dokumen hasil pindai (scan). PDF hasil berupa gambar per halaman,
                    jadi teksnya tidak bisa diseleksi — sama seperti berkas pindai pada umumnya.
                </p>

                <p class="mt-6 flex items-center gap-2 text-sm font-semibold">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-ink text-[11px] font-bold text-white">1</span>
                    Pilih file
                </p>
                <label for="pdf-file" class="mt-3 flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-line-strong bg-sunken/60 px-4 py-8 text-center transition-colors hover:bg-sunken">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full border border-line bg-surface">
                        <svg class="h-6 w-6 text-ink-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V4m0 0 4 4m-4-4L8 8"/><path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/></svg>
                    </span>
                    <span class="mt-3 text-sm font-medium">Pilih file PDF atau jatuhkan di sini</span>
                    <span class="mt-1 text-[13px] text-ink-2">Satu file PDF hasil pindai atau ekspor dokumen</span>
                    <span id="pdf-file-info" class="mt-3 inline-flex max-w-full items-center truncate rounded-full border border-line bg-surface px-2.5 py-1 text-xs text-ink-2">Belum ada file dipilih</span>
                </label>
                <input id="pdf-file" type="file" accept="application/pdf,.pdf" class="sr-only">

                <p class="mt-6 flex items-center gap-2 text-sm font-semibold">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-ink text-[11px] font-bold text-white">2</span>
                    Target ukuran
                </p>
                <div class="mt-3 flex flex-wrap items-center gap-2" role="group" aria-label="Target ukuran PDF">
                    <button type="button" data-preset-pdf="200" class="preset-pdf rounded-full border border-line-strong px-3.5 py-1.5 text-sm font-medium transition-colors">200 KB</button>
                    <button type="button" data-preset-pdf="500" class="preset-pdf rounded-full border border-line-strong px-3.5 py-1.5 text-sm font-medium transition-colors">500 KB</button>
                    <button type="button" data-preset-pdf="1024" class="preset-pdf rounded-full border border-line-strong px-3.5 py-1.5 text-sm font-medium transition-colors">1 MB</button>
                    <span class="flex items-center gap-1.5 text-sm text-ink-2">
                        <input id="pdf-custom" type="number" min="50" max="10240" placeholder="Lainnya" class="h-9 w-24 rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                        KB
                    </span>
                </div>

                <p class="mt-6 flex items-center gap-2 text-sm font-semibold">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-ink text-[11px] font-bold text-white">3</span>
                    Kompres
                    <span class="text-[13px] font-normal text-ink-2">— tombol aktif setelah file dipilih</span>
                </p>
                <button id="pdf-process" type="button" disabled class="mt-3 inline-flex h-11 w-full items-center justify-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800 disabled:cursor-not-allowed disabled:bg-sunken disabled:text-ink-2">
                    Kompres PDF
                </button>

                <div id="pdf-progress-wrap" class="mt-4 hidden">
                    <div class="h-2 overflow-hidden rounded-full bg-sunken">
                        <div id="pdf-progress" class="h-full w-0 rounded-full bg-accent transition-all"></div>
                    </div>
                    <p id="pdf-progress-text" class="mt-1.5 text-[13px] text-ink-2">Menyiapkan…</p>
                </div>

                <p id="pdf-error" class="mt-4 hidden rounded-lg bg-danger-soft px-3.5 py-2.5 text-sm text-danger"></p>

                <div id="pdf-result" class="mt-4 hidden rounded-xl border border-line bg-sunken/60 p-4">
                    <p id="pdf-result-text" class="text-sm leading-6"></p>
                    <a id="pdf-download" href="#" download class="mt-3 inline-flex h-10 items-center rounded-lg bg-ink px-4 text-sm font-medium text-white transition-colors hover:bg-stone-800">
                        Unduh PDF Hasil
                    </a>
                </div>
            </section>

            {{-- ================= Kompres Foto ================= --}}
            <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6" aria-labelledby="judul-foto">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-accent-soft text-accent">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-4.35-4.35a2 2 0 0 0-2.83 0L6 18"/></svg>
                    </span>
                    <h2 id="judul-foto" class="text-lg font-semibold tracking-tight">Kompres &amp; Ubah Ukuran Foto</h2>
                </div>
                <p class="mt-2.5 text-sm leading-6 text-ink-2">
                    Untuk pas foto dan foto berkas (JPG/PNG). Hasil selalu JPG agar ukurannya kecil.
                </p>

                <p class="mt-6 flex items-center gap-2 text-sm font-semibold">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-ink text-[11px] font-bold text-white">1</span>
                    Pilih file
                </p>
                <label for="foto-file" class="mt-3 flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-line-strong bg-sunken/60 px-4 py-8 text-center transition-colors hover:bg-sunken">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full border border-line bg-surface">
                        <svg class="h-6 w-6 text-ink-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-4.35-4.35a2 2 0 0 0-2.83 0L6 18"/></svg>
                    </span>
                    <span class="mt-3 text-sm font-medium">Pilih foto atau jatuhkan di sini</span>
                    <span class="mt-1 text-[13px] text-ink-2">JPG, PNG, atau WebP · hasil selalu JPG</span>
                    <span id="foto-file-info" class="mt-3 inline-flex max-w-full items-center truncate rounded-full border border-line bg-surface px-2.5 py-1 text-xs text-ink-2">Belum ada file dipilih</span>
                </label>
                <input id="foto-file" type="file" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" class="sr-only">

                <p class="mt-6 flex items-center gap-2 text-sm font-semibold">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-ink text-[11px] font-bold text-white">2</span>
                    Target ukuran &amp; dimensi
                </p>
                <div class="mt-3 flex flex-wrap items-center gap-2" role="group" aria-label="Target ukuran foto">
                    <button type="button" data-preset-foto="200" class="preset-foto rounded-full border border-line-strong px-3.5 py-1.5 text-sm font-medium transition-colors">200 KB</button>
                    <button type="button" data-preset-foto="500" class="preset-foto rounded-full border border-line-strong px-3.5 py-1.5 text-sm font-medium transition-colors">500 KB</button>
                    <button type="button" data-preset-foto="1024" class="preset-foto rounded-full border border-line-strong px-3.5 py-1.5 text-sm font-medium transition-colors">1 MB</button>
                </div>
                <div class="mt-4">
                    <label for="foto-dimensi" class="text-sm font-medium text-ink-2">Dimensi hasil</label>
                    <select id="foto-dimensi" class="mt-2 h-10 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 sm:max-w-sm">
                        <option value="asli">Ikuti ukuran asli</option>
                        <option value="1920">Maks 1920 px (sisi terpanjang)</option>
                        <option value="1280">Maks 1280 px (sisi terpanjang)</option>
                        <option value="1024">Maks 1024 px (sisi terpanjang)</option>
                        <option value="pasfoto">Pas foto 4×6 — 472×709 px</option>
                    </select>
                </div>

                <p class="mt-6 flex items-center gap-2 text-sm font-semibold">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-ink text-[11px] font-bold text-white">3</span>
                    Proses
                    <span class="text-[13px] font-normal text-ink-2">— tombol aktif setelah file dipilih</span>
                </p>
                <button id="foto-process" type="button" disabled class="mt-3 inline-flex h-11 w-full items-center justify-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800 disabled:cursor-not-allowed disabled:bg-sunken disabled:text-ink-2">
                    Proses Foto
                </button>

                <p id="foto-error" class="mt-4 hidden rounded-lg bg-danger-soft px-3.5 py-2.5 text-sm text-danger"></p>

                <div id="foto-result" class="mt-4 hidden rounded-xl border border-line bg-sunken/60 p-4">
                    <div class="flex items-start gap-4">
                        <img id="foto-preview" alt="Pratinjau foto hasil" class="h-24 w-24 shrink-0 rounded-lg border border-line object-cover">
                        <div>
                            <p id="foto-result-text" class="text-sm leading-6"></p>
                            <a id="foto-download" href="#" download class="mt-3 inline-flex h-10 items-center rounded-lg bg-ink px-4 text-sm font-medium text-white transition-colors hover:bg-stone-800">
                                Unduh Foto Hasil
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="mt-6 rounded-2xl border border-line bg-surface p-5 sm:p-6">
            <h2 class="text-[15px] font-semibold">Cara membaca target ukuran</h2>
            <p class="mt-1.5 text-sm leading-6 text-ink-2">
                Batas ukuran selalu tertulis di pengumuman instansi dan di checklist berkas tiap formasi —
                samakan preset di sini dengan angka di sana. Bila hasil kompres masih di atas target,
                alat ini sudah memakai kualitas terendah yang wajar; coba turunkan dimensi foto atau
                bagi PDF menjadi dua file.
            </p>
        </div>
    </div>
</x-layouts.public>
