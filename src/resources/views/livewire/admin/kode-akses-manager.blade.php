<div>
    <h1 class="text-2xl font-bold tracking-tight">Kode Akses</h1>
    <p class="mt-1.5 max-w-2xl text-[15px] text-ink-2">
        Buat batch kode untuk dijual lewat Lynk.id, pantau mana yang sudah terpakai, dan cabut kode yang belum terpakai bila perlu.
    </p>

    {{-- Form buat batch --}}
    <form wire:submit="buatBatch" class="mt-6 rounded-2xl border border-line bg-surface p-5 sm:p-6">
        <h2 class="font-semibold">Buat batch baru</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="ka-musim" class="mb-1.5 block text-sm font-medium">Musim</label>
                <select id="ka-musim" wire:model="musimId" class="h-11 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    @foreach ($musimList as $m)
                        <option value="{{ $m->id }}">{{ $m->label }} · {{ ucfirst($m->status) }}</option>
                    @endforeach
                </select>
                @error('musimId') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="ka-tipe" class="mb-1.5 block text-sm font-medium">Tipe pass</label>
                <select id="ka-tipe" wire:model="tipe" class="h-11 w-full rounded-lg border border-line-strong bg-surface px-2.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    <option value="historis">Pass Peta Historis</option>
                    <option value="strategi">Pass Strategi</option>
                </select>
                @error('tipe') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="ka-jumlah" class="mb-1.5 block text-sm font-medium">Jumlah kode</label>
                <input id="ka-jumlah" type="number" min="1" max="200" wire:model="jumlah"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                @error('jumlah') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="ka-batch" class="mb-1.5 block text-sm font-medium">Label batch <span class="font-normal text-ink-3">(opsional)</span></label>
                <input id="ka-batch" type="text" wire:model="batch" placeholder="Mis. lynk-nov-1"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                @error('batch') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
        </div>
        <button type="submit" wire:loading.attr="disabled"
            class="mt-4 inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800 disabled:opacity-60">
            <span wire:loading.remove wire:target="buatBatch">Buat Kode</span>
            <span wire:loading wire:target="buatBatch">Membuat…</span>
        </button>
    </form>

    {{-- Daftar kode --}}
    <div class="mt-6 flex items-center justify-between gap-3">
        <h2 class="font-semibold">Daftar kode</h2>
        <select wire:model.live="saringStatus" class="h-9 rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30" aria-label="Saring status">
            <option value="">Semua status</option>
            <option value="belum_dipakai">Belum dipakai</option>
            <option value="terpakai">Terpakai</option>
            <option value="dicabut">Dicabut</option>
        </select>
    </div>

    <div class="mt-3 overflow-x-auto rounded-2xl border border-line bg-surface">
        <table class="w-full min-w-180 text-left text-sm">
            <thead>
                <tr class="border-b border-line text-ink-2">
                    <th class="px-4 py-3 font-medium">Kode</th>
                    <th class="px-4 py-3 font-medium">Musim · Tipe</th>
                    <th class="px-4 py-3 font-medium">Batch</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Dipakai oleh</th>
                    <th class="px-4 py-3 font-medium"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftar as $kode)
                    <tr class="border-b border-line last:border-b-0">
                        <td class="px-4 py-3 font-mono text-[13px]">{{ $kode->kode }}</td>
                        <td class="px-4 py-3">{{ $kode->musim->label }} · {{ $kode->tipe_pass === 'strategi' ? 'Strategi' : 'Historis' }}</td>
                        <td class="px-4 py-3 text-ink-2">{{ $kode->batch ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @switch($kode->status)
                                @case('terpakai') <x-ui.badge variant="success">Terpakai</x-ui.badge> @break
                                @case('dicabut') <x-ui.badge variant="danger">Dicabut</x-ui.badge> @break
                                @default <x-ui.badge variant="neutral">Belum dipakai</x-ui.badge>
                            @endswitch
                        </td>
                        <td class="px-4 py-3 text-ink-2">
                            @if ($kode->user)
                                {{ $kode->user->email }}<br>
                                <span class="tnum text-[13px]">{{ $kode->dipakai_pada?->translatedFormat('d M Y H:i') }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($kode->status === 'belum_dipakai')
                                <button type="button" wire:click="mintaKonfirmasi('cabut', [{{ $kode->id }}], 'Cabut kode akses?', 'Cabut kode {{ $kode->kode }}? Kode tidak bisa di-redeem lagi.', true, 'Ya, cabut kode')"
                                    class="inline-flex h-8 items-center rounded-lg border border-danger/40 px-3 text-[13px] font-medium text-danger transition-colors hover:border-danger/60 hover:bg-danger-soft">Cabut</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-ink-2">Belum ada kode dengan status ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $daftar->links() }}</div>

    @include('livewire.admin._modal-konfirmasi')
</div>
