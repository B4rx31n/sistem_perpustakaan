@props(['paginator'])

@if ($paginator->hasPages())
    <nav aria-label="Navigasi halaman" class="mt-4 flex flex-col gap-3 border-t border-garis pt-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-[0.75rem] text-tinta-samar">
            Menampilkan {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }}
            dari {{ number_format($paginator->total(), 0, ',', '.') }} data
        </p>

        <div class="flex flex-wrap items-center gap-1">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true"
                      class="rounded-lg border border-garis bg-permukaan-lembut/50 px-3 py-1.5 text-[0.75rem] text-tinta-samar">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   rel="prev"
                   class="rounded-lg border border-garis bg-permukaan px-3 py-1.5 text-[0.75rem] text-tinta shadow-halus transition-[background-color,border-color,box-shadow,transform] duration-150 hover:border-garis-kuat hover:bg-permukaan-lembut active:scale-[0.98]">
                    Sebelumnya
                </a>
            @endif

            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $halaman => $url)
                @if ($halaman == $paginator->currentPage())
                    <span aria-current="page"
                          class="rounded-lg border border-transparent bg-inti px-3 py-1.5 text-[0.75rem] font-semibold text-inti-kunci shadow-halus">
                        {{ $halaman }}
                    </span>
                @else
                    <a href="{{ $url }}"
                       class="rounded-lg border border-garis bg-permukaan px-3 py-1.5 text-[0.75rem] text-tinta-lembut transition-[background-color,border-color,transform] duration-150 hover:border-garis-kuat hover:text-tinta active:scale-[0.98]">
                        {{ $halaman }}
                    </a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   rel="next"
                   class="rounded-lg border border-garis bg-permukaan px-3 py-1.5 text-[0.75rem] text-tinta shadow-halus transition-[background-color,border-color,box-shadow,transform] duration-150 hover:border-garis-kuat hover:bg-permukaan-lembut active:scale-[0.98]">
                    Berikutnya
                </a>
            @else
                <span aria-disabled="true"
                      class="rounded-lg border border-garis bg-permukaan-lembut/50 px-3 py-1.5 text-[0.75rem] text-tinta-samar">
                    Berikutnya
                </span>
            @endif
        </div>
    </nav>
@endif
