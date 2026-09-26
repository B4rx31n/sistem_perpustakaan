@if (session('success') || session('error') || $errors->any())
    <div class="mb-5 space-y-2">
        @if (session('success'))
            <div role="status"
                 data-notifikasi
                 class="flex items-start gap-2.5 border border-inti bg-inti-lembut px-3 py-2.5 text-[0.875rem] text-inti">
                <span aria-hidden="true" class="font-mono text-xs leading-5">&check;</span>
                <p>{{ session('success') }}</p>
                <button type="button"
                        data-tutup-notifikasi
                        aria-label="Tutup pemberitahuan"
                        class="ml-auto shrink-0 px-1 leading-5 text-inti transition-opacity duration-[120ms] hover:opacity-70">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div role="alert"
                 data-notifikasi
                 class="flex items-start gap-2.5 border border-bahaya bg-bahaya-lembut px-3 py-2.5 text-[0.875rem] text-bahaya">
                <span aria-hidden="true" class="font-mono text-xs leading-5">!</span>
                <p>{{ session('error') }}</p>
                <button type="button"
                        data-tutup-notifikasi
                        aria-label="Tutup pemberitahuan"
                        class="ml-auto shrink-0 px-1 leading-5 text-bahaya transition-opacity duration-[120ms] hover:opacity-70">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div role="alert"
                 data-notifikasi
                 class="border border-bahaya bg-bahaya-lembut px-3 py-2.5 text-[0.875rem] text-bahaya">
                <p class="font-semibold">Periksa kembali isian formulir berikut:</p>
                <ul class="mt-1 list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
