@if ($paginator->hasPages())
    <nav class="mt-6 flex items-center justify-between gap-4 text-sm" aria-label="Pagination">
        <p class="text-smoke">Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}</p>
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="icon-btn text-ink/25" aria-disabled="true"><x-icon name="arrow-left" /></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="icon-btn bg-white ring-1 ring-ink/10" rel="prev" aria-label="Previous page"><x-icon name="arrow-left" /></a>
            @endif
            <span class="px-3 tabular-nums">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="icon-btn bg-white ring-1 ring-ink/10" rel="next" aria-label="Next page"><x-icon name="arrow-right" /></a>
            @else
                <span class="icon-btn text-ink/25" aria-disabled="true"><x-icon name="arrow-right" /></span>
            @endif
        </div>
    </nav>
@endif
