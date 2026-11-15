<x-layouts.public>
    <x-slot:title>Harga Pass — Peta Formasi</x-slot:title>

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <x-ui.kepala-halaman judul="Harga Pass" varian="kontur">
            Semua fungsi pencarian formasi <span class="font-medium text-ink">gratis</span> — pass hanya membuka isi detailnya. Sekali bayar per musim, tanpa langganan, tanpa perpanjangan otomatis. Masa berlaku tiap pass tertulis di kartunya, sebelum kamu membeli.
        </x-ui.kepala-halaman>

        <div class="mt-8 grid gap-4 lg:grid-cols-3">
            {{-- Pass Historis --}}
            <section class="flex flex-col rounded-2xl border border-line bg-surface p-6">
                <h2 class="font-semibold">Pass Peta Historis</h2>
                <p class="tnum mt-3 text-3xl font-bold tracking-tight">Rp10.000</p>
                <p class="mt-1 text-sm text-ink-2">Sekali bayar · satu musim historis</p>
                <ul class="mt-5 flex-1 space-y-3 border-t border-line pt-5 text-[15px] leading-6">
                    <li class="flex gap-2.5">
                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Detail lengkap semua formasi musim historis</span>
                    </li>
                    <li class="flex gap-2.5">
                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Kualifikasi &amp; syarat asli pengumuman</span>
                    </li>
                    <li class="flex gap-2.5">
                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Gaji rinci beserta sumbernya</span>
                    </li>
                    <li class="flex gap-2.5">
                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Angka pelamar rinci &amp; riwayat snapshot</span>
                    </li>
                </ul>
                <p class="mt-6 flex items-start gap-2 border-t border-line pt-4 text-[13px] leading-5 text-ink-2">
                    <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    <span>Berlaku tanpa batas tanggal selama musim itu diterbitkan</span>
                </p>
                <button type="button" disabled class="mt-4 inline-flex h-11 w-full items-center justify-center rounded-lg bg-sunken px-5 text-sm font-medium text-ink-2">
                    Penjualan dibuka saat rilis musim
                </button>
            </section>

            {{-- Pass Strategi --}}
            <section class="relative flex flex-col rounded-2xl border-2 border-accent bg-surface p-6">
                <span class="absolute -top-3 left-6 inline-flex items-center gap-1 rounded-full bg-accent px-2.5 py-0.5 text-xs font-semibold text-white">
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    Paling lengkap — utama saat musim aktif
                </span>
                <h2 class="font-semibold">Pass Strategi 2026</h2>
                <p class="tnum mt-3 text-3xl font-bold tracking-tight">Rp19.000</p>
                <p class="mt-1 text-sm text-ink-2">Early bird Rp15.000 · sekali bayar</p>
                <ul class="mt-5 flex-1 space-y-3 border-t border-line pt-5 text-[15px] leading-6">
                    <li class="flex gap-2.5">
                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Semua isi Pass Historis, untuk musim aktif</span>
                    </li>
                    <li class="flex gap-2.5">
                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Detail lengkap formasi musim berjalan</span>
                    </li>
                    <li class="flex gap-2.5">
                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Cek kelayakan personal per formasi</span>
                    </li>
                    <li class="flex gap-2.5">
                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Checklist berkas per formasi + lompat ke Toolkit</span>
                    </li>
                    <li class="flex gap-2.5">
                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Angka pelamar live rinci &amp; indikator tren</span>
                    </li>
                </ul>
                <p class="mt-6 flex items-start gap-2 border-t border-line pt-4 text-[13px] leading-5 text-ink-2">
                    <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    <span>Berlaku sampai 30 hari setelah pendaftaran musim ditutup</span>
                </p>
                <button type="button" disabled class="mt-4 inline-flex h-11 w-full items-center justify-center rounded-lg bg-sunken px-5 text-sm font-medium text-ink-2">
                    Penjualan dibuka saat rilis musim
                </button>
            </section>

            {{-- Bundle Lolos --}}
            <section class="flex flex-col rounded-2xl border border-line bg-surface p-6">
                <h2 class="font-semibold">Bundle Lolos</h2>
                <p class="tnum mt-3 text-3xl font-bold tracking-tight">Rp39.000</p>
                <p class="mt-1 text-sm text-ink-2">Sekali bayar · Pass Strategi + paket latihan</p>
                <ul class="mt-5 flex-1 space-y-3 border-t border-line pt-5 text-[15px] leading-6">
                    <li class="flex gap-2.5">
                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Semua isi Pass Strategi 2026</span>
                    </li>
                    <li class="flex gap-2.5">
                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Paket latihan SKD 450 soal + kunci &amp; pembahasan</span>
                    </li>
                    <li class="flex gap-2.5">
                        <svg class="mt-1 h-4 w-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>2 tryout penuh format resmi + tracker skor</span>
                    </li>
                </ul>
                <p class="mt-6 flex items-start gap-2 border-t border-line pt-4 text-[13px] leading-5 text-ink-2">
                    <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    <span>Masa berlaku mengikuti Pass Strategi 2026</span>
                </p>
                <button type="button" disabled class="mt-4 inline-flex h-11 w-full items-center justify-center rounded-lg bg-sunken px-5 text-sm font-medium text-ink-2">
                    Penjualan dibuka saat rilis musim
                </button>
            </section>
        </div>

        <div class="mt-6 rounded-2xl border border-line bg-surface p-5 sm:p-6">
            <h2 class="text-[15px] font-semibold">Cara membeli</h2>
            <ol class="mt-2 list-decimal space-y-1.5 pl-5 text-sm leading-6 text-ink-2">
                <li>Beli pass di Lynk.id (tautan aktif saat penjualan dibuka) — kamu menerima satu kode akses unik.</li>
                <li>Daftar / masuk di situs ini, lalu redeem kode itu di halaman redeem.</li>
                <li>Pass langsung aktif di akunmu sesuai tipe dan musimnya. Satu kode hanya berlaku untuk satu akun.</li>
            </ol>
            <p class="mt-3 text-sm leading-6 text-ink-2">
                Pemegang Pass Historis dapat upgrade ke Pass Strategi dengan harga selisih melalui produk upgrade di Lynk.id.
                Kami tidak pernah menjanjikan kelulusan — pass ini alat bantu keputusan dan persiapan, keputusan akhir selalu mengikuti pengumuman resmi instansi.
            </p>
        </div>
    </div>
</x-layouts.public>
