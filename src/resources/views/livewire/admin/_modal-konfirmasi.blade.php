{{-- Modal konfirmasi bersama admin (trait App\Livewire\Admin\Concerns\KonfirmasiAksi).
     Dirender di dalam root komponen Livewire; tampil bila $dialogKonfirmasi terisi.
     Batal: tombol Batal, klik backdrop, atau tombol Escape. --}}
@if ($dialogKonfirmasi)
    <div x-data @keydown.escape.window="$wire.batalKonfirmasi()"
         class="fixed inset-0 z-110 flex items-end justify-center p-3 sm:items-center sm:p-6"
         role="dialog" aria-modal="true" aria-label="{{ $dialogKonfirmasi['judul'] }}">
        <button type="button" wire:click="batalKonfirmasi" aria-label="Batal" class="absolute inset-0 bg-ink/50"></button>
        <div class="relative w-full max-w-md rounded-2xl bg-surface p-6 shadow-float">
            <div class="flex items-start gap-3.5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $dialogKonfirmasi['bahaya'] ? 'bg-danger-soft text-danger' : 'bg-warning-soft text-warning' }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3ZM12 9v4M12 17h.01"/></svg>
                </span>
                <div class="min-w-0">
                    <h2 class="font-semibold leading-snug">{{ $dialogKonfirmasi['judul'] }}</h2>
                    <p class="mt-1.5 text-sm leading-6 text-ink-2">{{ $dialogKonfirmasi['pesan'] }}</p>
                </div>
            </div>
            <div class="mt-6 flex gap-2">
                <button type="button" wire:click="batalKonfirmasi"
                    class="inline-flex h-10 flex-1 items-center justify-center rounded-lg border border-line-strong px-4 text-sm font-medium transition-colors hover:bg-sunken">
                    Batal
                </button>
                <button type="button" wire:click="jalankanKonfirmasi"
                    class="inline-flex h-10 flex-1 items-center justify-center rounded-lg px-4 text-sm font-medium text-white transition-colors {{ $dialogKonfirmasi['bahaya'] ? 'bg-danger hover:bg-red-800' : 'bg-ink hover:bg-stone-800' }}">
                    {{ $dialogKonfirmasi['label'] }}
                </button>
            </div>
        </div>
    </div>
@endif
