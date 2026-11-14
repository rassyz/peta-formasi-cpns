<x-layouts.public>
    <x-slot name="title">Fondasi Desain — PetaFormasi (Tahap 0)</x-slot>

    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="rounded-xl border border-warning bg-warning-soft px-4 py-3 text-sm text-warning">
            Halaman verifikasi <strong>Tahap 0</strong> — bukan halaman produk. Dipakai untuk memeriksa token &amp; komponen DESIGN.md sebelum fitur dibangun. Akan dihapus sebelum rilis.
        </div>

        <h1 class="mt-8 text-3xl font-bold tracking-tight">Fondasi Desain</h1>
        <p class="mt-2 max-w-2xl text-[15px] leading-6 text-ink-2">Sumber nilai: <code class="rounded bg-sunken px-1.5 py-0.5 text-[13px]">resources/css/app.css</code> (@theme Tailwind v4, DESIGN.md §2). Komponen: <code class="rounded bg-sunken px-1.5 py-0.5 text-[13px]">resources/views/components/ui/</code>.</p>

        {{-- Token warna --}}
        <h2 class="mt-10 text-2xl font-semibold tracking-tight">1 · Token Warna</h2>
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            @foreach ([
                ['canvas', 'bg-canvas border border-line'], ['surface', 'bg-surface border border-line'],
                ['sunken', 'bg-sunken'], ['line', 'bg-line'], ['line-strong', 'bg-line-strong'],
                ['ink', 'bg-ink'], ['ink-2', 'bg-ink-2'], ['ink-3', 'bg-ink-3'],
                ['accent', 'bg-accent'], ['accent-soft', 'bg-accent-soft border border-line'],
                ['success', 'bg-success'], ['warning', 'bg-warning'], ['danger', 'bg-danger'], ['locked', 'bg-locked'],
            ] as [$nama, $kelas])
                <div class="overflow-hidden rounded-xl border border-line bg-surface">
                    <div class="h-12 {{ $kelas }}"></div>
                    <p class="px-3 py-2 text-[13px] font-medium">{{ $nama }}</p>
                </div>
            @endforeach
        </div>

        {{-- Tipografi --}}
        <h2 class="mt-10 text-2xl font-semibold tracking-tight">2 · Tipografi (Inter)</h2>
        <div class="mt-4 space-y-3 rounded-xl border border-line bg-surface p-5">
            <p class="text-[40px] font-bold leading-12 tracking-tight">Display — Temukan Formasi yang Peluangnya Nyata</p>
            <p class="text-3xl font-bold tracking-tight">H1 — Cari Formasi per Jurusan</p>
            <p class="text-2xl font-semibold tracking-tight">H2 — Syarat Kunci</p>
            <p class="text-[19px] font-semibold">H3 — Pranata Komputer Ahli Pertama</p>
            <p class="max-w-2xl text-base leading-6.5">Body — Berkas lamaran diunggah melalui SSCASN dengan ukuran dan format mengikuti ketentuan instansi. Periksa kembali setiap dokumen sebelum mengirim.</p>
            <p class="text-sm text-ink-2">Small — Sumber: Pengumuman BPK No. 2/S.Peng/X/08/2024 · Diverifikasi 14 Nov 2026</p>
            <p class="tnum text-[15px]">Angka tabular: 9.457 formasi · kuota 1.234 · peluang 3,4% · gaji Rp12,0–14,5 jt</p>
        </div>

        {{-- Tombol & chip --}}
        <h2 class="mt-10 text-2xl font-semibold tracking-tight">3 · Tombol &amp; Chip Filter</h2>
        <div class="mt-4 flex flex-wrap items-center gap-3 rounded-xl border border-line bg-surface p-5">
            <x-ui.button>Cari Formasi</x-ui.button>
            <x-ui.button variant="secondary">Kompres di Toolkit</x-ui.button>
            <x-ui.button variant="ghost">Lewati</x-ui.button>
            <x-ui.button size="sm">Redeem</x-ui.button>
            <span class="mx-1 h-6 w-px bg-line"></span>
            <x-ui.chip :active="true">0 Pelamar</x-ui.chip>
            <x-ui.chip>Pusat</x-ui.chip>
            <x-ui.chip>Daerah</x-ui.chip>
        </div>

        {{-- Badge --}}
        <h2 class="mt-10 text-2xl font-semibold tracking-tight">4 · Badge Status</h2>
        <div class="mt-4 flex flex-wrap items-center gap-2.5 rounded-xl border border-line bg-surface p-5">
            <x-ui.badge variant="success">Lolos Syarat</x-ui.badge>
            <x-ui.badge variant="danger">Tidak Memenuhi</x-ui.badge>
            <x-ui.badge variant="warning">Perlu Dicek</x-ui.badge>
            <x-ui.badge variant="neutral">Belum Dicek</x-ui.badge>
            <x-ui.badge variant="accent">Contoh</x-ui.badge>
            <x-ui.badge variant="locked">Terkunci</x-ui.badge>
            <x-ui.badge variant="neutral">CPNS 2024 · Historis</x-ui.badge>
        </div>

        {{-- Combobox & input --}}
        <h2 class="mt-10 text-2xl font-semibold tracking-tight">5 · Pemilih Jurusan &amp; Input</h2>
        <div class="mt-4 grid gap-5 rounded-xl border border-line bg-surface p-5 md:grid-cols-2">
            <x-ui.combobox-jurusan
                label="Jurusan kamu"
                size="lg"
                :items="[
                    ['value' => 'teknik-informatika', 'label' => 'Teknik Informatika', 'jenjang' => 'S1'],
                    ['value' => 'sistem-informasi', 'label' => 'Sistem Informasi', 'jenjang' => 'S1'],
                    ['value' => 'akuntansi', 'label' => 'Akuntansi', 'jenjang' => 'S1'],
                    ['value' => 'ilmu-hukum', 'label' => 'Ilmu Hukum', 'jenjang' => 'S1'],
                    ['value' => 'manajemen', 'label' => 'Manajemen', 'jenjang' => 'S1'],
                ]"
            />
            <x-ui.input label="Kode akses" name="kode_demo" placeholder="PF-XXXX-XXXX" hint="Contoh field standar dengan petunjuk." />
        </div>

        {{-- Kartu formasi --}}
        <h2 class="mt-10 text-2xl font-semibold tracking-tight">6 · Kartu Formasi (polos)</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-ui.kartu-formasi
                jabatan="Pranata Komputer Ahli Pertama"
                instansi="Badan Pemeriksa Keuangan"
                penempatan="Perwakilan Provinsi Banten"
                wilayah="Pusat"
                kebutuhan="Umum"
                :kuota="12"
                musim-label="CPNS 2024 · Historis"
            />
            <x-ui.kartu-formasi
                jabatan="Auditor Ahli Pertama"
                instansi="Badan Pemeriksa Keuangan"
                penempatan="Pusat"
                wilayah="Pusat"
                kebutuhan="Umum"
                :kuota="28"
                musim-label="CPNS 2024 · Historis"
                status="lolos"
                :contoh="true"
            />
            <x-ui.kartu-formasi
                jabatan="Pemeriksa Ahli Pertama"
                instansi="Badan Pemeriksa Keuangan"
                penempatan="Regional Jawa"
                wilayah="Daerah"
                kebutuhan="Umum"
                :kuota="2"
                musim-label="CPNS 2024 · Historis"
                status="gagal"
                alasan="IPK min 3,00 — IPK kamu 2,87"
            />
        </div>

        <p class="mt-10 text-sm text-ink-2">Checklist uji lengkap ada di RUNBOOK Tahap 0 (TC-0.1 s.d. TC-0.8).</p>
    </div>
</x-layouts.public>
