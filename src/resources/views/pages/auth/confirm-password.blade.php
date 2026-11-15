<x-layouts::auth :title="'Konfirmasi kata sandi'">
    <div class="flex flex-col gap-6">
        <x-auth-header
            title="Konfirmasi kata sandi"
            description="Ini area aman aplikasi. Konfirmasi kata sandimu sebelum melanjutkan."
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Kata sandi -->
            <div x-data="{ tampil: false }">
                <label for="password" class="mb-1.5 block text-sm font-medium">Kata sandi</label>
                <div class="relative">
                    <input id="password" name="password" type="password" :type="tampil ? 'text' : 'password'" required autocomplete="current-password" placeholder="Kata sandi"
                        class="h-11 w-full rounded-lg border bg-surface px-3.5 pr-11 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 {{ $errors->has('password') ? 'border-danger' : 'border-line-strong' }}">
                    <button type="button" @click="tampil = ! tampil" :aria-label="tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-ink-3 transition-colors hover:text-ink"><svg x-show="! tampil" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg><svg x-show="tampil" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></button>
                </div>
                @error('password') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>

            <x-ui.button variant="primary" type="submit" class="w-full" data-test="confirm-password-button">
                Konfirmasi
            </x-ui.button>
        </form>
    </div>
</x-layouts::auth>
