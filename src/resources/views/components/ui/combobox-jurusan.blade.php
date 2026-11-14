@props([
    'name' => 'jurusan',
    'label' => null,
    'placeholder' => 'Ketik nama jurusan kamu…',
    'items' => [], // [['value' => 'teknik-informatika', 'label' => 'Teknik Informatika', 'jenjang' => 'S1'], ...]
    'size' => 'md', // md | lg
])

@php
    $height = $size === 'lg' ? 'h-[52px] text-base' : 'h-11 text-[15px]';
@endphp

<div
    x-data="{
        open: false,
        query: '',
        value: '',
        items: {{ Illuminate\Support\Js::from($items) }},
        get filtered() {
            const q = this.query.trim().toLowerCase();
            if (!q) return this.items;
            return this.items.filter(i => i.label.toLowerCase().includes(q));
        },
        pilih(item) {
            this.value = item.value;
            this.query = item.label;
            this.open = false;
        }
    }"
    class="relative"
    @click.away="open = false"
    @keydown.escape="open = false"
>
    @if ($label)
        <label for="{{ $name }}_input" class="mb-1.5 block text-sm font-medium text-ink">{{ $label }}</label>
    @endif

    <input type="hidden" name="{{ $name }}" :value="value">

    <div class="relative">
        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-ink-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
            <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
        </svg>
        <input
            id="{{ $name }}_input"
            type="text"
            x-model="query"
            @focus="open = true"
            @input="open = true; value = ''"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            class="{{ $height }} w-full rounded-lg border border-line-strong bg-surface pl-11 pr-10 text-ink placeholder:text-ink-3 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent"
            role="combobox"
            aria-expanded="false"
            :aria-expanded="open"
        >
        <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-3 transition-transform" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </div>

    <ul
        x-show="open"
        x-cloak
        class="absolute inset-x-0 top-full z-30 mt-2 max-h-72 overflow-auto rounded-xl border border-line bg-surface py-1.5 shadow-float"
        role="listbox"
    >
        <template x-for="item in filtered" :key="item.value">
            <li>
                <button type="button" @click="pilih(item)"
                    class="flex w-full items-center justify-between gap-3 px-3.5 py-2.5 text-left text-[15px] hover:bg-sunken"
                    role="option">
                    <span x-text="item.label"></span>
                    <span class="text-xs text-ink-2" x-text="item.jenjang"></span>
                </button>
            </li>
        </template>
        <li x-show="filtered.length === 0" class="px-3.5 py-3 text-sm text-ink-2">
            Jurusan tidak ditemukan. Coba kata kunci lain.
        </li>
    </ul>
</div>
