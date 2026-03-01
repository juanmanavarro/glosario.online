@extends('layouts.front')

@section('hideFooter', true)
@section('fixedHeader', true)

    @section('content')
<main class="flex flex-1 w-full max-w-[1440px] mx-auto mt-[73px] h-[calc(100vh-73px)] overflow-hidden">
    <aside
        class="hidden md:flex fixed top-[73px] z-40 flex-col w-20 h-[calc(100vh-73px)] overflow-y-auto border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 items-center py-6 gap-2"
        style="left: max(0px, calc((100vw - 1440px) / 2));">
        <a class="{{ $selectedLetter === ''
            ? 'bg-primary text-white shadow-sm scale-105'
            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }} w-10 h-10 flex items-center justify-center rounded-lg font-medium text-sm transition-all"
            href="{{ route('browse') }}">
            #
        </a>
        @foreach ($availableLetters as $letter)
            <a class="{{ $selectedLetter === $letter
                ? 'bg-primary text-white shadow-sm scale-105'
                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }} w-10 h-10 flex items-center justify-center rounded-lg font-medium text-sm transition-all"
                href="{{ route('browse', ['letter' => $letter]) }}">
                {{ $letter }}
            </a>
        @endforeach
    </aside>

    <div class="flex-1 flex flex-col h-full min-h-0 pt-4 pb-6 px-4 md:pt-6 md:pb-10 md:px-6 lg:pt-8 lg:pb-16 lg:px-8 md:ml-20 overflow-hidden">
        <div class="md:hidden mb-8">
            <div class="flex overflow-x-auto gap-2 pb-2 scrollbar-hide">
                <a class="{{ $selectedLetter === ''
                    ? 'bg-primary text-white border-primary'
                    : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400' }} flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg border font-medium text-sm"
                    href="{{ route('browse') }}">
                    #
                </a>
                @foreach ($availableLetters as $letter)
                    <a class="{{ $selectedLetter === $letter
                        ? 'bg-primary text-white border-primary'
                        : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400' }} flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg border font-medium text-sm"
                        href="{{ route('browse', ['letter' => $letter]) }}">
                        {{ $letter }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="flex-1 min-h-0 overflow-y-auto pb-20" data-browse-scroll-root>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6" data-browse-grid>
                @include('partials.browse-term-cards', ['terms' => $terms])
            </div>

            <div
                class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 bg-white/80 dark:bg-slate-900/80 p-10 text-center text-slate-500 dark:text-slate-400 @if ($terms->isNotEmpty()) hidden @endif"
                data-browse-empty>
                No hay términos para mostrar en este filtro.
            </div>

            <div
                class="flex items-center justify-center py-8 text-sm text-slate-500 dark:text-slate-400 opacity-0 pointer-events-none transition-opacity"
                data-browse-loader
                data-next-page-url="{{ $nextPageUrl }}"
                @if (! $terms->hasMorePages()) hidden @endif>
                <span data-browse-loader-text>Cargando más términos...</span>
            </div>
        </div>
    </div>
</main>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const scrollRoot = document.querySelector('[data-browse-scroll-root]');
            const grid = document.querySelector('[data-browse-grid]');
            const emptyState = document.querySelector('[data-browse-empty]');
            const loader = document.querySelector('[data-browse-loader]');
            const loaderText = document.querySelector('[data-browse-loader-text]');
            const searchForm = document.querySelector('[data-browse-search-form]');
            const searchInput = document.querySelector('[data-browse-search-input]');
            const resultsCount = document.querySelector('[data-browse-results-count]');

            if (!scrollRoot || !grid || !emptyState || !loader || !loaderText) {
                return;
            }

            let nextPageUrl = loader.dataset.nextPageUrl;
            let isLoading = false;
            let debounceTimer;
            let searchRequestId = 0;
            const preloadOffset = 300;

            const updateLoaderState = (hasMorePages, keepVisible = false) => {
                nextPageUrl = hasMorePages ? loader.dataset.nextPageUrl : '';

                if (!hasMorePages || !nextPageUrl) {
                    loader.hidden = true;
                    loader.classList.add('opacity-0', 'pointer-events-none');

                    return;
                }

                loader.hidden = false;

                if (keepVisible) {
                    loader.classList.remove('opacity-0', 'pointer-events-none');
                } else {
                    loader.classList.add('opacity-0', 'pointer-events-none');
                }
            };

            const applyPayload = (payload, { append = false } = {}) => {
                if (!append) {
                    grid.innerHTML = payload.items || '';
                    window.scrollTo({ top: 0, behavior: 'auto' });
                } else if (payload.items) {
                    grid.insertAdjacentHTML('beforeend', payload.items);
                }

                emptyState.hidden = Boolean(payload.has_items);
                loader.dataset.nextPageUrl = payload.next_page_url || '';
                updateLoaderState(Boolean(payload.has_more_pages));

                if (resultsCount && typeof payload.total !== 'undefined') {
                    resultsCount.textContent = `${payload.total} términos`;
                }
            };

            const fetchPage = async (url, { append = false, requestId = null } = {}) => {
                if (!append && requestId !== searchRequestId) {
                    return;
                }

                isLoading = true;
                loaderText.textContent = 'Cargando más términos...';

                if (append) {
                    updateLoaderState(true, true);
                }

                try {
                    const requestUrl = new URL(url, window.location.origin);
                    requestUrl.protocol = window.location.protocol;
                    requestUrl.host = window.location.host;
                    requestUrl.searchParams.set('fragment', '1');

                    const response = await fetch(requestUrl.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const payload = await response.json();

                    if (!append && requestId !== searchRequestId) {
                        return;
                    }

                    applyPayload(payload, { append });
                } catch (error) {
                    loader.hidden = false;
                    loader.classList.remove('opacity-0', 'pointer-events-none');
                    loaderText.textContent = 'No se pudieron cargar más términos. Reintenta al hacer scroll.';
                } finally {
                    isLoading = false;
                }
            };

            const shouldLoadMore = () => {
                const loaderRect = loader.getBoundingClientRect();

                return !loader.hidden && loaderRect.top <= window.innerHeight + preloadOffset;
            };

            const handleScroll = () => {
                if (!isLoading && nextPageUrl && shouldLoadMore()) {
                    fetchPage(nextPageUrl, { append: true });
                }
            };

            if (searchForm && searchInput) {
                searchForm.addEventListener('submit', (event) => {
                    event.preventDefault();

                    const url = new URL(searchForm.action, window.location.origin);
                    const formData = new FormData(searchForm);

                    for (const [key, value] of formData.entries()) {
                        if (String(value).trim() !== '') {
                            url.searchParams.set(key, String(value));
                        }
                    }

                    history.replaceState({}, '', url);
                    searchRequestId += 1;
                    fetchPage(url.toString(), { requestId: searchRequestId });
                });

                searchInput.addEventListener('input', () => {
                    window.clearTimeout(debounceTimer);

                    debounceTimer = window.setTimeout(() => {
                        searchForm.requestSubmit();
                    }, 250);
                });
            }

            updateLoaderState(Boolean(nextPageUrl));
            window.addEventListener('scroll', handleScroll, { passive: true });
        });
    </script>
@endpush
@endsection
