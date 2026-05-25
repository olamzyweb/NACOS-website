@extends('layouts.app')

@section('title', 'Home')
@section('meta_description', 'Vote for your favorite candidates in the NACOS Day Awards. Support your peers and make your voice count!')

@section('content')
{{-- Hero Section - Full Screen --}}
<section class="relative overflow-hidden bg-[#08111d] text-white min-h-[92vh] flex items-center py-12">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <img src="{{ asset('images/awards_gala.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-15" alt="NACOS Day Awards">
        <div class="absolute inset-0 bg-gradient-to-t from-[#08111d] via-transparent to-transparent opacity-90"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid items-center gap-10 lg:grid-cols-[minmax(0,1.2fr)_minmax(350px,0.8fr)]">
            <div class="max-w-4xl text-center lg:text-left">
            {{-- Badge - White Text --}}
            <div class="hero-fade-up mb-8 inline-flex items-center gap-2 rounded-full border border-primary-500/40 bg-primary-500/30 px-4 py-1.5 text-xs font-bold text-white shadow-xl backdrop-blur-md mx-auto lg:mx-0 uppercase tracking-widest">
                <span class="w-1.5 h-1.5 bg-primary-400 rounded-full animate-pulse"></span>
                VOTING IS {{ $votingEnabled ? 'LIVE' : 'SOON' }}
            </div>

            <h1 class="font-black text-white uppercase tracking-tight" style="font-size: clamp(3rem, 8vw, 6.5rem); line-height: 1; margin-bottom: 1.5rem;">
          NACOS AWARDS
          <span class="block text-primary italic normal-case" style="font-size: clamp(2.5rem, 6vw, 4.5rem);">Day 2026</span>
        </h1>

            <p class="hero-fade-up max-w-2xl text-lg leading-relaxed text-white/70 mb-10 mx-auto lg:mx-0 font-medium">
                Celebrating the brightest stars of NACOS LASUSTECH. Support your peers with your votes!
            </p>

            <div class="hero-fade-up flex flex-col items-center lg:items-start gap-4 sm:flex-row sm:justify-center lg:justify-start mb-10">
                <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-10 py-4 text-lg font-bold text-white shadow-2xl shadow-primary-600/30 transition-all hover:bg-primary-700 hover:-translate-y-1">
                    Start Voting
                </a>
                <a href="{{ route('leaderboard') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-8 py-4 text-lg font-semibold text-white transition-all hover:bg-white/10 hover:-translate-y-1">
                    Leaderboard
                </a>
            </div>
            </div>

            <div class="hidden lg:block relative">
                <div class="relative overflow-hidden rounded-[2.5rem] border border-white/10 bg-white/5 p-10 backdrop-blur-2xl shadow-2xl">
                    <div class="mb-8 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 rounded-2xl bg-primary-600/20 flex items-center justify-center text-primary-400 border border-white/5 shadow-inner">
                                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-black text-white uppercase tracking-tight">Today's Event</p>
                                <p class="text-xs text-white/40">LASUSTECH Computer Science</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5">
                        <div class="rounded-2xl bg-white/5 p-5 border border-white/5">
                            <p class="text-xs font-bold text-white/40 uppercase tracking-widest mb-2">Status</p>
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-bold text-white">Voting Active</p>
                                <span class="h-2.5 w-2.5 rounded-full bg-green-500 animate-pulse shadow-sm shadow-green-500/50"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-2xl bg-primary-600/20 p-5 border border-white/5 text-center">
                                <p class="text-xs font-bold text-primary-400 uppercase tracking-widest">Nominees</p>
                                <p class="text-2xl font-black text-white">120+</p>
                            </div>
                            <div class="rounded-2xl bg-white/10 p-5 border border-white/10 text-center">
                                <p class="text-xs font-bold text-white/40 uppercase tracking-widest">Awards</p>
                                <p class="text-2xl font-black text-white">32</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Featured Categories --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 bg-surface-50">
    <div class="text-center mb-16">
        <h2 class="text-4xl font-black mb-4 text-surface-900 uppercase tracking-tight">Award Categories</h2>
        <p class="text-surface-500 text-lg max-w-2xl mx-auto">Explore categories and cast your votes for excellence</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($categories as $category)
        <a href="{{ route('categories.show', $category) }}" class="group relative bg-white rounded-[2rem] border border-surface-200 overflow-hidden transition-all hover:shadow-xl hover:border-primary-200">
            <div class="h-36 bg-surface-100 relative overflow-hidden">
                @if($category->image)
                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent opacity-60"></div>
            </div>
            <div class="p-8">
                <h3 class="font-bold text-xl mb-3 text-surface-900 group-hover:text-primary-600 transition-colors leading-tight">{{ $category->name }}</h3>
                <p class="text-surface-500 text-sm line-clamp-2 mb-6 leading-relaxed">{{ $category->description }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-primary-600 bg-primary-50 px-3 py-1.5 rounded-full uppercase tracking-widest">{{ $category->candidates_count }} Nominees</span>
                    <div class="h-10 w-10 rounded-full bg-surface-50 flex items-center justify-center text-surface-300 group-hover:bg-primary-600 group-hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endsection
