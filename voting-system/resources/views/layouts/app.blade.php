<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Vote for your favorite candidates in the NACOS Awards. Support your peers and make your voice count!')">

    <title>@yield('title', 'NACOS Day Awards') — {{ \App\Models\Setting::getSiteTitle() }}</title>

    {{-- Canonical URL --}}
    @hasSection('canonical_url')
        <link rel="canonical" href="@yield('canonical_url')">
    @endif

    {{-- Open Graph Tags --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', 'NACOS Awards — Vote for your favorites')">
    <meta property="og:description" content="@yield('og_description', 'Vote for your favorite candidates in the NACOS Awards. Support your peers and make your voice count!')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">
    <meta property="og:site_name" content="{{ \App\Models\Setting::getSiteTitle() }}">

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('og_title', 'NACOS Awards — Vote for your favorites')">
    <meta name="twitter:description" content="@yield('og_description', 'Vote for your favorite candidates in the NACOS Awards.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.png'))">

    {{-- Structured Data --}}
    @stack('structured_data')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-surface-900 text-surface-900 dark:bg-surface-950 dark:text-surface-100 transition-colors duration-300">

    {{-- Toast Notifications --}}
    <div x-data="toastNotification()" x-init="init()" id="toast-container" class="fixed top-4 right-4 z-[100] space-y-3">
        <template x-if="show">
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 :class="{
                          'bg-primary-600': type === 'success',
                    'bg-red-500': type === 'error',
                    'bg-amber-500': type === 'warning',
                    'bg-blue-500': type === 'info'
                 }"
                 class="px-6 py-4 rounded-xl text-white shadow-2xl flex items-center gap-3 min-w-[320px] backdrop-blur-sm">
                <template x-if="type === 'success'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="type === 'error'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </template>
                <span x-text="message" class="text-sm font-medium"></span>
                <button @click="show = false" class="ml-auto hover:bg-white/20 rounded-full p-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    {{-- Navigation --}}
    <nav class="sticky top-0 z-50 border-b border-surface-700/50 bg-surface-900/80 backdrop-blur-2xl dark:border-surface-700/50 dark:bg-surface-950/80" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center bg-white/95 ring-1 ring-surface-900/5 shadow-lg shadow-surface-900/5 group-hover:shadow-primary-500/20 transition-all">
                            <img src="{{ asset('images/nacos-logo.png') }}" alt="NACOS" class="w-8 h-8 object-contain" />
                        </div>
                        <span class="text-xl font-extrabold tracking-tight text-white">NACOS Day Awards</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center gap-2 lg:gap-3">
                    <a href="{{ route('home') }}" class="px-4 py-2.5 rounded-full text-sm font-semibold transition-all duration-200 hover:bg-white/10 hover:text-white {{ request()->routeIs('home') ? 'text-primary-400 bg-primary-500/10 ring-1 ring-primary-600/10 shadow-sm' : 'text-surface-400' }}">Home</a>
                    <a href="{{ route('categories.index') }}" class="px-4 py-2.5 rounded-full text-sm font-semibold transition-all duration-200 hover:bg-white/10 hover:text-white {{ request()->routeIs('categories.*') ? 'text-primary-400 bg-primary-500/10 ring-1 ring-primary-600/10 shadow-sm' : 'text-surface-400' }}">Categories</a>
                    <a href="{{ route('leaderboard') }}" class="px-4 py-2.5 rounded-full text-sm font-semibold transition-all duration-200 hover:bg-white/10 hover:text-white {{ request()->routeIs('leaderboard') ? 'text-primary-400 bg-primary-500/10 ring-1 ring-primary-600/10 shadow-sm' : 'text-surface-400' }}">Leaderboard</a>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-full text-sm font-semibold text-primary-700 hover:bg-white/80 transition-all">Admin</a>
                        @endif
                    @endauth

                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="ml-2 rounded-full border border-surface-900/5 bg-white/75 p-2.5 shadow-sm transition-colors hover:bg-white dark:border-surface-700 dark:bg-surface-800/80 dark:hover:bg-surface-800" title="Toggle dark mode">
                        <svg x-show="!darkMode" class="w-5 h-5 text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>

                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2.5 rounded-full text-sm font-semibold text-surface-500 transition-colors hover:bg-white/80 hover:text-red-500">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="ml-2 inline-flex items-center rounded-full bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary-600/20 transition-all hover:-translate-y-0.5 hover:bg-primary-700 hover:shadow-xl hover:shadow-primary-600/30">Admin Login</a>
                    @endauth
                </div>

                <div class="flex items-center md:hidden gap-2">
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="rounded-full border border-surface-900/5 bg-white/75 p-2.5 shadow-sm hover:bg-white dark:border-surface-700 dark:bg-surface-800">
                        <svg x-show="!darkMode" class="w-5 h-5 text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                    <button @click="mobileOpen = !mobileOpen" class="rounded-full border border-surface-900/5 bg-white/75 p-2.5 shadow-sm hover:bg-white dark:border-surface-700 dark:bg-surface-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak x-transition class="md:hidden border-t border-surface-700 bg-surface-900/95 backdrop-blur-2xl dark:bg-surface-950">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('home') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold transition hover:bg-white/80">Home</a>
                <a href="{{ route('categories.index') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold transition hover:bg-white/80">Categories</a>
                <a href="{{ route('leaderboard') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold transition hover:bg-white/80">Leaderboard</a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-primary-600 transition hover:bg-white/80">Admin Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl px-4 py-3 text-left text-sm font-semibold text-red-500 transition hover:bg-white/80">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block rounded-2xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-600/20 transition hover:bg-primary-700">Admin Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-surface-900 dark:bg-surface-950 text-surface-400 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-white">
                            <img src="{{ asset('images/nacos-logo.png') }}" alt="NACOS" class="w-8 h-8 object-contain" />
                        </div>
                        <span class="text-xl font-bold text-white">NACOS Vote</span>
                    </div>
                    <p class="text-sm leading-relaxed">The official voting platform for NACOS Day Awards. Cast your votes and support your favorite candidates.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-primary-400 transition">Home</a></li>
                        <li><a href="{{ route('categories.index') }}" class="hover:text-primary-400 transition">Categories</a></li>
                        <li><a href="{{ route('leaderboard') }}" class="hover:text-primary-400 transition">Leaderboard</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m22 6-10 7L2 6" />
                            </svg>
                            nacos@university.edu.ng
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.81.3 1.6.54 2.36a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.72-1.11a2 2 0 0 1 2.11-.45c.76.24 1.55.42 2.36.54a2 2 0 0 1 1.72 2.03z" />
                            </svg>
                            +234 800 000 0000
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-surface-800 mt-8 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} NACOS Day Awards. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function toastNotification() {
            return {
                show: false,
                message: '',
                type: 'success',
                init() {
                    @if(session('success'))
                        this.showToast('{{ session("success") }}', 'success');
                    @endif
                    @if(session('error'))
                        this.showToast('{{ session("error") }}', 'error');
                    @endif
                },
                showToast(message, type = 'success') {
                    this.message = message;
                    this.type = type;
                    this.show = true;
                    setTimeout(() => { this.show = false; }, 5000);
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
