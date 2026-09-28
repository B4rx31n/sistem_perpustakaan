<x-layouts.guest title="Masuk ke Sistem Perpustakaan">
    <div class="kartu overflow-hidden">
        <x-auth-kepala
            judul="Masuk ke sistem"
            deskripsi="Gunakan surel dan kata sandi yang terdaftar di loket perpustakaan."
        />

        <div class="px-5 py-5">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
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

        <div class="border-t border-garis bg-pastel-1/50 px-5 py-3.5 text-[0.8125rem] text-tinta-lembut">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-medium text-inti underline underline-offset-2">Daftar sebagai anggota</a>
        </div>
    </div>

    {{-- Kotak kredensial contoh. Warnanya apricot lembut supaya terbaca sebagai
         catatan bantu, bukan bagian dari formulir, dan tidak pernah disalahartikan
         sebagai kredensial yang sudah dipakai. --}}
    <div class="mt-5 rounded-xl border border-pastel-aksen-2 bg-pastel-aksen-1/50 px-4 py-3.5">
        <p class="flex items-center gap-2 text-[0.75rem] font-semibold text-tinta-lembut">
            <span aria-hidden="true" class="text-aksen"><x-icon nama="rak" ukuran="kecil" /></span>
            Akun contoh
        </p>
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
