<x-layouts.app>
    <x-page-heading
        judul="Profil saya"
        :deskripsi="'Data yang tersimpan di sini dipakai petugas saat memproses peminjaman dan menghubungi kamu lewat surel.'"
    />


    <div class="grid gap-5 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-panel judul="Data akun" subjudul="Nama dan surel dipakai untuk masuk dan menghubungi kamu.">
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <x-form-input
                        nama="name"
                        label="Nama lengkap"
                        wajib
                        autocomplete="name"
                        :nilai="$user->name"
                    />

                    <x-form-input
                        nama="email"
                        label="Surel"
                        tipe="email"
                        wajib
                        autocomplete="email"
                        :nilai="$user->email"
                        bantuan="Surel ini dipakai untuk masuk ke sistem."
                    />

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-form-input
                            nama="no_hp"
                            label="Nomor telepon"
                            placeholder="08xxxxxxxxxx"
                            :nilai="$user->no_hp"
                            bantuan="Hanya angka, spasi, dan tanda hubung."
                        />

                        <x-form-input
                            nama="program_studi"
                            label="Program studi"
                            placeholder="Contoh: Teknik Informatika"
                            :nilai="$user->program_studi"
                        />
                    </div>

                    <x-form-textarea
                        nama="alamat"
                        label="Alamat"
                        :baris="3"
                        :nilai="$user->alamat"
                        placeholder="Alamat lengkap sesuai identitas."
                    />
                </form>

                <form method="POST" action="{{ route('profile.update') }}" class="mt-5 space-y-4 border-t border-garis pt-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <h3 class="text-[0.875rem] font-semibold">Kata sandi</h3>
                        <p class="mt-0.5 text-[0.8125rem] text-tinta-lembut">
                            Kosongkan kedua kolom kalau tidak ingin mengganti kata sandi.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-form-input
                            nama="password"
                            label="Kata sandi baru"
                            tipe="password"
                            autocomplete="new-password"
                            bantuan="Minimal 8 karakter."
                        />

                        <x-form-input
                            nama="password_confirmation"
                            label="Ulangi kata sandi baru"
                            tipe="password"
                            autocomplete="new-password"
                        />
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2">
                        <x-button :href="route('dashboard')" varian="garis">Batal</x-button>
                        <x-button type="submit">Simpan profil</x-button>
                    </div>
                </form>
            </x-panel>
        </div>

        <aside class="lg:col-span-1">
            <div class="sticky top-20 space-y-4">
                <x-panel judul="Ringkasan akun" subjudul="Bagian ini hanya bisa diubah petugas." padat>
                    <dl class="divide-y divide-garis text-[0.8125rem]">
                        <div class="flex items-center justify-between gap-2 px-4 py-2.5">
                            <dt class="text-tinta-lembut">Peran</dt>
                            <dd>
                                <x-status-tag
                                    :label="$user->role->label()"
                                    :warna="match ($user->role->value) {
                                        'admin' => 'inti',
                                        'petugas' => 'aksen',
                                        default => 'netral',
                                    }"
                                />
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-2 px-4 py-2.5">
                            <dt class="text-tinta-lembut">Nomor anggota</dt>
                            <dd class="font-mono text-[0.75rem]">{{ $user->nomor_anggota ?? '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-2 px-4 py-2.5">
                            <dt class="text-tinta-lembut">Status</dt>
                            <dd>
                                <x-status-tag
                                    :label="$user->status === 'aktif' ? 'Aktif' : 'Nonaktif'"
                                    :warna="$user->status === 'aktif' ? 'inti' : 'bahaya'"
                                />
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-2 px-4 py-2.5">
                            <dt class="text-tinta-lembut">Bergabung sejak</dt>
                            <dd class="text-[0.8125rem]">{{ $user->created_at->translatedFormat('d M Y') }}</dd>
                        </div>
                    </dl>

                    <div class="px-4 py-3">
                        <p class="text-[0.75rem] text-tinta-samar">
                            Peran, nomor anggota, dan status akun hanya bisa diubah petugas atau administrator.
                        </p>
                    </div>
                </x-panel>
            </div>
        </aside>
    </div>
</x-layouts.app>
