<x-layouts.guest title="Pendaftaran Anggota">
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
            <h1 class="text-[1.125rem] font-semibold text-tinta">Pendaftaran anggota</h1>
            <p class="mt-1 text-[0.8125rem] text-tinta-lembut">
                Akun yang dibuat di sini langsung bisa dipakai untuk memesan buku.
                Nomor anggota dibuat otomatis setelah pendaftaran.
            </p>

            <form method="POST" action="{{ route('register') }}" class="mt-5 space-y-4">
                @csrf

                <x-form-input nama="name" label="Nama lengkap" wajib autocomplete="name" />

                <x-form-input
                    nama="email"
                    label="Surel"
                    tipe="email"
                    wajib
                    autocomplete="email"
                    placeholder="nama@perpustakaan.id"
                />

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form-input
                        nama="nomor_anggota"
                        label="Nomor anggota"
                        bantuan="Kosongkan bila belum punya nomor dari petugas."
                    />

                    <x-form-input nama="no_hp" label="Nomor telepon" placeholder="08xxxxxxxxxx" />
                </div>

                <x-form-input
                    nama="program_studi"
                    label="Program studi"
                    placeholder="Contoh: Teknik Informatika"
                />

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form-input
                        nama="password"
                        label="Kata sandi"
                        tipe="password"
                        wajib
                        autocomplete="new-password"
                        bantuan="Minimal 8 karakter."
                    />

                    <x-form-input
                        nama="password_confirmation"
                        label="Ulangi kata sandi"
                        tipe="password"
                        wajib
                        autocomplete="new-password"
                    />
                </div>

                <x-button type="submit" class="w-full">Buat akun anggota</x-button>
            </form>
        </div>

        <div class="border-t border-garis bg-permukaan-lembut px-5 py-3.5 text-[0.8125rem] text-tinta-lembut">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-inti underline underline-offset-2">Masuk ke sistem</a>
        </div>
    </div>
</x-layouts.guest>
