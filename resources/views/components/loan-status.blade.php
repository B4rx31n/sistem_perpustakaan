@props(['loan', 'loanService' => null, 'ringkas' => false])

@php
    $sisaHari = $loanService?->sisaHari($loan) ?? null;
    $terlambat = $loan->isTerlambat();
    $hariTelat = $loan->hariTerlambat();
@endphp

@if ($terlambat)
    <x-status-tag label="Terlambat {{ $hariTelat }} hari" warna="bahaya" />
@elseif ($sisaHari !== null && $sisaHari <= 2)
    <x-status-tag label="Jatuh tempo {{ $sisaHari === 0 ? 'hari ini' : $sisaHari.' hari lagi' }}" warna="aksen" />
@else
    <x-status-tag label="Aman" warna="inti" />
@endif
