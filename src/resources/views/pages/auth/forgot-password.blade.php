<x-layouts::auth :title="'Lupa kata sandi'">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Lupa kata sandi" description="Masukkan emailmu untuk menerima tautan atur ulang kata sandi" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email -->
            <x-ui.input
                name="email"
                label="Email"
                type="email"
                required
                autofocus
                placeholder="email@example.com"
            />

            <x-ui.button variant="primary" type="submit" class="w-full" data-test="email-password-reset-link-button">
                Kirim tautan atur ulang
            </x-ui.button>
        </form>

        <p class="text-center text-sm text-ink-2">
            <span>Atau, kembali ke</span>
            <a href="{{ route('login') }}" wire:navigate class="font-medium text-accent hover:text-accent-hover hover:underline">masuk</a>
        </p>
    </div>
</x-layouts::auth>
