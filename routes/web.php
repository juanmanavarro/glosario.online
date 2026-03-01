<?php

use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    $timezone = config('app.timezone');
    $now = now()->setTimezone($timezone);
    $cacheKey = 'home:trending-terms:'.$now->toDateString();
    $expiresAt = $now->copy()->addDay()->startOfDay();

    $trendingTerms = Cache::remember($cacheKey, $expiresAt, fn () => Term::query()
        ->with('currentVersion')
        ->whereNotNull('current_version_id')
        ->inRandomOrder()
        ->limit(3)
        ->get());

    return view('home', [
        'trendingTerms' => $trendingTerms,
    ]);
});

Route::get('/browse', function (Request $request) {
    $toRelativeUrl = static function (?string $url): ?string {
        if (! filled($url)) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $query = parse_url($url, PHP_URL_QUERY);
        $fragment = parse_url($url, PHP_URL_FRAGMENT);

        return $path
            . ($query ? '?'.$query : '')
            . ($fragment ? '#'.$fragment : '');
    };

    $baseQuery = Term::query()
        ->join('term_versions as current_versions', 'current_versions.id', '=', 'terms.current_version_id')
        ->whereNotNull('terms.current_version_id');

    $search = trim((string) $request->query('q', ''));

    $availableLetters = (clone $baseQuery)
        ->selectRaw('UPPER(LEFT(current_versions.title, 1)) as letter')
        ->distinct()
        ->orderBy('letter')
        ->pluck('letter')
        ->filter()
        ->values();

    $selectedLetter = strtoupper((string) $request->query('letter', ''));

    if ($search !== '') {
        $selectedLetter = '';
    }

    if ($search === '' && $selectedLetter !== '' && ! $availableLetters->contains($selectedLetter)) {
        $selectedLetter = '';
    }

    $terms = Term::query()
        ->with(['currentVersion.senses', 'categories'])
        ->join('term_versions as current_versions', 'current_versions.id', '=', 'terms.current_version_id')
        ->whereNotNull('terms.current_version_id')
        ->when(
            $search !== '',
            fn ($query) => $query->where('current_versions.title', 'like', '%'.$search.'%')
        )
        ->when(
            $search === '' && $selectedLetter !== '',
            fn ($query) => $query->where('current_versions.title', 'like', $selectedLetter.'%')
        )
        ->orderBy('current_versions.title')
        ->select('terms.*')
        ->paginate(24)
        ->withQueryString();

    if ($request->boolean('fragment')) {
        return response()->json([
            'items' => view('partials.browse-term-cards', ['terms' => $terms])->render(),
            'next_page_url' => $toRelativeUrl($terms->nextPageUrl()),
            'has_more_pages' => $terms->hasMorePages(),
            'has_items' => $terms->isNotEmpty(),
            'total' => $terms->total(),
        ]);
    }

    return view('browse', [
        'availableLetters' => $availableLetters,
        'nextPageUrl' => $toRelativeUrl($terms->nextPageUrl()),
        'selectedLetter' => $selectedLetter,
        'terms' => $terms,
    ]);
})->name('browse');

Route::get('/term/{slug}', function (Request $request, string $slug) {
    $term = Term::query()
        ->with([
            'categories',
            'currentVersion.senses.relations.relatedTerm.currentVersion',
        ])
        ->whereNotNull('current_version_id')
        ->where('slug', $slug)
        ->firstOrFail();

    $selectedLetter = Str::upper((string) Str::substr(
        $term->currentVersion?->title ?? $term->title_en ?? $term->slug,
        0,
        1
    ));

    $previousTerm = null;
    $from = trim((string) $request->query('from', ''));

    if ($from !== '' && $from !== $term->slug) {
        $previousTerm = Term::query()
            ->with('currentVersion')
            ->whereNotNull('current_version_id')
            ->where('slug', $from)
            ->first();
    }

    return view('term', [
        'previousTerm' => $previousTerm,
        'selectedLetter' => $selectedLetter,
        'term' => $term,
    ]);
})->name('terms.show');

Route::get('/login', fn () => redirect('/admin/login'))->name('login');

Route::middleware('auth')->get('/glosary', function (Request $request) {
    abort_unless($request->user()?->hasRole('member'), 403);

    return response('Zona privada member (/glosary)');
});
