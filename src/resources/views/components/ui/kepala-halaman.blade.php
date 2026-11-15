@props(['judul', 'varian' => 'polos'])
{{-- Kepala halaman bersama (15 Nov 2026): satu rangka judul untuk semua
     halaman — tipografi H1, gaya teks pengantar, ritme ruang, dan
     geometri panel identik di mana pun. Yang membedakan antar-halaman
     hanya aksennya: varian 'kontur'/'titik' (panel bertekstur untuk
     area pajangan) atau 'polos' (teks + garis bawah, untuk permukaan
     kerja seperti Finder — tekstur tidak di belakang area kerja,
     DESIGN.md §12). Slot = teks pengantar, boleh berisi HTML. --}}
@if ($varian === 'polos')
    <div class="border-b border-line pb-5">
        <h1 class="text-3xl font-bold tracking-tight">{{ $judul }}</h1>
        @if ($slot->isNotEmpty())
            <p class="mt-3 max-w-2xl leading-7 text-ink-2">{{ $slot }}</p>
        @endif
    </div>
@else
    <div class="rounded-2xl {{ $varian === 'titik' ? 'bg-titik' : 'bg-kontur' }} p-6 sm:p-8">
        <div class="max-w-2xl">
            <h1 class="text-3xl font-bold tracking-tight">{{ $judul }}</h1>
            @if ($slot->isNotEmpty())
                <p class="mt-3 leading-7 text-ink-2">{{ $slot }}</p>
            @endif
        </div>
    </div>
@endif
