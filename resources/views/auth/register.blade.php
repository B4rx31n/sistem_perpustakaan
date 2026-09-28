<x-layouts.guest title="Pendaftaran Anggota">
    <div class="kartu overflow-hidden">
        <x-auth-kepala
            judul="Pendaftaran anggota"
            deskripsi="Akun yang dibuat di sini langsung bisa dipakai untuk memesan buku. Nomor anggota dibuat otomatis setelah pendaftaran."
        />

        <div class="px-5 py-5">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
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

        <div class="border-t border-garis bg-pastel-1/50 px-5 py-3.5 text-[0.8125rem] text-tinta-lembut">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-inti underline underline-offset-2">Masuk ke sistem</a>
        </div>
    </div>
</x-layouts.guest>
