<x-layouts.guest title="Masuk ke Sistem Perpustakaan">
    <div class="kartu overflow-hidden">
        <div class="border-b border-garis bg-permukaan-lembut/60 px-6 py-5">
            <div class="flex items-center gap-2.5">
                <span aria-hidden="true"
                      class="flex h-10 w-10 items-center justify-center rounded-lg bg-inti font-mono text-[0.8125rem] font-semibold text-inti-kunci shadow-halus">
                    PN
                </span>
                <div>
                    <p class="text-[0.9375rem] font-semibold tracking-tight text-tinta">Perpustakaan Nusantara</p>
                    <p class="text-[0.75rem] text-tinta-samar">Sistem Layanan</p>
                </div>
            </div>
        </div>

        <div class="px-5 py-5">
            <h1 class="text-[1.125rem] font-semibold text-tinta">Masuk ke sistem</h1>
            <p class="mt-1 text-[0.8125rem] text-tinta-lembut">
                Gunakan surel dan kata sandi yang terdaftar di loket perpustakaan.
            </p>

            <form method="POST" action="{{ route('login') }}" class="mt-5 space-y-4">
                @csrf

                <x-form-input
                    nama="email"
                    label="Surel"
                    tipe="email"
                    wajib
                    autocomplete="email"
                    placeholder="nama@perpustakaan.id"
                />

                <x-form-input
                    nama="password"
                    label="Kata sandi"
                    tipe="password"
                    wajib
                    autocomplete="current-password"
                />

                <label class="flex items-center gap-2 text-[0.8125rem] text-tinta-lembut">
                    <input type="checkbox" name="ingat" value="1" @checked(old('ingat'))
                           class="h-4 w-4 rounded border-garis text-inti transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-inti/40">
                    Ingat saya di perangkat ini
                </label>

                <x-button type="submit" class="w-full">Masuk</x-button>
            </form>
        </div>

        <div class="border-t border-garis bg-permukaan-lembut px-5 py-3.5 text-[0.8125rem] text-tinta-lembut">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-medium text-inti underline underline-offset-2">Daftar sebagai anggota</a>
        </div>
    </div>

    <div class="mt-5 rounded-xl border border-garis bg-permukaan-lembut/70 px-4 py-3.5">
        <p class="text-[0.75rem] font-semibold text-tinta-lembut">Akun contoh</p>
        <p class="mt-1 text-[0.75rem] text-tinta-samar">
            Data di bawah dibuat oleh seeder untuk mencoba sistem. Ganti kredensialnya
            sebelum dipakai di lingkungan nyata.
        </p>
        <dl class="mt-2.5 space-y-1 font-mono text-[0.75rem]">
            <div class="flex gap-2">
                <dt class="w-24 shrink-0 text-tinta-samar">Admin</dt>
                <dd>admin@perpustakaan.id</dd>
            </div>
            <div class="flex gap-2">
                <dt class="w-24 shrink-0 text-tinta-samar">Petugas</dt>
                <dd>petugas@perpustakaan.id</dd>
            </div>
            <div class="flex gap-2">
                <dt class="w-24 shrink-0 text-tinta-samar">Anggota</dt>
                <dd>anggota@perpustakaan.id</dd>
            </div>
            <div class="flex gap-2">
                <dt class="w-24 shrink-0 text-tinta-samar">Kata sandi</dt>
                <dd>password</dd>
            </div>
        </dl>
    </div>
</x-layouts.guest>
