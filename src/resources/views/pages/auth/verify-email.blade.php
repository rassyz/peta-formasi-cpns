<x-layouts::auth :title="'Verifikasi email'">
    <div class="flex flex-col gap-6">
        <p class="text-center text-[15px] leading-7 text-ink-2">
            Verifikasi alamat emailmu dengan mengeklik tautan yang baru saja kami kirim ke emailmu.
        </p>

        @if (session('status') == 'verification-link-sent')
            <p class="text-center text-[15px] font-medium leading-7 text-success">
                Tautan verifikasi baru telah dikirim ke alamat email yang kamu daftarkan.
            </p>
        @endif

        <div class="flex flex-col items-center gap-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <x-ui.button variant="primary" type="submit" class="w-full">
                    Kirim ulang email verifikasi
                </x-ui.button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-ui.button variant="ghost" type="submit" data-test="logout-button">
                    Keluar
                </x-ui.button>
            </form>
        </div>
    </div>
</x-layouts::auth>
