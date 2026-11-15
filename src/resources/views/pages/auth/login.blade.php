<x-layouts::auth :title="'Masuk'">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Masuk ke akunmu" description="Masukkan email dan kata sandimu untuk masuk" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email -->
            <x-ui.input
                name="email"
                label="Email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Kata sandi -->
            <div x-data="{ tampil: false }">
                <div class="mb-1.5 flex items-baseline justify-between gap-3">
                    <label for="password" class="block text-sm font-medium">Kata sandi</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate class="text-sm font-medium text-accent hover:text-accent-hover hover:underline">Lupa kata sandi?</a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" name="password" type="password" :type="tampil ? 'text' : 'password'" required autocomplete="current-password" placeholder="Kata sandi"
                        class="h-11 w-full rounded-lg border bg-surface px-3.5 pr-11 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 {{ $errors->has('password') ? 'border-danger' : 'border-line-strong' }}">
                    <button type="button" @click="tampil = ! tampil" :aria-label="tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-ink-3 transition-colors hover:text-ink"><svg x-show="! tampil" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg><svg x-show="tampil" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></button>
                </div>
                @error('password') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>

            <!-- Ingat saya -->
            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-2">
                <input type="checkbox" name="remember" @checked(old('remember')) class="h-4 w-4 rounded border-line-strong accent-ink">
                Ingat saya
            </label>

            <x-ui.button variant="primary" type="submit" class="w-full" data-test="login-button">
                Masuk
            </x-ui.button>
        </form>

        {{-- @chisel-registration --}}
        <p class="text-center text-sm text-ink-2">
            <span>Belum punya akun?</span>
            <a href="{{ route('register') }}" wire:navigate class="font-medium text-accent hover:text-accent-hover hover:underline">Daftar</a>
        </p>
        {{-- @end-chisel-registration --}}
    </div>
</x-layouts::auth>
