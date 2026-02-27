<?php

use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/browse', function (Request $request) {
    $baseQuery = Term::query()
        ->join('term_versions as current_versions', 'current_versions.id', '=', 'terms.current_version_id')
        ->whereNotNull('terms.current_version_id');

    $availableLetters = (clone $baseQuery)
        ->selectRaw('UPPER(LEFT(current_versions.title, 1)) as letter')
        ->distinct()
        ->pluck('letter')
        ->filter()
        ->values();

    $selectedLetter = strtoupper((string) $request->query('letter', ''));

    if ($selectedLetter === '' && $availableLetters->isNotEmpty()) {
        $selectedLetter = (string) $availableLetters->first();
    }

    if ($selectedLetter !== '' && ! $availableLetters->contains($selectedLetter)) {
        $selectedLetter = '';
    }

    $terms = Term::query()
        ->with(['currentVersion', 'categories'])
        ->join('term_versions as current_versions', 'current_versions.id', '=', 'terms.current_version_id')
        ->whereNotNull('terms.current_version_id')
        ->when(
            $selectedLetter !== '',
            fn ($query) => $query->where('current_versions.title', 'like', $selectedLetter . '%')
        )
        ->orderBy('current_versions.title')
        ->select('terms.*')
        ->paginate(24)
        ->withQueryString();

    return view('browse', [
        'availableLetters' => $availableLetters,
        'selectedLetter' => $selectedLetter,
        'terms' => $terms,
    ]);
})->name('browse');

Route::get('/login', fn () => redirect('/admin/login'))->name('login');

Route::middleware('auth')->get('/glosary', function (Request $request) {
    abort_unless($request->user()?->hasRole('member'), 403);

    return response('Zona privada member (/glosary)');
});
