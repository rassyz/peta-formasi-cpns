<x-layouts.public>
    <x-slot:title>{{ $formasi->jabatan }} — {{ $formasi->instansi->nama }} · Peta Formasi</x-slot:title>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <a href="{{ route('formasi.index', ['jurusan' => $formasi->jurusan->first()?->slug]) }}" class="text-sm font-medium text-accent hover:underline">← Kembali ke pencarian</a>

        {{-- Kepala detail: ringkasan kartu polos (selalu tampil, data publik) --}}
        <header class="mt-4 rounded-2xl border border-line bg-surface p-5 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">{{ $formasi->jabatan }}</h1>
                    <p class="mt-1 text-[15px] text-ink-2">{{ $formasi->instansi->nama }} · {{ $formasi->penempatan }}</p>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <x-ui.badge variant="neutral">{{ $formasi->musim->label }} · {{ ucfirst($formasi->musim->status) }}</x-ui.badge>
                    <x-ui.badge variant="neutral">{{ ucfirst($formasi->instansi->wilayah) }}</x-ui.badge>
                    @if ($terbuka && $formasi->kartu_contoh)
                        <x-ui.badge variant="accent">Contoh — Detail Terbuka</x-ui.badge>
                    @endif
                    @if ($kelayakan)
                        <x-ui.badge :variant="$kelayakan['status'] === 'lolos' ? 'success' : ($kelayakan['status'] === 'perlu' ? 'warning' : 'danger')">
                            {{ $kelayakan['label'] }}
                        </x-ui.badge>
                    @endif
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-x-5 gap-y-1.5 border-t border-line pt-4 text-sm text-ink-2">
                @foreach ($formasi->kuota as $k)
                    <span>Kuota {{ ucfirst($k->jenis_kebutuhan) }}: <span class="tnum font-semibold text-ink">{{ number_format($k->jumlah, 0, ',', '.') }}</span></span>
                @endforeach
            </div>

            @if ($formasi->musim->status === 'historis')
                <p class="mt-4 rounded-lg bg-warning-soft px-3.5 py-2.5 text-sm text-warning">
                    Data {{ $formasi->musim->label }} bersifat historis — referensi strategi dari musim sebelumnya, bukan formasi yang sedang dibuka.
                </p>
            @endif
        </header>

        @if (! $terbuka)
            {{-- ============ Panel terkunci (DESIGN §3.6) — data premium TIDAK dirender sama sekali ============ --}}
            <section class="mt-6 rounded-2xl border border-line bg-surface p-5 sm:p-6" aria-labelledby="judul-terkunci">
                <div class="flex items-center gap-2.5">
                    <svg class="h-5 w-5 text-locked" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                    <h2 id="judul-terkunci" class="text-lg font-semibold tracking-tight">Detail formasi ini terkunci</h2>
                    <x-ui.badge variant="locked">Terkunci</x-ui.badge>
                </div>
                <p class="mt-2 max-w-2xl text-[15px] leading-7 text-ink-2">
                    Ringkasan di atas selalu gratis. Isi detail di bawah dibuka dengan pass untuk
                    {{ $formasi->musim->label }} — sekali bayar, tanpa langganan.
                </p>

                <ul class="mt-5 grid gap-x-8 gap-y-3 text-[15px] sm:grid-cols-2">
                    @foreach ([
                        'Kualifikasi pendidikan lengkap',
                        'Syarat kunci: usia, IPK, SKL, akreditasi',
                        'Syarat khusus sesuai teks pengumuman',
                        'Gaji rinci beserta sumber & tanggal verifikasinya',
                        'Jumlah pelamar rinci & riwayat snapshot',
                        'Checklist berkas formasi ini',
                        'Cek kelayakan untuk profil kamu',
                    ] as $isi)
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-locked" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            <span>{{ $isi }}</span>
                        </li>
                    @endforeach
                </ul>

                {{-- Kerangka kosong isi berbayar (DESIGN §12.4): bentuknya terasa, datanya tidak ada --}}
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-dashed border-line-strong p-4">
                        <p class="flex items-center gap-2 text-[13px] font-medium text-ink-2">
                            <svg class="h-4 w-4 text-locked" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            Meter peluang
                        </p>
                        <div class="mt-3 flex gap-1" aria-hidden="true">
                            <div class="h-2.5 flex-1 rounded-full bg-sunken"></div>
                            <div class="h-2.5 flex-1 rounded-full bg-sunken"></div>
                            <div class="h-2.5 flex-1 rounded-full bg-sunken"></div>
                        </div>
                        <div class="mt-1.5 flex text-[11px] text-ink-3" aria-hidden="true">
                            <span class="flex-1">Ketat</span>
                            <span class="flex-1">Bersaing</span>
                            <span class="flex-1">Longgar</span>
                        </div>
                    </div>
                    <div class="rounded-xl border border-dashed border-line-strong p-4">
                        <p class="flex items-center gap-2 text-[13px] font-medium text-ink-2">
                            <svg class="h-4 w-4 text-locked" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            Grafik riwayat pelamar
                        </p>
                        <svg class="mt-2 w-full" viewBox="0 0 300 96" aria-hidden="true">
                            <polyline points="8,74 70,60 130,66 190,42 250,48 292,28" fill="none" class="stroke-line-strong" stroke-width="2" stroke-dasharray="5 5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2.5">
                    <a href="{{ route('harga') }}" class="inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800">
                        Beli Pass — mulai Rp10.000
                    </a>
                    @if (Route::has('akun.redeem'))
                        <a href="{{ route('akun.redeem') }}" class="inline-flex h-11 items-center rounded-lg border border-line-strong bg-surface px-5 text-sm font-medium transition-colors hover:bg-sunken">
                            Sudah punya kode? Redeem
                        </a>
                    @endif
                </div>
                <p class="mt-3 text-[13px] text-ink-2">Ingin melihat isinya dulu? Kartu bertanda <span class="font-medium text-accent">Contoh</span> di daftar formasi detailnya terbuka penuh untuk semua orang.</p>
            </section>
        @else
            {{-- ============ Detail terbuka (kartu contoh di Tahap 3; pemegang pass di Tahap 4) ============ --}}
            @if ($dapatCek && ! $kelayakan)
                <section class="mt-6 rounded-2xl border border-accent/25 bg-accent-soft p-5 sm:p-6">
                    <h2 class="text-lg font-semibold tracking-tight">Cek kelayakan belum dijalankan</h2>
                    <p class="mt-2 max-w-2xl text-[15px] leading-7 text-ink-2">
                        Pass Strategi kamu aktif untuk musim ini, tetapi profil kelayakan pada sesi ini masih kosong.
                        Jalankan cek dari Finder — hasilnya akan menempel di kartu dan muncul per aturan di halaman detail seperti ini.
                    </p>
                    <a href="{{ route('formasi.index', ['jurusan' => $formasi->jurusan->first()?->slug, 'musim' => $formasi->musim_id]) }}"
                        class="mt-4 inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800">
                        Jalankan Cek di Finder
                    </a>
                </section>
            @endif

            <div class="mt-6 grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    @if ($kelayakan)
                        {{-- Hasil Cek Kelayakan per aturan --}}
                        <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <h2 class="text-lg font-semibold tracking-tight">Hasil Cek Kelayakan</h2>
                                <x-ui.badge :variant="$kelayakan['status'] === 'lolos' ? 'success' : ($kelayakan['status'] === 'perlu' ? 'warning' : 'danger')">
                                    {{ $kelayakan['label'] }}
                                </x-ui.badge>
                            </div>
                            <p class="mt-2 text-sm leading-6 text-ink-2">
                                Penilaian syarat formal berdasarkan profil sesi kamu dan data terstruktur formasi ini.
                                Ini bukan janji diterima — keputusan akhir selalu mengikuti pengumuman resmi dan verifikasi SSCASN.
                            </p>

                            <div class="mt-4 divide-y divide-line border-y border-line">
                                @foreach ($kelayakan['aturan'] as $aturan)
                                    <div class="grid gap-2 py-3.5 sm:grid-cols-[minmax(0,1fr)_auto] sm:gap-4">
                                        <div>
                                            <h3 class="text-sm font-semibold">
                                                <span class="mr-1.5 text-ink-3">{{ $aturan['kode'] }}</span>{{ $aturan['label'] }}
                                            </h3>
                                            <p class="mt-1 text-sm leading-6 {{ $aturan['status'] === 'gagal' ? 'text-danger' : ($aturan['status'] === 'perlu' ? 'text-warning' : 'text-ink-2') }}">
                                                {{ $aturan['pesan'] }}
                                            </p>
                                        </div>
                                        <div>
                                            <x-ui.badge :variant="$aturan['status'] === 'lolos' ? 'success' : ($aturan['status'] === 'gagal' ? 'danger' : ($aturan['status'] === 'perlu' ? 'warning' : 'neutral'))">
                                                {{ $aturan['status'] === 'lolos' ? 'Lolos' : ($aturan['status'] === 'gagal' ? 'Tidak Memenuhi' : ($aturan['status'] === 'perlu' ? 'Perlu Dicek' : 'Cek Mandiri')) }}
                                            </x-ui.badge>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <p class="mt-3 text-[13px] leading-5 text-ink-2">
                                R-08 tidak mengubah status otomatis: syarat khusus tetap harus kamu nilai sendiri dari teks pengumuman.
                            </p>
                        </section>
                    @endif

                    {{-- Kualifikasi --}}
                    <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6">
                        <h2 class="text-lg font-semibold tracking-tight">Kualifikasi Pendidikan</h2>
                        <ul class="mt-3 flex flex-wrap gap-1.5">
                            @foreach ($detail['kualifikasi'] as $j)
                                <li>
                                    <a href="{{ route('jurusan.show', $j->slug) }}" class="inline-block rounded-full border border-line-strong px-3 py-1.5 text-sm hover:bg-sunken">
                                        {{ $j->jenjang }} {{ $j->nama }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>

                    {{-- Syarat kunci --}}
                    <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6">
                        <h2 class="text-lg font-semibold tracking-tight">Syarat Kunci</h2>
                        <dl class="mt-4 grid grid-cols-2 gap-4 text-sm lg:grid-cols-4">
                            <div>
                                <dt class="text-ink-2">Usia</dt>
                                <dd class="mt-0.5 font-semibold">{{ $formasi->usia_min ?? '—' }}–{{ $formasi->usia_maks ?? '—' }} tahun</dd>
                            </div>
                            <div>
                                <dt class="text-ink-2">IPK minimum</dt>
                                <dd class="mt-0.5 font-semibold">{{ $formasi->ipk_min !== null ? number_format((float) $formasi->ipk_min, 2, ',', '.') : 'Tidak ditetapkan' }}</dd>
                            </div>
                            <div>
                                <dt class="text-ink-2">SKL diterima</dt>
                                <dd class="mt-0.5 font-semibold">{{ $formasi->skl_diterima ? 'Ya' : 'Tidak — wajib ijazah' }}</dd>
                            </div>
                            <div>
                                <dt class="text-ink-2">Akreditasi prodi</dt>
                                <dd class="mt-0.5 font-semibold">{{ $formasi->syarat_akreditasi === 'wajib' ? 'Wajib terakreditasi' : 'Tidak disyaratkan' }}</dd>
                            </div>
                        </dl>
                        <p class="mt-3 text-[13px] text-ink-2">Usia dihitung pada tanggal acuan musim {{ $formasi->musim->label }}: {{ $formasi->musim->tanggal_acuan_usia?->translatedFormat('d F Y') }}.</p>
                    </section>

                    {{-- Syarat khusus --}}
                    @if ($formasi->syarat_khusus)
                        <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6">
                            <h2 class="text-lg font-semibold tracking-tight">Syarat Khusus</h2>
                            <p class="mt-2.5 whitespace-pre-line text-[15px] leading-7">{{ $formasi->syarat_khusus }}</p>
                            <p class="mt-2 text-[13px] text-ink-2">Ditampilkan apa adanya dari pengumuman — bagian ini dinilai manual oleh peserta (cek mandiri).</p>
                        </section>
                    @endif

                    {{-- Gaji --}}
                    <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6">
                        <h2 class="text-lg font-semibold tracking-tight">Gaji</h2>
                        @if ($formasi->gaji_min !== null || $formasi->gaji_maks !== null)
                            <p class="tnum mt-2 text-xl font-bold tracking-tight">
                                Rp{{ number_format($formasi->gaji_min ?? $formasi->gaji_maks, 0, ',', '.') }} – Rp{{ number_format($formasi->gaji_maks ?? $formasi->gaji_min, 0, ',', '.') }}
                                <span class="text-sm font-normal text-ink-2">/ bulan (perkiraan total penghasilan)</span>
                            </p>
                            <p class="mt-1.5 text-[13px] text-ink-2">Sumber: {{ $formasi->sumber_gaji }} · Diverifikasi {{ $formasi->tanggal_verifikasi_gaji?->translatedFormat('d F Y') }}</p>
                        @else
                            <p class="mt-2 text-[15px] text-ink-2">Gaji belum tersedia — belum ditemukan sumber resmi yang dapat dirujuk untuk formasi ini, jadi kami tidak menampilkan perkiraan karangan.</p>
                        @endif
                    </section>

                    {{-- Pelamar & snapshot --}}
                    <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6">
                        <h2 class="text-lg font-semibold tracking-tight">Pelamar &amp; Riwayat Snapshot</h2>
                        @foreach ($detail['kuota_snapshot'] as $ks)
                            <div class="mt-4 border-t border-line pt-4 first:border-t-0 first:pt-0">
                                <h3 class="text-sm font-semibold">Kebutuhan {{ ucfirst($ks['jenis']) }} · Kuota <span class="tnum">{{ number_format($ks['jumlah'], 0, ',', '.') }}</span></h3>
                                @if ($ks['snapshot_terakhir'])
                                    @php
                                        $snapT = $ks['snapshot_terakhir'];
                                        $sepi = (int) $snapT->jumlah_ms === 0;
                                        $peluang = $ks['peluang'];
                                        $band = $sepi ? 'sepi' : ($peluang === null ? null : ($peluang >= 5 ? 'longgar' : ($peluang >= 1 ? 'bersaing' : 'ketat')));
                                        $riwayatNaik = $ks['riwayat']->sortBy('tanggal_snapshot')->values();
                                        $koordinat = [];
                                        $titikString = '';
                                        $areaString = '';
                                        if ($riwayatNaik->count() >= 2) {
                                            $maks = max(1, (int) $riwayatNaik->max('jumlah_ms'));
                                            $n = $riwayatNaik->count();
                                            foreach ($riwayatNaik as $i => $s) {
                                                $koordinat[] = [
                                                    'x' => round(8 + $i * (284 / ($n - 1)), 1),
                                                    'y' => round(88 - ($s->jumlah_ms / $maks) * 62, 1),
                                                    'snap' => $s,
                                                ];
                                            }
                                            $titikString = collect($koordinat)->map(fn ($k) => $k['x'] . ',' . $k['y'])->implode(' ');
                                            $areaString = '8,88 ' . $titikString . ' 292,88';
                                        }
                                    @endphp

                                    {{-- Blok peluang (DESIGN §12.2) --}}
                                    @if ($sepi)
                                        <div class="mt-3 flex flex-wrap items-center gap-x-3.5 gap-y-2">
                                            <p class="text-3xl font-bold tracking-tight text-accent">Sepi</p>
                                            <x-ui.badge variant="accent">0 pelamar MS</x-ui.badge>
                                        </div>
                                        <div class="mt-3 h-2.5 rounded-full bg-accent" role="img" aria-label="Meter peluang: sepi, nol pelamar memenuhi syarat"></div>
                                        <p class="mt-2 text-sm leading-6 text-ink-2">
                                            Tidak ada pelamar yang memenuhi syarat pada snapshot terakhir — kuota kebutuhan ini belum terperebutkan sama sekali.
                                        </p>
                                    @elseif ($peluang !== null)
                                        <div class="mt-3 flex flex-wrap items-center gap-x-3.5 gap-y-2">
                                            <p class="tnum text-3xl font-bold tracking-tight">{{ number_format($peluang, 2, ',', '.') }}%</p>
                                            @if ($band === 'longgar')
                                                <x-ui.badge variant="success">Peluang longgar</x-ui.badge>
                                            @elseif ($band === 'bersaing')
                                                <x-ui.badge variant="warning">Persaingan sedang</x-ui.badge>
                                            @else
                                                <x-ui.badge variant="danger">Persaingan ketat</x-ui.badge>
                                            @endif
                                            @if ($ks['rasio'] !== null)
                                                <span class="text-sm text-ink-2">Rasio <span class="tnum font-semibold text-ink">{{ number_format($ks['rasio'], 1, ',', '.') }} : 1</span></span>
                                            @endif
                                        </div>
                                        <div class="mt-3 flex gap-1" role="img" aria-label="Meter peluang: {{ $band === 'longgar' ? 'peluang longgar' : ($band === 'bersaing' ? 'persaingan sedang' : 'persaingan ketat') }}">
                                            <div class="h-2.5 flex-1 rounded-full {{ $band === 'ketat' ? 'bg-danger' : 'bg-sunken' }}"></div>
                                            <div class="h-2.5 flex-1 rounded-full {{ $band === 'bersaing' ? 'bg-warning' : 'bg-sunken' }}"></div>
                                            <div class="h-2.5 flex-1 rounded-full {{ $band === 'longgar' ? 'bg-success' : 'bg-sunken' }}"></div>
                                        </div>
                                        <div class="mt-1.5 flex text-[11px] text-ink-3" aria-hidden="true">
                                            <span class="flex-1">Ketat &lt;1%</span>
                                            <span class="flex-1">Bersaing 1–5%</span>
                                            <span class="flex-1">Longgar ≥5%</span>
                                        </div>
                                        <p class="mt-2 text-sm leading-6 text-ink-2">
                                            Dari <span class="tnum font-semibold text-ink">{{ number_format($snapT->jumlah_ms, 0, ',', '.') }}</span> pelamar MS
                                            untuk kuota <span class="tnum font-semibold text-ink">{{ number_format($ks['jumlah'], 0, ',', '.') }}</span>.
                                        </p>
                                    @endif

                                    <p class="mt-1.5 text-[13px] text-ink-2">
                                        Snapshot {{ $snapT->tipe }} · {{ $snapT->tanggal_snapshot?->translatedFormat('d F Y') }}
                                        @if ($snapT->sumber) · {{ $snapT->sumber }} @endif
                                    </p>

                                    {{-- Grafik riwayat snapshot (DESIGN §12.3) --}}
                                    @if ($riwayatNaik->count() >= 2)
                                        <svg class="mt-3 w-full" viewBox="0 0 300 96" role="img" aria-label="Grafik riwayat jumlah pelamar memenuhi syarat">
                                            <polygon points="{{ $areaString }}" class="fill-accent/10" />
                                            <polyline points="{{ $titikString }}" fill="none" class="stroke-accent" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />
                                            @foreach ($koordinat as $k)
                                                <circle cx="{{ $k['x'] }}" cy="{{ $k['y'] }}" r="3" class="fill-surface stroke-accent" stroke-width="2" />
                                            @endforeach
                                        </svg>
                                        <p class="mt-1 flex justify-between gap-3 text-[11px] text-ink-3">
                                            <span>{{ $riwayatNaik->first()->tanggal_snapshot?->translatedFormat('d M Y') }} · MS <span class="tnum">{{ number_format($riwayatNaik->first()->jumlah_ms, 0, ',', '.') }}</span></span>
                                            <span class="text-right">{{ $riwayatNaik->last()->tanggal_snapshot?->translatedFormat('d M Y') }} · MS <span class="tnum">{{ number_format($riwayatNaik->last()->jumlah_ms, 0, ',', '.') }}</span></span>
                                        </p>
                                    @elseif ($riwayatNaik->count() === 1)
                                        <svg class="mt-3 w-full" viewBox="0 0 300 96" role="img" aria-label="Baru ada satu snapshot pelamar">
                                            <line x1="8" x2="292" y1="88" y2="88" class="stroke-line" stroke-width="1" />
                                            <circle cx="150" cy="46" r="4" class="fill-accent" />
                                            <text x="150" y="30" text-anchor="middle" class="fill-ink text-[12px] font-semibold">MS {{ number_format($riwayatNaik->first()->jumlah_ms, 0, ',', '.') }}</text>
                                        </svg>
                                        <p class="mt-1 text-center text-[11px] text-ink-3">Baru satu snapshot — grafik tren terbentuk setelah snapshot berikutnya masuk.</p>
                                    @endif

                                    <ul class="mt-2.5 space-y-1 text-[13px] text-ink-2">
                                        @foreach ($ks['riwayat'] as $snap)
                                            <li>
                                                {{ $snap->tanggal_snapshot?->translatedFormat('d M Y') }} · {{ ucfirst($snap->tipe) }} ·
                                                MS <span class="tnum">{{ number_format($snap->jumlah_ms, 0, ',', '.') }}</span>
                                                @if ($snap->jumlah_total !== null) · Total <span class="tnum">{{ number_format($snap->jumlah_total, 0, ',', '.') }}</span>@endif
                                                @if ($snap->sumber) · {{ $snap->sumber }}@endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="mt-1.5 text-sm text-ink-2">Data pelamar belum tersedia untuk kebutuhan ini.</p>
                                @endif
                            </div>
                        @endforeach
                        @if ($formasi->musim->status === 'historis')
                            <p class="mt-4 text-[13px] text-ink-2">Angka historis adalah referensi musim sebelumnya, bukan prediksi untuk musim berikutnya.</p>
                        @endif
                    </section>
                </div>

                {{-- Checklist (rel kanan sticky di lg) --}}
                <aside class="lg:sticky lg:top-24 lg:self-start">
                    <section class="rounded-2xl border border-line bg-surface p-5 sm:p-6">
                        <h2 class="text-lg font-semibold tracking-tight">Checklist Berkas</h2>
                        <ul class="mt-3 space-y-3">
                            @foreach ($detail['checklist'] as $dok)
                                <li class="flex items-start justify-between gap-3 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                    <div>
                                        <p class="text-[15px] font-medium leading-snug">{{ $dok->nama_dokumen }}</p>
                                        <p class="mt-0.5 text-[13px] text-ink-2">
                                            {{ $dok->format ?? 'Format mengikuti SSCASN' }}
                                            @if ($dok->ukuran_maks_kb) · maks <span class="tnum">{{ number_format($dok->ukuran_maks_kb, 0, ',', '.') }}</span> KB @endif
                                            @if (! $dok->wajib) · opsional @endif
                                        </p>
                                    </div>
                                    @if ($dok->ukuran_maks_kb)
                                        <a href="{{ route('toolkit', ['target' => $dok->ukuran_maks_kb <= 200 ? 200 : ($dok->ukuran_maks_kb <= 500 ? 500 : 1024)]) }}"
                                            class="shrink-0 rounded-lg border border-line-strong px-3 py-1.5 text-[13px] font-medium transition-colors hover:bg-sunken">
                                            Kompres
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <p class="mt-3 text-[13px] text-ink-2">Tombol Kompres membuka Toolkit dengan target ukuran dokumen itu (FR-D3).</p>
                    </section>
                </aside>
            </div>
        @endif

        <footer class="mt-6 text-[13px] text-ink-2">
            Sumber: {{ $formasi->sumber_pengumuman ?? 'Pengumuman resmi instansi' }}
            @if ($formasi->tanggal_verifikasi) · Diverifikasi {{ $formasi->tanggal_verifikasi->translatedFormat('d F Y') }} @endif
            @if ($formasi->instansi->link_pengumuman) · <a href="{{ $formasi->instansi->link_pengumuman }}" class="text-accent hover:underline" rel="noopener" target="_blank">Pengumuman resmi</a> @endif
        </footer>
    </div>
</x-layouts.public>
