@if ($paginator->hasPages())
    <p class="m-0 text-muted">Showing <span>{{ $paginator->firstItem() }}</span> to <span>{{ $paginator->lastItem() }}</span> of <span>{{ $paginator->total() }}</span> entries</p>
    <ul class="pagination m-0 ms-auto">
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                    <!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="15 6 9 12 15 18"/>
                    </svg>
                    Previous
                </a>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" tabindex="-1" aria-disabled="true">
                    <!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="15 6 9 12 15 18"/>
                    </svg>
                    Previous
                </a>
            </li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><a class="page-link" href="#">{{ $page }}</a></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}">
                    Next <!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="9 6 15 12 9 18"/>
                    </svg>
                </a>
            </li>
        @else
            <li class="page-item disabled">
                <a class="page-link" href="#">
                   Next <!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="9 6 15 12 9 18"/>
                    </svg>
                </a>
            </li>
        @endif
    </ul>
@endif
