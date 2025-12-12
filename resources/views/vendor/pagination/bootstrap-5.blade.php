@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation" class="mt-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center">
            <!-- Info Text -->
            <div class="mb-3 mb-sm-0">
                <small class="text-muted">
                    Menampilkan <strong>{{ $paginator->firstItem() }}</strong> sampai
                    <strong>{{ $paginator->lastItem() }}</strong>
                    dari total <strong>{{ $paginator->total() }}</strong> data
                </small>
            </div>

            <!-- Pagination Links -->
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="bi bi-chevron-left text-dark"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                            <i class="bi bi-chevron-left text-dark"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled">
                            <span class="page-link">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active">
                                    <span class="page-link bg-primary text-white">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link text-dark" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                            <i class="bi bi-chevron-right text-dark"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="bi bi-chevron-right text-dark"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif