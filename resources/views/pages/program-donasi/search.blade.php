@extends('components.layout.app')

@section('title', 'Hasil Pencarian - ' . site_name())

@section('og_title', 'Hasil Pencarian - ' . site_name())
@section('og_description', 'Hasil Pencarian - ' . site_name())
@section('og_url', url()->current())
@section('og_image', asset(site_setting('site_logo', 'img/icon_ABBI.png')))

@section('header-content')
    @include('components.layout.header-with-search')
@endsection

@section('content')
    <div class="bg-white p-4 mt-2">
        <h3 class="font-bold text-lg mb-3">
            Hasil Pencarian:
            <span class="text-secondary">"{{ $keyword }}"</span>
        </h3>

        @if ($programs->count() > 0)
            <div class="space-y-4">
                @foreach ($programs as $item)
                    @php
                        $progress =
                            $item->target_amount > 0 ? ($item->collected_amount / $item->target_amount) * 100 : 0;
                        $progress = min(100, $progress);
                    @endphp

                    <a href="{{ route('home.program.show', $item->slug) }}"
                        class="flex border rounded-lg overflow-hidden hover:shadow-md transition">
                        <div class="w-24 h-24 flex-shrink-0">
                            <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->title }}"
                                class="w-full h-full object-cover" loading="lazy" />
                        </div>
                        <div class="p-3 flex-1">
                            <h4 class="font-bold text-sm line-clamp-2">{{ $item->title }}</h4>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Terkumpul: Rp {{ number_format($item->collected_amount, 0, ',', '.') }}</span>
                                <span>
                                    Sisa hari:
                                    {{ $item->end_date ? floor(max(0, \Carbon\Carbon::now()->diffInDays($item->end_date, false))) : '∞' }}
                                </span>
                            </div>
                            <div class="progress-bar-simple bg-gray-200 mt-2">
                                <div class="progress-fill-simple bg-secondary" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $programs->links() }}
            </div>
        @else
            <p class="text-gray-500 text-sm">Tidak ada program yang cocok dengan kata kunci tersebut.</p>
        @endif
    </div>
    <div class="pb-24"></div>
@endsection
