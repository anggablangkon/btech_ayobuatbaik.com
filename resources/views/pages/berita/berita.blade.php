@extends('components.layout.app')

{{-- Menggunakan $berita->judul (asumsi ini judulnya) untuk title halaman --}}
@section('title', $berita->judul)

@section('og_title', $berita->judul)
@section('og_description', $berita->deskripsi_singkat)
@section('og_url', url()->current())
@section('og_image', asset('storage/' . $berita->gambar))

@section('header-content')
    @include('components.layout.header')
@endsection

@section('content')
    <div class="content bg-white pb-24">

        <div class="w-full h-56 overflow-hidden">
            {{-- Menggunakan $berita->gambar untuk path gambar utama --}}
            <img src="{{ asset('storage/' . $berita->gambar) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover">
        </div>

        <div class="p-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                {{ $berita->judul }}
            </h1>

            <div class="flex items-center gap-4 text-sm text-gray-500 mb-6">
                {{-- Menampilkan tanggal publikasi --}}
                <span class="flex items-center gap-1">
                    <i class="far fa-calendar-alt"></i>
                    {{-- Asumsi $berita->tanggal adalah Carbon instance --}}
                    {{ $berita->tanggal->format('d F Y') }}
                </span>

                {{-- Anda bisa tambahkan Penulis di sini jika ada kolomnya --}}
                {{-- <span class="flex items-center gap-1">
                    <i class="fas fa-user-circle"></i>
                    {{ $berita->author->name ?? 'Admin' }}
                </span> --}}
            </div>

            {{-- Menggunakan 'content' atau 'deskripsi' untuk isi penuh berita --}}
            <div class="prose max-w-none text-gray-800 leading-relaxed">
                {!! $berita->konten !!} {{-- Asumsi kolom 'content' berisi HTML/LongText --}}
            </div>

        </div>

        {{-- Horizontal Rule --}}
        <div class="my-8 border-t border-gray-100"></div>

        @if(isset($relatedBeritas) && $relatedBeritas->count() > 0)
            <div class="px-4">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Berita Terkait</h2>
                <div class="space-y-4">
                    @foreach ($relatedBeritas as $related)
                        <a href="{{ route('home.berita.show', $related->slug) }}"
                            class="flex items-start gap-3 border-b pb-3 last:border-b-0 last:pb-0 hover:bg-gray-50 p-2 -mx-2 rounded-lg transition-colors">

                            <div class="w-20 h-16 flex-shrink-0 overflow-hidden rounded-lg">
                                {{-- Gambar untuk berita terkait --}}
                                <img src="{{ asset('storage/' . $related->gambar) }}" alt="{{ $related->judul }}" class="w-full h-full object-cover">
                            </div>

                            <div>
                                <h4 class="text-sm font-semibold line-clamp-2 text-gray-900 hover:text-primary transition">
                                    {{ $related->judul }}
                                </h4>
                                <span class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                    <i class="far fa-clock"></i>
                                    {{ $related->tanggal->diffForHumans() }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
