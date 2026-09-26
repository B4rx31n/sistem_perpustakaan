@props(['nilai', 'label' => 'Nomor halaman', 'halaman' => 20])

<form method="GET" class="flex items-center gap-2">
    @foreach (request()->except(['page', 'halaman']) as $kunci => $nilaiFilter)
        @if (filled($nilaiFilter))
            <input type="hidden" name="{{ $kunci }}" value="{{ $nilaiFilter }}">
        @endif
    @endforeach

    <label for="jumlah-baris" class="text-[0.75rem] text-tinta-samar">{{ $label }}</label>
    <select id="jumlah-baris"
            name="halaman"
            onchange="this.form.requestSubmit()"
            class="rounded-lg border border-garis bg-permukaan px-2.5 py-1.5 text-[0.75rem] text-tinta shadow-halus transition-colors duration-150 focus:border-inti focus:shadow-fokus focus:outline-none">
        @foreach ([20 => '20', 50 => '50', 100 => '100'] as $pilihan => $teks)
            <option value="{{ $pilihan }}" @selected((int) request('halaman', $halaman) === $pilihan)>
                {{ $teks }}
            </option>
        @endforeach
    </select>
</form>
