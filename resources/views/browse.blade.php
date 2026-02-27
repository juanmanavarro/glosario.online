@extends('layouts.front')

@section('content')
<main class="flex flex-1 w-full max-w-[1440px] mx-auto">
    <!-- Sidebar A-Z Filter -->
    <aside
        class="hidden md:flex flex-col w-20 sticky top-[73px] h-[calc(100vh-73px)] border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-y-auto items-center py-6 gap-2">
        <a class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary text-white font-bold text-sm shadow-sm transition-transform hover:scale-105"
            href="#">A</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">B</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">C</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">D</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">E</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">F</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">G</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">H</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">I</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">J</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">K</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">L</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">M</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">N</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">O</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">P</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">Q</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">R</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">S</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">T</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">U</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">V</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">W</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">X</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">Y</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">Z</a>
        <a class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-sm transition-colors"
            href="#">#</a>
    </aside>
    <!-- Terms Content -->
    <div class="flex-1 flex flex-col p-6 md:p-10 lg:p-16">
        <!-- Mobile Search & Filter (Visible only on mobile) -->
        <div class="md:hidden mb-8">
            <label class="flex flex-col w-full h-12 mb-4">
                <div
                    class="flex w-full flex-1 items-stretch rounded-lg h-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <div class="text-slate-500 dark:text-slate-400 flex items-center justify-center pl-3">
                        <span class="material-symbols-outlined text-[24px]">search</span>
                    </div>
                    <input
                        class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-slate-900 dark:text-white focus:outline-0 focus:ring-0 border-none bg-transparent h-full placeholder:text-slate-400 px-3 text-base"
                        placeholder="Search..." value="" />
                </div>
            </label>
            <div class="flex overflow-x-auto gap-2 pb-2 scrollbar-hide">
                <a class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-primary text-white font-bold text-sm"
                    href="#">A</a>
                <a class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-medium text-sm"
                    href="#">B</a>
                <a class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-medium text-sm"
                    href="#">C</a>
            </div>
        </div>
        <div class="flex items-center justify-between mb-8">
            <div class="flex flex-col">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Terminos tecnicos</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Mostrando 1-12 de 148 terminos que empiezan por "A"</p>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Ordenar por:</span>
                <select
                    class="form-select bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary py-1.5 pl-3 pr-8 cursor-pointer">
                    <option>Alfabetico (A-Z)</option>
                    <option>Alfabetico (Z-A)</option>
                    <option>Mas populares</option>
                    <option>Mas recientes</option>
                </select>
            </div>
        </div>
        <!-- Grid of Terms -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
            <!-- Term Card 1 -->
            <div
                class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                            API</h3>
                        <button class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                        </button>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                        Application Programming Interface. A set of rules and protocols that allows different software
                        applications to communicate with each other.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">Development</span>
                    <span
                        class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/20 px-2.5 py-0.5 text-xs font-medium text-primary">Core</span>
                </div>
            </div>
            <!-- Term Card 2 -->
            <div
                class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                            Abstraction</h3>
                        <button class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                        </button>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                        The process of hiding complex implementation details and showing only the essential features of
                        the object to the user.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">OOP</span>
                </div>
            </div>
            <!-- Term Card 3 -->
            <div
                class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                            Algorithm</h3>
                        <button class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                        </button>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                        A step-by-step procedure or formula for solving a problem, often used for data processing and
                        automated reasoning.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">Computer
                        Science</span>
                </div>
            </div>
            <!-- Term Card 4 -->
            <div
                class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                            Array</h3>
                        <button class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                        </button>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                        A data structure consisting of a collection of elements, each identified by at least one array
                        index or key.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">Data
                        Structures</span>
                </div>
            </div>
            <!-- Term Card 5 -->
            <div
                class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                            Asynchronous</h3>
                        <button class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                        </button>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                        Events that happen independently of the main program flow and do not block the execution of
                        subsequent code.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">Programming</span>
                    <span
                        class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/20 px-2.5 py-0.5 text-xs font-medium text-primary">Advanced</span>
                </div>
            </div>
            <!-- Term Card 6 -->
            <div
                class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                            Authentication</h3>
                        <button class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                        </button>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                        The process of verifying the identity of a user, device, or system.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">Security</span>
                </div>
            </div>
            <!-- Term Card 7 -->
            <div
                class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                            Automation</h3>
                        <button class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                        </button>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                        The use of technology to perform tasks with minimal human assistance.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">DevOps</span>
                </div>
            </div>
            <!-- Term Card 8 -->
            <div
                class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                            Availability</h3>
                        <button class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                        </button>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                        The percentage of time that a system is operational and accessible.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">Reliability</span>
                </div>
            </div>
            <!-- Term Card 9 -->
            <div
                class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                            AWS</h3>
                        <button class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                        </button>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                        Amazon Web Services. A comprehensive, evolving cloud computing platform provided by Amazon.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">Cloud</span>
                    <span
                        class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/20 px-2.5 py-0.5 text-xs font-medium text-primary">Service</span>
                </div>
            </div>
            <!-- Term Card 10 -->
            <div
                class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                            Agile</h3>
                        <button class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                        </button>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                        A software development methodology characterized by iterative development, where requirements
                        and solutions evolve through collaboration.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">Methodology</span>
                </div>
            </div>
            <!-- Term Card 11 -->
            <div
                class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                            AJAX</h3>
                        <button class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                        </button>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                        Asynchronous JavaScript and XML. A set of web development techniques using many web technologies
                        on the client side.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">Frontend</span>
                </div>
            </div>
            <!-- Term Card 12 -->
            <div
                class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300 cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                            Angular</h3>
                        <button class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">bookmark_add</span>
                        </button>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-4">
                        A platform and framework for building single-page client applications using HTML and TypeScript.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-400">Framework</span>
                </div>
            </div>
        </div>
        <!-- Pagination -->
        <div class="mt-12 flex items-center justify-center">
            <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
                <a class="relative inline-flex items-center rounded-l-md px-2 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 focus:z-20 focus:outline-offset-0"
                    href="#">
                    <span class="sr-only">Previous</span>
                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                </a>
                <a aria-current="page"
                    class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                    href="#">1</a>
                <a class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 focus:z-20 focus:outline-offset-0"
                    href="#">2</a>
                <a class="relative hidden items-center px-4 py-2 text-sm font-semibold text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 md:inline-flex focus:z-20 focus:outline-offset-0"
                    href="#">3</a>
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 ring-1 ring-inset ring-slate-300 dark:ring-slate-700 focus:outline-offset-0">...</span>
                <a class="relative hidden items-center px-4 py-2 text-sm font-semibold text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 md:inline-flex focus:z-20 focus:outline-offset-0"
                    href="#">8</a>
                <a class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-900 dark:text-white ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 focus:z-20 focus:outline-offset-0"
                    href="#">9</a>
                <a class="relative inline-flex items-center rounded-r-md px-2 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 focus:z-20 focus:outline-offset-0"
                    href="#">
                    <span class="sr-only">Next</span>
                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                </a>
            </nav>
        </div>
    </div>
</main>
@endsection
