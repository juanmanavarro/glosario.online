@extends('layouts.front')

@section('title', ($term->currentVersion?->title ?? $term->title_en ?? $term->slug) . ' - Glosario')
@section('fixedHeader', true)

@section('content')
    @php
        $displayTitle = $term->currentVersion?->title ?? $term->title_en ?? $term->slug;
        $englishTitle = $term->title_en;
        $senses = $term->currentVersion?->senses ?? collect();
        $notes = trim((string) ($term->currentVersion?->notes ?? ''));
        $publishedAt = $term->published_at?->format('d/m/Y');
    @endphp

    <main class="flex min-h-[calc(100vh-73px)] w-full max-w-[1440px] mx-auto mt-[73px]">
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

        <div class="flex-1 p-6 md:p-10 lg:p-16 md:ml-20">
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

            <div class="max-w-5xl mx-auto w-full mb-8">
                <a class="inline-flex items-center text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                    href="{{ route('browse', ['letter' => $selectedLetter !== '' ? $selectedLetter : null]) }}">
                    <span class="material-symbols-outlined text-[20px] mr-1">arrow_back</span>
                    Volver a explorar
                </a>
            </div>

            <section
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-8 md:p-12 shadow-sm max-w-5xl mx-auto">
                <div
                    class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-8 border-b border-slate-100 dark:border-slate-800 pb-8">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-3 mb-3">
                            @if (auth()->check() && ! auth()->user()->hasRole('member'))
                                <span
                                    @class([
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide',
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
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">
                                    {{ $publishedAt ? 'Publicado: ' . $publishedAt : 'Sin fecha de publicacion' }}
                                </span>
                            @endif
                        </div>

                        <h1 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white tracking-tight mb-3">
                            {{ $displayTitle }}
                        </h1>

                        <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                            @if (filled($englishTitle))
                                <span class="inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-base">translate</span>
                                    {{ $englishTitle }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    @if ($senses->isNotEmpty())
                        <div>
                            <h2 class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4">
                                Acepciones
                            </h2>
                            <div class="space-y-4">
                                @foreach ($senses as $sense)
                                    <article class="rounded-lg border border-slate-200 dark:border-slate-800 p-5">
                                        <div class="flex items-center gap-3 mb-3">
                                            <span
                                                class="inline-flex items-center rounded-full bg-primary/10 text-primary px-2.5 py-0.5 text-xs font-semibold">
                                                {{ $sense->sense_number }}
                                            </span>
                                        </div>

                                        <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                                            {{ trim(strip_tags($sense->definition)) !== '' ? trim(strip_tags($sense->definition)) : 'Sin definicion disponible.' }}
                                        </p>

                                        @if ($sense->relations->isNotEmpty())
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                @foreach ($sense->relations as $relation)
                                                    @if ($relation->relatedTerm)
                                                        <a href="{{ route('terms.show', $relation->relatedTerm->slug) }}"
                                                            class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-primary transition-colors">
                                                            {{ $relation->relatedTerm->currentVersion?->title ?? $relation->relatedTerm->title_en ?? $relation->relatedTerm->slug }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 {{ auth()->check() && ! auth()->user()->hasRole('member') ? 'md:grid-cols-2' : '' }} gap-8 pt-2">
                        @if (auth()->check() && ! auth()->user()->hasRole('member'))
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-3">
                                    <span class="material-symbols-outlined text-slate-400 text-lg">notes</span>
                                    Notas
                                </h3>
                                <div
                                    class="text-slate-500 dark:text-slate-400 text-sm {{ $notes === '' ? 'italic' : '' }} bg-slate-50 dark:bg-slate-800/30 p-4 rounded border border-dashed border-slate-200 dark:border-slate-700">
                                    {{ $notes !== '' ? $notes : 'No hay notas disponibles.' }}
                                </div>
                            </div>
                        @endif

                        <div>
                            <div class="flex flex-wrap gap-2">
                                @forelse ($term->categories as $category)
                                    <span
                                        class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/20 px-3 py-1 text-xs font-medium text-primary border border-blue-100 dark:border-blue-900/30">
                                        {{ $category->name }}
                                    </span>
                                @empty
                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-medium text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        Sin categoria
                                    </span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
@endsection
