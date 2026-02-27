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

    <div class="flex-1 flex flex-col h-full min-h-0 p-6 md:p-10 lg:p-16 md:ml-20 overflow-hidden">
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
                <p class="text-slate-500 dark:text-slate-400 text-sm">
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
            <div class="flex-1 min-h-0 overflow-y-auto pr-2 pb-20">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                    @foreach ($terms as $term)
                        <article
                            class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between mb-3 gap-3">
                                    <h2
                                        class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                                        {{ $term->currentVersion?->title ?? $term->title_en ?? $term->slug }}
                                    </h2>
                                    @if (auth()->check() && ! auth()->user()->hasRole('member'))
                                        <span
                                            @class([
                                                'inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wide',
                                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' => $term->status?->value === 'published',
                                                'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' => $term->status?->value === 'review',
                                                'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' => $term->status?->value === 'draft',
                                                'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300' => $term->status?->value === 'archived',
                                            ])>
                                            {{ match($term->status?->value) {
                                                'published' => 'Publicado',
                                                'review' => 'Revision',
                                                'draft' => 'Borrador',
                                                'archived' => 'Archivado',
                                                default => 'Sin estado',
                                            } }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                                    {{ \Illuminate\Support\Str::limit(trim(strip_tags($term->currentVersion?->definition ?? 'Sin definicion disponible.')), 180) }}
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                                @forelse ($term->categories as $category)
                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">
                                        {{ $category->name }}
                                    </span>
                                @empty
                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">
                                        Sin categoría
                                    </span>
                                @endforelse
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="fixed bottom-6 left-1/2 z-40 w-full max-w-[calc(1440px-5rem)] -translate-x-1/2 px-6 md:px-10 lg:px-16">
                {{ $terms->links('vendor.pagination.browse') }}
            </div>
        @else
            <div
                class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 bg-white/80 dark:bg-slate-900/80 p-10 text-center text-slate-500 dark:text-slate-400">
                No hay términos para mostrar en este filtro.
            </div>
        @endif
    </div>
</main>
@endsection
