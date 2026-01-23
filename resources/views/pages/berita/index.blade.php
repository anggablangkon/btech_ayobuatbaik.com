@extends('components.layout.app')

@section('title', 'Berita & Artikel - ' . site_name())

@section('og_title', 'Berita & Artikel - ' . site_name())
@section('og_description', 'Berita & Artikel - ' . site_name())
@section('og_url', url()->current())
@section('og_image', asset(site_setting('site_logo', 'img/icon_ABBI.png')))

@section('header-content')
    @include('components.layout.header')
@endsection

@section('content')
    <div class="content">
        <div class="bg-primary text-white pt-6 pb-8 px-4">
            <div class="flex justify-center items-center">
                <h1 class="text-2xl font-bold">Berita & Kegiatan Terbaru</h1>
            </div>
        </div>

        <div class="p-4 pb-24">
            {{-- Berita Card List --}}
            @forelse ($beritas as $berita)
                <a href="{{ route('home.berita.show', $berita->slug) }}"
                    class="berita-card bg-white overflow-hidden shadow-md flex items-stretch h-28 mb-4 rounded-xl transition-all duration-300 hover:shadow-lg hover:scale-[1.01]">

                    <div class="w-1/2 h-full flex-shrink-0 overflow-hidden">
                        {{-- Asumsi kolom 'gambar' ada di model Berita --}}
                        <img src="{{ asset('storage/' . $berita->gambar) }}" class="w-full h-full object-cover"
                            alt="{{ $berita->judul }}" loading="lazy">
                    </div>

                    <div class="py-2 px-3 flex flex-col justify-between w-full">
                        <div>
                            <h3 class="text-xs font-bold text-primary leading-snug line-clamp-2">
                                {{ $berita->judul }}
                            </h3>
                            {{-- Asumsi kolom 'short_description' atau 'excerpt' ada di model Berita --}}
                            <p class="text-gray-600 text-[10px] mt-0.5 line-clamp-1">
                                {{ $berita->deskripsi_singkat ?? Str::limit(strip_tags($berita->content), 50) }}
                            </p>
                        </div>

                        <div class="mt-1">
                            <span class="text-[10px] text-gray-500 flex items-center gap-1">
                                <i class="far fa-clock"></i>
                                {{ $berita->tanggal->format('d F Y') }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-10 text-gray-500">
                    <i class="fas fa-newspaper text-5xl mb-3 text-gray-300"></i>
                    <p class="text-lg font-semibold">Belum ada berita yang tersedia saat ini.</p>
                </div>
            @endforelse

            {{-- Pagination Links --}}
            <div class="mt-8">
                {{ $beritas->links('components.paginate.pagination-simple') }}
            </div>

        </div>
    </div>
@endsection
