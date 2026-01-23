@extends('components.layout.app')

@section('title', 'Koleksi Kitab - ' . site_name())

@section('og_title', 'Koleksi Kitab - ' . site_name())
@section('og_description', 'Kumpulan kitab-kitab hikmah dan nasihat dari ulama salaf')
@section('og_url', url()->current())
@section('og_image', asset(site_setting('site_logo', 'img/icon_ABBI.png')))

@section('header-content')
    @include('components.layout.header')
@endsection

@section('content')
    <div class="content bg-gray-50 min-h-screen">
        {{-- Hero Section --}}
        <div class="bg-gradient-to-br from-primary via-gray-800 to-gray-900 text-white pt-8 pb-12 px-4 relative overflow-hidden">
            <div class="relative z-10 text-center max-w-7xl mx-auto">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 backdrop-blur-sm rounded-2xl mb-4 shadow-lg border border-white/10">
                    <i class="fas fa-book-quran text-3xl text-secondary"></i>
                </div>
                <h1 class="text-2xl font-bold mb-2 tracking-tight">Koleksi Kitab</h1>
                <p class="text-xs text-gray-400 max-w-sm mx-auto leading-relaxed">
                    Kumpulan kitab-kitab hikmah dan nasihat dari ulama salaf
                </p>
                <div class="flex justify-center gap-4 mt-6 text-xs">
                    <div class="bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full border border-white/10">
                        <i class="fas fa-book mr-1.5 text-secondary"></i> {{ count($kitabs) }} Kitab
                    </div>
                </div>
            </div>
        </div>

        {{-- Kitab List --}}
        <div class="px-4 py-8 pb-32">
            <div class="max-w-7xl mx-auto">
                <header class="mb-6">
                    <h2 class="text-xl font-bold text-primary flex items-center gap-2">
                        <i class="fas fa-book-open text-secondary"></i>
                        Pilih Kitab
                    </h2>
                    <div class="h-1 w-16 bg-secondary rounded-full mt-2"></div>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($kitabs as $kitab)
                        <a href="{{ route('home.kitab.show', $kitab->slug) }}"
                            class="block bg-white rounded-xl shadow-sm border border-gray-100 hover:border-secondary hover:shadow-md transition-all duration-300 overflow-hidden group h-full">
                            
                            {{-- Cover Image --}}
                            @if($kitab->cover_image)
                            <div class="aspect-[16/9] overflow-hidden">
                                <img src="{{ asset('storage/' . $kitab->cover_image) }}" 
                                     alt="{{ $kitab->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            @else
                            <div class="aspect-[16/9] bg-gradient-to-br from-primary via-gray-800 to-gray-900 flex items-center justify-center">
                                <i class="fas fa-book-quran text-5xl text-secondary/50"></i>
                            </div>
                            @endif
                            
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-800 text-lg group-hover:text-primary transition-colors mb-1">
                                    {{ $kitab->name }}
                                </h3>
                                
                                @if($kitab->author)
                                <p class="text-xs text-secondary font-medium mb-2">
                                    <i class="fas fa-user-pen mr-1"></i> {{ $kitab->author }}
                                </p>
                                @endif
                                
                                @if($kitab->description)
                                <p class="text-xs text-gray-500 line-clamp-2 mb-3">
                                    {{ Str::limit(strip_tags($kitab->description), 100) }}
                                </p>
                                @endif
                                
                                <div class="flex items-center justify-between text-[10px] text-gray-400">
                                    <span>
                                        <i class="fas fa-book-open mr-1 text-secondary"></i>
                                        {{ $kitab->chapters_count }} Bab
                                    </span>
                                    <span class="text-secondary font-medium group-hover:translate-x-1 transition-transform">
                                        Baca <i class="fas fa-arrow-right ml-1"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full text-center py-10 text-gray-500 bg-white rounded-xl border border-dashed border-gray-300">
                            <i class="fas fa-book text-5xl mb-3 text-gray-300"></i>
                            <p class="text-lg font-semibold">Belum ada kitab.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
