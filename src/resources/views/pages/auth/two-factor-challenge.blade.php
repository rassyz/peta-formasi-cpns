<x-layouts::auth :title="'Autentikasi dua faktor'">
    <div class="flex flex-col gap-6">
        <div
            class="relative w-full h-auto"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                focusOtp() {
                    this.$nextTick(() => this.$refs.otp?.querySelector('input')?.focus());
                },
                init() {
                    if (! this.showRecoveryInput) {
                        this.focusOtp();
                    }
                },
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : this.focusOtp();
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    title="Kode autentikasi"
                    description="Masukkan kode autentikasi dari aplikasi autentikatormu."
                />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header
                    title="Kode pemulihan"
                    description="Konfirmasi akses ke akunmu dengan memasukkan salah satu kode pemulihan daruratmu."
                />
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <div class="space-y-5 text-center">
                    <div x-show="!showRecoveryInput">
                        <div class="flex items-center justify-center my-5" x-ref="otp">
                            <flux:otp
                                x-model="code"
                                length="6"
                                name="code"
                                label="Kode OTP"
                                label:sr-only
                                class="mx-auto"
                             />
                        </div>
                    </div>

                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <input
                                type="text"
                                name="recovery_code"
                                x-ref="recovery_code"
                                x-bind:required="showRecoveryInput"
                                autocomplete="one-time-code"
                                x-model="recovery_code"
                                class="h-11 w-full rounded-lg border bg-surface px-3.5 text-center text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 {{ $errors->has('recovery_code') ? 'border-danger' : 'border-line-strong' }}"
                            />
                        </div>

                        @error('recovery_code')
                            <p class="text-sm text-danger">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <x-ui.button
                        variant="primary"
                        type="submit"
                        class="w-full"
                    >
                        Lanjutkan
                    </x-ui.button>
                </div>

                <div class="mt-5 space-x-0.5 text-sm leading-5 text-center">
                    <span class="text-ink-3">{{ __('atau kamu bisa') }}</span>
                    <div class="inline font-medium underline cursor-pointer text-accent">
                        <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('masuk dengan kode pemulihan') }}</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('masuk dengan kode autentikasi') }}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>
