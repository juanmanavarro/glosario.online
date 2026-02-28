@foreach ($terms as $term)
    @php
        $senseCount = $term->currentVersion?->senses?->count() ?? 0;
        $additionalSensesCount = max($senseCount - 1, 0);
        $previewDefinition = trim(strip_tags($term->currentVersion?->senses->first()?->definition ?? ''));
        $visibleCategories = $term->categories->reject(
            fn ($category) => $category->slug === 'sin-categoria'
        );
    @endphp
    <a href="{{ route('terms.show', $term->slug) }}"
        class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between mb-3 gap-3">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
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
                {{ \Illuminate\Support\Str::limit($previewDefinition !== '' ? $previewDefinition : 'Sin definicion disponible.', 180) }}
            </p>
            @if ($additionalSensesCount > 0)
                <p class="text-xs font-medium text-primary mb-4">
                    + {{ $additionalSensesCount }}
                    {{ $additionalSensesCount === 1 ? 'acepción' : 'acepciones' }}
                </p>
            @endif
        </div>
        @if ($visibleCategories->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                @foreach ($visibleCategories as $category)
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">
                        {{ $category->name }}
                    </span>
                @endforeach
            </div>
        @endif
    </a>
@endforeach
