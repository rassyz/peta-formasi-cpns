@props([
    'jabatan',
    'instansi',
    'penempatan' => null,
    'kebutuhan' => null,      // Jenis kebutuhan, mis. Umum
    'kuota' => null,
    'musimLabel' => null,     // Mis. CPNS 2024 · Historis
    'status' => null,         // null | lolos | gagal | perlu
    'alasan' => null,         // Alasan spesifik bila status gagal/perlu
    'contoh' => false,
    'href' => '#',
    'tampilkanMusim' => true, // Finder mematikan ini: daftarnya selalu satu musim
    'pelamarTotal' => null,   // Total pendaftar snapshot terakhir — HANYA diisi untuk yang berhak (FR-A2)
    'pelamarTerkunci' => false, // true = tampilkan baris teaser terkunci tanpa angka
    'totalKuota' => null,      // Jumlah SEMUA baris kuota formasi; baris "Total" hanya tampil bila > kuota utama
])

@php
    $badge = match ($status) {
        'lolos' => ['success', 'Lolos Syarat'],
        'gagal' => ['danger', 'Tidak Memenuhi'],
        'perlu' => ['warning', 'Perlu Dicek'],
        default => null,
    };
@endphp

{{-- Kartu daftar polos (PRD §5): tanpa rasio/gaji. Angka pelamar di kartu = total pendaftar snapshot terakhir, hanya untuk pemegang pass (FR-A2); MS difiturkan di detail. Kuota tampil sebagai pil. --}}
<a href="{{ $href }}" class="group isolate relative flex h-full flex-col rounded-xl border border-line bg-surface p-4 shadow-card transition-[translate,transform,border-color] duration-300 ease-[cubic-bezier(0.22,1,0.36,1)] after:pointer-events-none after:absolute after:inset-0 after:-z-10 after:rounded-xl after:shadow-lift after:opacity-0 after:transition-opacity after:duration-300 hover:border-accent/50 hover:after:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent hover:-translate-y-1">
    <div class="flex items-start justify-between gap-3">
        <h3 class="text-[15px] font-semibold leading-snug text-ink sm:text-base">{{ $jabatan }}</h3>
        @if ($contoh)
            <x-ui.badge variant="accent" class="shrink-0">Contoh</x-ui.badge>
        @endif
    </div>

    @if ($penempatan)
        <p class="mt-2 flex items-start gap-1.5 text-sm font-medium leading-snug text-ink">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>{{ $penempatan }}</span>
        </p>
    @endif
    <p class="mt-1 flex items-center gap-1.5 text-[13px] leading-snug text-ink-2">
        <svg class="h-4 w-4 shrink-0 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 22h18"/><path d="M6 18v-7M10 18v-7M14 18v-7M18 18v-7"/><path d="m12 2 8 5H4Z"/></svg>
        <span>{{ $instansi }}</span>
    </p>

    @if ($pelamarTotal !== null)
        <p class="mt-2 flex items-center gap-1.5 text-[13px] text-ink-2">
            <svg class="h-4 w-4 shrink-0 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Pelamar <span class="tnum font-semibold text-ink">{{ number_format($pelamarTotal, 0, ',', '.') }}</span>
        </p>
    @elseif ($pelamarTerkunci)
        <p class="mt-2 flex items-center gap-1.5 text-[13px] text-ink-3">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Data pelamar · terbuka dengan pass
        </p>
    @endif

    @if ($kuota !== null)
        <div class="mt-3 flex flex-wrap gap-1.5">
            <span class="inline-flex items-center rounded-full bg-accent-soft px-2.5 py-1 text-xs text-ink-2">Kuota{{ $kebutuhan ? ' ' . $kebutuhan : '' }}:<span class="tnum ml-1 font-semibold text-ink">{{ number_format($kuota, 0, ',', '.') }}</span></span>
            @if ($totalKuota !== null && $totalKuota > $kuota)
                <span class="inline-flex items-center rounded-full bg-accent-soft px-2.5 py-1 text-xs text-ink-2">Total:<span class="tnum ml-1 font-semibold text-ink">{{ number_format($totalKuota, 0, ',', '.') }}</span></span>
            @endif
        </div>
    @endif

    @if ($musimLabel && $tampilkanMusim)
        <div class="mt-3 flex flex-wrap gap-1.5">
            <span class="inline-flex items-center rounded-full bg-sunken px-2.5 py-1 text-xs font-medium text-ink-2">{{ $musimLabel }}</span>
        </div>
    @endif

    <div class="mt-auto pt-4">
        <div class="border-t border-line pt-3">
            <div class="min-w-0">
                @if ($badge)
                    <x-ui.badge :variant="$badge[0]">{{ $badge[1] }}</x-ui.badge>
                    @if ($alasan)
                        <p class="mt-2 text-[13px] leading-5 {{ $status === 'gagal' ? 'text-danger' : 'text-warning' }}">{{ $alasan }}</p>
                    @endif
                @else
                    <p class="flex items-center gap-1.5 text-[13px] text-ink-3">
                        <svg class="h-3.5 w-3.5 shrink-0 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18ZM12 7v5l3 2"/></svg>
                        Syarat belum dicek
                    </p>
                @endif
            </div>
        </div>
    </div>
</a>
