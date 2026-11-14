<section class="rounded-2xl border border-line bg-surface p-5 sm:p-6" aria-labelledby="judul-request">
    <h2 id="judul-request" class="text-lg font-semibold tracking-tight">Tidak menemukan formasimu?</h2>
    <p class="mt-1 text-sm leading-6 text-ink-2">
        Kirim request instansi + jabatan yang kamu cari. Request terbanyak diprioritaskan untuk didata berikutnya — tanpa perlu akun.
    </p>

    @if ($terkirim)
        <p class="mt-4 rounded-lg bg-success-soft px-3.5 py-2.5 text-sm text-success">
            Request terkirim. Terima kasih — antreannya kami baca berkala dan yang terbanyak didahulukan.
        </p>
        <button type="button" wire:click="$set('terkirim', false)" class="mt-3 text-sm font-medium text-accent hover:underline">
            Kirim request lain
        </button>
    @else
        <form wire:submit="simpan" class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="req-instansi" class="mb-1.5 block text-sm font-medium">Instansi yang dicari</label>
                <input id="req-instansi" type="text" wire:model="instansiDicari" placeholder="Mis. Kementerian Keuangan"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                @error('instansiDicari') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="req-jabatan" class="mb-1.5 block text-sm font-medium">Jabatan yang dicari</label>
                <input id="req-jabatan" type="text" wire:model="jabatanDicari" placeholder="Mis. Pranata Komputer"
                    class="h-11 w-full rounded-lg border border-line-strong bg-surface px-3.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30">
                @error('jabatanDicari') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <label for="req-catatan" class="mb-1.5 block text-sm font-medium">Catatan <span class="font-normal text-ink-3">(opsional)</span></label>
                <textarea id="req-catatan" wire:model="catatan" rows="2" placeholder="Penempatan atau hal lain yang kamu harapkan"
                    class="w-full rounded-lg border border-line-strong bg-surface px-3.5 py-2.5 text-[15px] focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30"></textarea>
                @error('catatan') <p class="mt-1.5 text-[13px] text-danger">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex h-11 items-center rounded-lg bg-ink px-5 text-sm font-medium text-white transition-colors hover:bg-stone-800 disabled:opacity-60">
                    <span wire:loading.remove wire:target="simpan">Kirim Request</span>
                    <span wire:loading wire:target="simpan">Mengirim…</span>
                </button>
            </div>
        </form>
    @endif
</section>
