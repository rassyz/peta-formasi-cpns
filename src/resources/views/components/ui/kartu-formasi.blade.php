@props([
    'jabatan',
    'instansi',
    'penempatan' => null,
    'wilayah' => null,        // Pusat | Daerah
    'kebutuhan' => null,      // Jenis kebutuhan, mis. Umum
    'kuota' => null,
    'musimLabel' => null,     // Mis. CPNS 2024 · Historis
    'status' => null,         // null | lolos | gagal | perlu
    'alasan' => null,         // Alasan spesifik bila status gagal/perlu
    'contoh' => false,
    'href' => '#',
])

@php
    $badge = match ($status) {
        'lolos' => ['success', 'Lolos Syarat'],
        'gagal' => ['danger', 'Tidak Memenuhi'],
        'perlu' => ['warning', 'Perlu Dicek'],
        default => null,
    };
@endphp

{{-- Kartu daftar SELALU polos (PRD §5): tidak ada angka pelamar/rasio/gaji di sini. --}}
<a href="{{ $href }}" class="group block rounded-xl border border-line bg-surface p-4 sm:p-5 transition-all hover:border-line-strong hover:shadow-float focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent">
    <div class="flex items-start justify-between gap-3">
        <h3 class="text-[15px] font-semibold leading-snug text-ink group-hover:text-accent sm:text-base">{{ $jabatan }}</h3>
        @if ($contoh)
            <x-ui.badge variant="accent" class="shrink-0">Contoh</x-ui.badge>
        @endif
    </div>

    <p class="mt-1 flex flex-wrap items-center gap-x-1.5 gap-y-0.5 text-sm text-ink-2">
        <span>{{ $instansi }}</span>
        @if ($penempatan)
            <span aria-hidden="true">·</span>
            <svg class="h-4 w-4 shrink-0 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>{{ $penempatan }}</span>
        @endif
    </p>

    <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-[13px] text-ink-2">
        @if ($wilayah)<span>{{ $wilayah }}</span>@endif
        @if ($kebutuhan)<span aria-hidden="true">·</span><span>Kebutuhan {{ $kebutuhan }}</span>@endif
        @if ($kuota !== null)<span aria-hidden="true">·</span><span>Kuota <span class="tnum font-semibold text-ink">{{ number_format($kuota, 0, ',', '.') }}</span></span>@endif
        @if ($musimLabel)<span aria-hidden="true">·</span><span>{{ $musimLabel }}</span>@endif
    </div>

    <div class="mt-3.5 border-t border-line pt-3">
        @if ($badge)
            <x-ui.badge :variant="$badge[0]">{{ $badge[1] }}</x-ui.badge>
            @if ($alasan)
                <p class="mt-2 text-[13px] leading-5 {{ $status === 'gagal' ? 'text-danger' : 'text-warning' }}">{{ $alasan }}</p>
            @endif
        @else
            <p class="text-[13px] text-ink-3">Syarat belum dicek</p>
        @endif
    </div>
</a>
