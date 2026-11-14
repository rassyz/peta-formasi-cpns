<div>
    <h1 class="text-2xl font-bold tracking-tight">Peserta Terdaftar</h1>
    <p class="mt-1.5 max-w-2xl text-[15px] text-ink-2">
        Telusur akun pembeli dari nama atau email untuk melihat pass yang dipegang dan kode yang
        pernah di-redeem — halaman dukungan ini <span class="font-medium text-ink">hanya membaca</span>,
        tidak ada tombol ubah atau hapus.
    </p>

    {{-- Saringan --}}
    <div class="mt-6 flex flex-wrap items-center gap-2.5">
        <input type="search" wire:model.live.debounce.300ms="cari" placeholder="Cari nama atau email…"
            class="h-10 w-full max-w-sm rounded-lg border border-line-strong bg-surface px-3.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30"
            aria-label="Cari peserta">
        <select wire:model.live="saringPass" class="h-10 rounded-lg border border-line-strong bg-surface px-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30" aria-label="Saring kepemilikan pass">
            <option value="">Semua peserta</option>
            <option value="aktif">Punya pass aktif</option>
            <option value="tanpa">Belum punya pass</option>
        </select>
        <p class="tnum ml-auto text-sm text-ink-2">{{ number_format($pengguna->total(), 0, ',', '.') }} peserta</p>
    </div>

    {{-- Daftar peserta --}}
    <div class="mt-4 overflow-hidden rounded-xl border border-line bg-surface">
        @forelse ($pengguna as $u)
            <div class="border-b border-line last:border-b-0" wire:key="peserta-{{ $u->id }}">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 px-5 py-4">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sunken text-sm font-bold text-ink-2" aria-hidden="true">
                        {{ strtoupper(substr($u->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate font-medium">{{ $u->name }}</p>
                        <p class="truncate text-sm text-ink-2">{{ $u->email }}</p>
                    </div>
                    <div class="ml-auto flex items-center gap-4">
                        <p class="hidden text-sm text-ink-3 sm:block">
                            Daftar {{ $u->created_at?->translatedFormat('d M Y') }}
                        </p>
                        <p class="tnum text-sm text-ink-2">
                            {{ $u->passes_count }} pass
                        </p>
                        <button type="button" wire:click="toggleDetail({{ $u->id }})"
                            class="inline-flex h-9 items-center rounded-lg border border-line-strong px-3.5 text-sm font-medium transition-colors hover:bg-sunken">
                            {{ $terbukaId === $u->id ? 'Tutup' : 'Detail' }}
                        </button>
                    </div>
                </div>

                @if ($terbukaId === $u->id)
                    <div class="border-t border-line bg-canvas px-5 py-4">
                        @forelse ($u->passes as $pass)
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 border-b border-line py-3 first:pt-0 last:border-b-0 last:pb-0">
                                @if ($pass->tipe_pass === 'strategi')
                                    <x-ui.badge variant="accent">Pass Strategi</x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral">Pass Peta Historis</x-ui.badge>
                                @endif
                                <p class="font-medium">{{ $pass->musim?->label ?? '—' }}</p>
                                @if ($pass->status !== 'aktif')
                                    <x-ui.badge variant="danger">{{ ucfirst($pass->status) }}</x-ui.badge>
                                @elseif ($pass->isAktif())
                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning">Berakhir</x-ui.badge>
                                @endif
                                <p class="w-full text-sm text-ink-2">
                                    Kode <span class="font-mono text-[13px] text-ink">{{ $pass->kodeAkses?->kode ?? '—' }}</span>
                                    · aktif sejak {{ $pass->aktif_dari?->translatedFormat('d F Y') }}
                                    · {{ $pass->aktif_hingga ? 'berlaku sampai ' . $pass->aktif_hingga->translatedFormat('d F Y') : 'tanpa batas tanggal' }}
                                </p>
                            </div>
                        @empty
                            <p class="text-sm text-ink-2">Belum memiliki pass — akun ini belum pernah redeem kode.</p>
                        @endforelse
                    </div>
                @endif
            </div>
        @empty
            <p class="px-5 py-10 text-center text-ink-2">Tidak ada peserta yang cocok dengan pencarian/saringan ini.</p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $pengguna->links() }}
    </div>
</div>
