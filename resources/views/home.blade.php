@extends('layouts.front')

@section('content')
    <main class="flex-grow flex flex-col items-center justify-center px-4 py-12 md:py-20">
        <div class="w-full max-w-3xl flex flex-col items-center text-center space-y-8">
            <div class="space-y-4">
                <h1
                    class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Aclarando la <br /> terminologia del mundo.
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-lg md:text-xl max-w-2xl mx-auto font-light">
                    Una palabra a la vez. Descubre definiciones, contexto y matices para miles de terminos.
                </p>
            </div>
            <div class="w-full max-w-2xl mt-8">
                <div class="relative group">
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                        <span class="material-symbols-outlined">search</span>
                    </div>
                    <input
                        class="block w-full h-14 pl-12 pr-4 text-lg text-slate-900 dark:text-white bg-white dark:bg-[#1a2634] border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary placeholder-slate-400 shadow-sm transition-all"
                        placeholder="Busca un termino, tema o categoria..." type="text" />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                        <button
                            class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                            <span class="material-symbols-outlined text-xl">mic</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="w-full max-w-2xl pt-4">
                <div class="flex flex-col md:flex-row items-center justify-center gap-3 text-sm">
                    <span class="text-slate-400 font-medium">Tendencia ahora:</span>
                    <div class="flex flex-wrap justify-center gap-2">
                        @foreach ($trendingTerms as $term)
                            <a class="px-3 py-1.5 bg-white dark:bg-[#1a2634] border border-slate-200 dark:border-slate-700 rounded-full text-slate-600 dark:text-slate-300 hover:border-primary hover:text-primary transition-colors"
                                href="{{ route('browse', ['letter' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($term->currentVersion?->title ?? $term->title_en ?? $term->slug, 0, 1))]) }}">
                                {{ $term->currentVersion?->title ?? $term->title_en ?? $term->slug }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!-- Featured Categories Grid (Optional visual balance)
        <div class="w-full max-w-5xl mt-24 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-white dark:bg-[#15202b] p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow cursor-pointer group">
                <div
                    class="size-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-primary flex items-center justify-center mb-4 group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">rocket_launch</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Tech &amp; Startups</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Jargon from the valley, decoded for everyone.</p>
            </div>
            <div
                class="bg-white dark:bg-[#15202b] p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow cursor-pointer group">
                <div
                    class="size-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">account_balance</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Finance &amp; Crypto</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Understanding markets, assets, and the blockchain.
                </p>
            </div>
            <div
                class="bg-white dark:bg-[#15202b] p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow cursor-pointer group">
                <div
                    class="size-10 rounded-lg bg-purple-50 dark:bg-purple-900/20 text-purple-600 flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">palette</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Design &amp; Art</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm">From typography to color theory, master the craft.
                </p>
            </div>
        </div>
        -->
    </main>
@endsection
