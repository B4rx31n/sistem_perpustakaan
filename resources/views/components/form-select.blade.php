@props([
    'nama',
    'label',
    'nilai' => null,
    'pilihan' => [],
    'wajib' => false,
    'bantuan' => null,
    'kosong' => 'Pilih salah satu',
    'atribut' => [],
])

@php
    $id = $attributes->get('id') ?? $nama;
    $adaGalat = $errors->has($nama);
@endphp

<div>
    <label for="{{ $id }}" class="block text-[0.8125rem] font-medium text-tinta">
        {{ $label }}
        @if ($wajib)
            <span class="text-bahaya" aria-hidden="true">*</span>
            <span class="sr-only">(wajib diisi)</span>
        @endif
    </label>

    <select
        id="{{ $id }}"
        name="{{ $nama }}"
        @if ($adaGalat) aria-invalid="true" aria-describedby="{{ $id }}-galat" @endif
        @if ($bantuan) aria-describedby="{{ $id }}-bantuan" @endif
        {{ $attributes->except(['id'])->merge([
            'class' => 'mt-1.5 block w-full rounded-lg border bg-permukaan px-3 py-2.5 text-[0.875rem] text-tinta transition-[border-color,box-shadow] duration-150 focus:outline-none '.($adaGalat ? 'border-bahaya focus:shadow-fokus-bahaya' : 'border-garis focus:border-inti focus:shadow-fokus'),
        ]) }}
    >
        @if ($kosong)
            <option value="">{{ $kosong }}</option>
        @endif

        @foreach ($pilihan as $nilaiOpsi => $teksOpsi)
            <option value="{{ $nilaiOpsi }}"
                    @selected((string) old($nama, $nilai) === (string) $nilaiOpsi)>
                {{ $teksOpsi }}
            </option>
        @endforeach
    </select>

    @if ($bantuan && ! $adaGalat)
        <p id="{{ $id }}-bantuan" class="mt-1 text-[0.75rem] text-tinta-samar">{{ $bantuan }}</p>
    @endif

    @error($nama)
        <p id="{{ $id }}-galat" class="mt-1 text-[0.75rem] text-bahaya">{{ $message }}</p>
    @enderror
</div>
