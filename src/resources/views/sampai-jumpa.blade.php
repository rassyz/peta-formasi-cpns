<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Peta Formasi</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-canvas bg-kontur px-4 text-ink">
    {{-- Halaman perpisahan setelah keluar (15 Nov 2026) — dokumen MANDIRI tanpa
         navbar/footer layout publik, sesuai keputusan desain: hanya pesan
         perpisahannya saja. Sesi SUDAH dihanguskan server (Fortify) sebelum
         halaman ini tampil; halaman ini murni pengesahan + transisi.
         Penghitung 1% -> 100% selama 3,6 detik menggerakkan angka dan bilah
         dari satu sumber, lalu mengalihkan ke Beranda. --}}
    <main class="flex w-full max-w-md flex-col items-center py-16 text-center">
        <div class="relative flex h-16 w-16 items-center justify-center" aria-hidden="true">
            <span class="cincin-denyut absolute inset-0 rounded-full bg-accent/25"></span>
            <span class="relative flex h-16 w-16 items-center justify-center rounded-full bg-accent-soft text-accent">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            </span>
        </div>

        <h1 class="mt-6 text-2xl font-semibold tracking-tight sm:text-3xl">Sampai jumpa!</h1>
        <p class="mt-2.5 max-w-md leading-7 text-ink-2">Kamu sudah keluar dengan aman — sesi ini sudah ditutup. Terima kasih sudah memakai Peta Formasi.</p>

        <p id="persen" class="mt-9 text-5xl font-bold tabular-nums tracking-tight" aria-hidden="true">1%</p>

        <div class="mt-4 w-full max-w-xs" aria-hidden="true">
            <div class="h-1.5 overflow-hidden rounded-full bg-sunken">
                <div id="bilah" class="h-full rounded-full bg-accent" style="width: 1%"></div>
            </div>
        </div>
        <p id="status" class="mt-2.5 text-[13px] text-ink-3">Menutup sesi dengan aman…</p>

        <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('home') }}"
                class="inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800">
                Ke Beranda sekarang
            </a>
            @if (Route::has('login'))
                <a href="{{ route('login') }}"
                    class="inline-flex h-11 items-center rounded-lg border border-line-strong px-5 text-sm font-medium transition-colors hover:bg-sunken">
                    Masuk lagi
                </a>
            @endif
        </div>
    </main>

    <script>
        // Penghitung 1% -> 100% yang digerakkan WAKTU berlalu (requestAnimationFrame
        // membaca cap waktu asli, jadi persen selalu jujur terhadap waktu).
        // Kurva easeInOutQuad: berangkat perlahan (angka awal terbaca satu per
        // satu), mengalir di tengah, lalu mendarat perlahan (angka terakhir
        // terbaca satu per satu). Selesai di 100% ada jeda selesai 0,5 detik
        // dengan status berubah, baru mengalihkan ke Beranda — kedatangan di
        // 100% tidak langsung lenyap. Angka, bilah, dan status satu sumber.
        // Penghitung ini SENGAJA tetap berjalan di mesin reduced-motion:
        // ia informasi kemajuan, bukan gerak dekoratif — sejalan keputusan
        // OPSI 2 pada transisi (kill-switch app.css dilunakkan). Tanpa ini,
        // mesin dengan Animation effects mati (Windows) meloncati animasi
        // seluruhnya: langsung 100% dan pergi dalam 0,4 detik.
        (function () {
            var DURASI = 2000;
            var TAHAN_SELESAI = 600;
            var tujuan = @json(route('home'));
            var angka = document.getElementById('persen');
            var bilah = document.getElementById('bilah');
            var status = document.getElementById('status');

            function selesai() {
                angka.textContent = '100%';
                bilah.style.width = '100%';
                status.textContent = 'Selesai — mengalihkan ke Beranda…';
                setTimeout(function () { window.location.href = tujuan; }, TAHAN_SELESAI);
            }

            var mulai = null;
            var terakhir = 1;
            function langkah(stempel) {
                if (mulai === null) mulai = stempel;
                var t = Math.min((stempel - mulai) / DURASI, 1);
                var p = t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2; // easeInOutQuad
                var nilai = Math.max(1, Math.round(p * 100));
                if (nilai !== terakhir) {
                    angka.textContent = nilai + '%';
                    terakhir = nilai;
                }
                bilah.style.width = (p * 100) + '%';
                if (t < 1) {
                    requestAnimationFrame(langkah);
                } else {
                    selesai();
                }
            }
            requestAnimationFrame(langkah);
        })();
    </script>
</body>
</html>
