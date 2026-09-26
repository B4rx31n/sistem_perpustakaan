<footer class="mt-8 border-t border-garis/70">
    <div class="flex w-full flex-col gap-1 px-4 py-5 text-[0.75rem] text-tinta-samar sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <p>
            Sistem Layanan Perpustakaan Nusantara
            <span class="mx-1.5 text-garis-kuat" aria-hidden="true">&middot;</span>
            Masa pinjam {{ config('perpustakaan.durasi_peminjaman_hari') }} hari
            <span class="mx-1.5 text-garis-kuat" aria-hidden="true">&middot;</span>
            Denda Rp{{ number_format(config('perpustakaan.denda_per_hari'), 0, ',', '.') }} per hari
        </p>
        <p>
            {{ now()->translatedFormat('l, d F Y H:i') }} WIB
        </p>
    </div>
</footer>
