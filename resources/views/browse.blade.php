@extends('layouts.front')

@section('hideFooter', true)
@section('fixedHeader', true)

    @section('content')
<main class="flex flex-1 w-full max-w-[1440px] mx-auto mt-[73px] h-[calc(100vh-73px)] overflow-hidden">
    <aside
        class="hidden md:flex fixed top-[73px] z-40 flex-col w-20 h-[calc(100vh-73px)] overflow-y-auto border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 items-center py-6 gap-2"
        style="left: max(0px, calc((100vw - 1440px) / 2));">
        @foreach ($availableLetters as $letter)
            <a class="{{ $selectedLetter === $letter
                ? 'bg-primary text-white shadow-sm scale-105'
                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }} w-10 h-10 flex items-center justify-center rounded-lg font-medium text-sm transition-all"
                href="{{ route('browse', ['letter' => $letter]) }}">
                {{ $letter }}
            </a>
        @endforeach
    </aside>

    <div class="flex-1 flex flex-col h-full min-h-0 py-6 px-4 md:py-10 md:px-6 lg:py-16 lg:px-8 md:ml-20 overflow-hidden">
        <div class="md:hidden mb-8">
            <div class="flex overflow-x-auto gap-2 pb-2 scrollbar-hide">
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

        <div class="mb-8">
            <div class="flex flex-col">
                <p
                    class="text-slate-500 dark:text-slate-400 text-sm"
                    data-browse-summary
                    data-total="{{ $terms->total() }}"
                    data-loaded="{{ $terms->lastItem() ?? 0 }}"
                    data-letter="{{ $selectedLetter }}">
                    @if ($terms->total() > 0)
                        Mostrando {{ $terms->firstItem() }}-{{ $terms->lastItem() }} de {{ $terms->total() }}
                        términos{{ $selectedLetter !== '' ? ' que empiezan por "' . $selectedLetter . '"' : '' }}
                    @else
                        No hay términos{{ $selectedLetter !== '' ? ' para la letra "' . $selectedLetter . '"' : '' }}
                    @endif
                </p>
            </div>
        </div>

        @if ($terms->isNotEmpty())
            <div class="flex-1 min-h-0 overflow-y-auto pb-20" data-browse-scroll-root>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6" data-browse-grid>
                    @include('partials.browse-term-cards', ['terms' => $terms])
                </div>

                <div
                    class="flex items-center justify-center py-8 text-sm text-slate-500 dark:text-slate-400"
                    data-browse-loader
                    data-next-page-url="{{ $terms->nextPageUrl() }}"
                    @if (! $terms->hasMorePages()) hidden @endif>
                    <span data-browse-loader-text>Cargando más términos...</span>
                </div>
            </div>
        @else
            <div
                class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 bg-white/80 dark:bg-slate-900/80 p-10 text-center text-slate-500 dark:text-slate-400">
                No hay términos para mostrar en este filtro.
            </div>
        @endif
    </div>
</main>

@if ($terms->isNotEmpty())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const scrollRoot = document.querySelector('[data-browse-scroll-root]');
                const grid = document.querySelector('[data-browse-grid]');
                const loader = document.querySelector('[data-browse-loader]');
                const loaderText = document.querySelector('[data-browse-loader-text]');
                const summary = document.querySelector('[data-browse-summary]');

                if (!scrollRoot || !grid || !loader || !summary) {
                    return;
                }

                let nextPageUrl = loader.dataset.nextPageUrl;
                let isLoading = false;
                const total = Number(summary.dataset.total || 0);
                const selectedLetter = summary.dataset.letter || '';
                const preloadOffset = 300;

                const updateSummary = (loaded) => {
                    if (total === 0) {
                        summary.textContent = selectedLetter !== ''
                            ? `No hay términos para la letra "${selectedLetter}"`
                            : 'No hay términos';

                        return;
                    }

                    const suffix = selectedLetter !== ''
                        ? ` que empiezan por "${selectedLetter}"`
                        : '';

                    summary.textContent = `Mostrando 1-${loaded} de ${total} términos${suffix}`;
                    summary.dataset.loaded = String(loaded);
                };

                const shouldLoadMore = () => {
                    const loaderRect = loader.getBoundingClientRect();

                    return loaderRect.top <= window.innerHeight + preloadOffset;
                };

                const loadMore = async () => {
                    if (!nextPageUrl || isLoading) {
                        return;
                    }

                    isLoading = true;
                    loader.hidden = false;
                    loaderText.textContent = 'Cargando más términos...';

                    try {
                        const url = new URL(nextPageUrl, window.location.origin);
                        url.searchParams.set('fragment', '1');

                        const response = await fetch(url.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const payload = await response.json();

                        if (payload.items) {
                            grid.insertAdjacentHTML('beforeend', payload.items);
                        }

                        nextPageUrl = payload.next_page_url;
                        loader.dataset.nextPageUrl = nextPageUrl || '';
                        updateSummary(Number(payload.last_item || summary.dataset.loaded || 0));

                        if (!payload.has_more_pages || !nextPageUrl) {
                            loader.hidden = true;
                            window.removeEventListener('scroll', handleScroll);
                        }
                    } catch (error) {
                        loaderText.textContent = 'No se pudieron cargar más términos. Reintenta al hacer scroll.';
                    } finally {
                        isLoading = false;
                    }
                };

                const handleScroll = () => {
                    if (shouldLoadMore()) {
                        loadMore();
                    }
                };

                if (nextPageUrl) {
                    window.addEventListener('scroll', handleScroll, { passive: true });
                }
            });
        </script>
    @endpush
@endif
@endsection
