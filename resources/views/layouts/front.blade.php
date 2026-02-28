<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'Glosario - Busqueda simple')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        @font-face {
            font-family: "Lexend";
            src: url("/fonts/front/Lexend-Regular.ttf") format("truetype");
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: "Lexend";
            src: url("/fonts/front/Lexend-Medium.ttf") format("truetype");
            font-weight: 500;
            font-style: normal;
        }

        @font-face {
            font-family: "Lexend";
            src: url("/fonts/front/Lexend-SemiBold.ttf") format("truetype");
            font-weight: 600;
            font-style: normal;
        }

        @font-face {
            font-family: "Lexend";
            src: url("/fonts/front/Lexend-Bold.ttf") format("truetype");
            font-weight: 700;
            font-style: normal;
        }

        @font-face {
            font-family: "Lexend";
            src: url("/fonts/front/Lexend-ExtraBold.ttf") format("truetype");
            font-weight: 800;
            font-style: normal;
        }
    </style>
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Lexend", "sans-serif"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>
</head>

<body
    class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display min-h-screen flex flex-col">
    @php
        $showSearchLink = request()->routeIs('browse', 'terms.show');
        $showBrowseHeaderSearch = request()->routeIs('browse');
    @endphp
    <header
        class="{{ View::hasSection('fixedHeader') ? 'fixed top-0 inset-x-0 z-50' : '' }} w-full border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-[#15202b]">
        <div class="w-full max-w-[1440px] mx-auto px-4 py-5 flex items-center gap-6">
            <a class="flex items-center gap-3" href="{{ url('/') }}">
                <div class="size-8 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">menu_book</span>
                </div>
                <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Glosario</h2>
            </a>

            @if ($showBrowseHeaderSearch)
                <div class="hidden md:flex flex-1 items-center justify-center gap-4">
                    <form action="{{ route('browse') }}" class="w-full max-w-md" data-browse-search-form
                        method="GET">
                        @if (request()->filled('letter'))
                            <input name="letter" type="hidden" value="{{ request()->query('letter') }}">
                        @endif
                        <label class="relative w-full">
                            <span
                                class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                            <input
                                class="w-full h-11 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#1a2634] pl-11 pr-4 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                                autocomplete="off" autocapitalize="none" autocorrect="off" name="q"
                                placeholder="Buscar término..." spellcheck="false" type="search"
                                data-browse-search-input
                                value="{{ request()->query('q') }}">
                        </label>
                    </form>

                    <div
                        class="items-center text-sm font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap"
                        data-browse-results-count>
                        {{ isset($terms) ? $terms->total() : 0 }} términos
                    </div>
                </div>
            @else
                <div class="hidden md:block flex-1"></div>
            @endif

            <div class="hidden md:flex items-center gap-8 ml-auto">
                <nav class="flex gap-6">
                    <a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary text-sm font-medium transition-colors"
                        href="{{ $showSearchLink ? url('/') : route('browse') }}">
                        {{ $showSearchLink ? 'Buscar' : 'Explorar' }}
                    </a>
                    {{-- <a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary text-sm font-medium transition-colors"
                        href="#">Acerca de</a>
                    <a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary text-sm font-medium transition-colors"
                        href="#">Contribuir</a> --}}
                </nav>
                <a class="bg-primary hover:bg-blue-600 text-white text-sm font-bold px-5 py-2.5 rounded-lg transition-colors shadow-sm"
                    href="{{ url('/admin') }}">
                    {{ auth()->check() ? 'Panel' : 'Iniciar sesion' }}
                </a>
            </div>
            <button class="md:hidden text-slate-600 dark:text-slate-400">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>
    </header>

    @yield('content')

    @unless (View::hasSection('hideFooter'))
        <footer class="w-full py-8 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-[#15202b] mt-auto">
            <div
                class="w-full max-w-[1440px] mx-auto px-4 flex flex-col md:flex-row justify-between items-center text-sm text-slate-500 dark:text-slate-400">
                <p>© 2023 Glosario. Todos los derechos reservados.</p>
                <div class="flex gap-6 mt-4 md:mt-0">
                    <a class="hover:text-primary transition-colors" href="#">Privacidad</a>
                    <a class="hover:text-primary transition-colors" href="#">Terminos</a>
                    <a class="hover:text-primary transition-colors" href="#">Contacto</a>
                </div>
            </div>
        </footer>
    @endunless

    @stack('scripts')
</body>

</html>
