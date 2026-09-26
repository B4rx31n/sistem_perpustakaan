<x-layouts.app>
    @php
        /** @var \App\Models\User $member */
        /** @var \App\Enums\UserRole $peran */
        $roleOpsi = collect($peran)->mapWithKeys(fn ($item) => [$item->value => $item->label()]);
        $statusOpsi = ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'];

        $bolehUbahPeran = auth()->user()->can('ubahPeran', $member);
        $bolehUbahStatus = auth()->user()->can('ubahStatus', $member);
    @endphp

    <x-page-heading
        judul="Ubah data anggota"
        :deskripsi="$member->nomor_anggota ?? $member->email"
    >
        <x-button :href="route('members.show', $member)" varian="garis" ukuran="kecil">Kembali ke kartu</x-button>
    </x-page-heading>


    <form method="POST" action="{{ route('members.update', $member) }}" class="grid gap-5 lg:grid-cols-3">
        @csrf
        @method('PUT')

        <div class="space-y-5 lg:col-span-2">
            <section aria-labelledby="identitas-anggota" class="kartu overflow-hidden">
                <h2 id="identitas-anggota" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Identitas
                </h2>

                <div class="grid gap-4 px-4 py-4 sm:grid-cols-2">
                    <x-form-input nama="name" label="Nama lengkap" wajib :nilai="old('name', $member->name)" />
                    <x-form-input
                        nama="email"
                        label="Surel"
                        tipe="email"
                        wajib
                        :nilai="old('email', $member->email)"
                    />
                    <x-form-input
                        nama="nomor_anggota"
                        label="Nomor anggota"
                        :nilai="old('nomor_anggota', $member->nomor_anggota)"
                        bantuan="Boleh dikosongkan untuk akun petugas."
                    />
                    <x-form-input
                        nama="no_hp"
                        label="Nomor telepon"
                        :nilai="old('no_hp', $member->no_hp)"
                        placeholder="08xxxxxxxxxx"
                    />
                    <div class="sm:col-span-2">
                        <x-form-input
                            nama="program_studi"
                            label="Program studi"
                            :nilai="old('program_studi', $member->program_studi)"
                        />
                    </div>
                    <div class="sm:col-span-2">
                        <x-form-textarea
                            nama="alamat"
                            label="Alamat"
                            :nilai="old('alamat', $member->alamat)"
                            :baris="2"
                        />
                    </div>
                </div>
            </section>

            <section aria-labelledby="akses-anggota" class="kartu overflow-hidden">
                <h2 id="akses-anggota" class="border-b border-garis px-4 py-3 text-[0.875rem] font-semibold">
                    Peran dan status
                </h2>

                <div class="grid gap-4 px-4 py-4 sm:grid-cols-2">
                    @if ($bolehUbahPeran)
                        <x-form-select
                            nama="role"
                            label="Peran"
                            wajib
                            :nilai="old('role', $member->role->value)"
                            :pilihan="$roleOpsi"
                            kosong=""
                            :bantuan="$member->role->description()"
                        />
                    @else
                        <div>
                            <p class="block text-[0.8125rem] font-medium text-tinta">Peran</p>
                            <p class="mt-1 rounded-lg border border-garis bg-permukaan-lembut/70 px-3 py-2.5 text-[0.875rem] text-tinta-lembut">
                                {{ $member->role->label() }}
                            </p>
                            <p class="mt-1 text-[0.75rem] text-tinta-samar">
                                Hanya administrator yang bisa mengubah peran.
                            </p>
                            <input type="hidden" name="role" value="{{ $member->role->value }}">
                        </div>
                    @endif

                    @if ($bolehUbahStatus)
                        <x-form-select
                            nama="status"
                            label="Status akun"
                            wajib
                            :nilai="old('status', $member->status)"
                            :pilihan="$statusOpsi"
                            kosong=""
                            bantuan="Akun nonaktif tidak bisa masuk ke sistem."
                        />
                    @else
                        <div>
                            <p class="block text-[0.8125rem] font-medium text-tinta">Status akun</p>
                            <p class="mt-1 rounded-lg border border-garis bg-permukaan-lembut/70 px-3 py-2.5 text-[0.875rem] text-tinta-lembut">
                                {{ $member->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                            </p>
                            <p class="mt-1 text-[0.75rem] text-tinta-samar">
                                Hanya administrator yang bisa memblokir akun.
                            </p>
                            <input type="hidden" name="status" value="{{ $member->status }}">
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <aside class="lg:col-span-1">
            <div class="sticky top-20 border border-garis bg-permukaan p-4">
                <h2 class="text-[0.875rem] font-semibold">Simpan perubahan</h2>
                <p class="mt-1 text-[0.8125rem] text-tinta-lembut">
                    Perubahan peran dan status langsung berlaku pada permintaan berikutnya.
                </p>

                <x-button type="submit" class="mt-3 w-full">Simpan perubahan</x-button>
                <x-button :href="route('members.show', $member)" varian="garis" class="mt-2 w-full">Batal</x-button>
            </div>
        </aside>
    </form>
</x-layouts.app>
