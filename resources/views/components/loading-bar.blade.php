{{-- Indikator memuat. dipakai bersama oleh JS global: setiap form POST menyalakan bilah ini sampai
     halaman selesai dimuat, sehingga kondisi "sedang mengirim" selalu terlihat
     dan tombol tidak menggantung tanpa penjelasan. --}}
<div data-indikator-muat
     hidden
     class="fixed inset-x-0 top-0 z-50 h-1 bg-garis/40"
     role="status"
     aria-live="polite">
    <span class="sr-only">Sedang mengirim data, mohon tunggu sebentar.</span>
    <span aria-hidden="true" class="block h-full w-1/3 rounded-full bg-inti motion-safe:animate-[geser-indikator_1.1s_ease-in-out_infinite]"></span>
</div>
