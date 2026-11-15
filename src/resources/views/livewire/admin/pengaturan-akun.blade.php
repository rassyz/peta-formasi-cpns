<div>
    <div>
        <h1 class="text-2xl font-semibold tracking-tight">Pengaturan Akun</h1>
        <p class="mt-1 text-ink-2">Profil dan kata sandi akun yang sedang kamu pakai untuk masuk panel admin.</p>
    </div>

    <div class="mt-6 grid max-w-2xl gap-6">
        <form wire:submit="simpanProfil" class="rounded-xl border border-line bg-surface p-5">
            <h2 class="text-base font-semibold tracking-tight">Profil</h2>
            <p class="mt-1 text-sm text-ink-2">Nama tampil dan email login akun ini.</p>

            <div class="mt-4 grid gap-4">
                <div>
                    <label for="pengaturan-nama" class="mb-1.5 block text-sm font-medium">Nama</label>
                    <input id="pengaturan-nama" type="text" wire:model="nama" autocomplete="name"
                        class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    @error('nama') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="pengaturan-email" class="mb-1.5 block text-sm font-medium">Email</label>
                    <input id="pengaturan-email" type="email" wire:model="email" autocomplete="email"
                        class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                    <p class="mt-1.5 text-[13px] leading-5 text-ink-3">Email ini dipakai untuk masuk. Menggantinya mengubah alamat loginmu.</p>
                    @error('email') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <button type="submit"
                class="mt-5 inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800">
                Simpan Profil
            </button>
        </form>

        <form wire:submit="simpanSandi" class="rounded-xl border border-line bg-surface p-5">
            <h2 class="text-base font-semibold tracking-tight">Kata Sandi</h2>
            <p class="mt-1 text-sm text-ink-2">Perbarui kata sandi login. Sesi ini tetap masuk; kata sandi baru berlaku pada login berikutnya.</p>

            <div class="mt-4 grid gap-4">
                <div x-data="{ tampil: false }">
                    <label for="pengaturan-sandi-sekarang" class="mb-1.5 block text-sm font-medium">Kata sandi sekarang</label>
                    <div class="relative">
                        <input id="pengaturan-sandi-sekarang" type="password" :type="tampil ? 'text' : 'password'" wire:model="sandiSekarang" autocomplete="current-password"
                            class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 pr-11 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                        <button type="button" @click="tampil = ! tampil" :aria-label="tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-ink-3 transition-colors hover:text-ink"><svg x-show="! tampil" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg><svg x-show="tampil" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></button>
                    </div>
                    @error('sandiSekarang') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                </div>
                <div x-data="{ tampil: false }">
                    <label for="pengaturan-sandi-baru" class="mb-1.5 block text-sm font-medium">Kata sandi baru</label>
                    <div class="relative">
                        <input id="pengaturan-sandi-baru" type="password" :type="tampil ? 'text' : 'password'" wire:model="sandiBaru" autocomplete="new-password"
                            class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 pr-11 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                        <button type="button" @click="tampil = ! tampil" :aria-label="tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-ink-3 transition-colors hover:text-ink"><svg x-show="! tampil" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg><svg x-show="tampil" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></button>
                    </div>
                    <p class="mt-1.5 text-[13px] leading-5 text-ink-3">Minimal 8 karakter.</p>
                    @error('sandiBaru') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                </div>
                <div x-data="{ tampil: false }">
                    <label for="pengaturan-sandi-konfirmasi" class="mb-1.5 block text-sm font-medium">Konfirmasi kata sandi baru</label>
                    <div class="relative">
                        <input id="pengaturan-sandi-konfirmasi" type="password" :type="tampil ? 'text' : 'password'" wire:model="sandiBaruKonfirmasi" autocomplete="new-password"
                            class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 pr-11 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                        <button type="button" @click="tampil = ! tampil" :aria-label="tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-ink-3 transition-colors hover:text-ink"><svg x-show="! tampil" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg><svg x-show="tampil" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></button>
                    </div>
                    @error('sandiBaruKonfirmasi') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <button type="submit"
                class="mt-5 inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800">
                Perbarui Kata Sandi
            </button>
        </form>
    </div>
</div>
