<header class="relative text-white overflow-hidden pt-2 pb-8">
    <div class="absolute top-0 left-0 right-0 z-0 bg-primary h-24 md:h-28"></div>
    <div class="relative w-full z-10 overflow-hidden">
        <div class="slider-track flex h-auto gap-3 px-4" style="transition: transform 0.5s ease-in-out;"> {{-- ✅ Kembali ke px-4 --}}
            {{-- Clone slides terakhir di awal --}}
            @foreach ($sliders as $slide)
                @php
                    $fallbackImageUrl = 'https://placehold.co/1600x900/4CAF50/FFFFFF?text=' . urlencode(site_setting('site_name_highlight', 'Slider') . ' ' . site_setting('site_name_rest', 'Image'));
                    $imageUrl = $slide->gambar ? asset('storage/' . $slide->gambar) : $fallbackImageUrl;
                @endphp
                <a href="{{ $slide->url ?? '#' }}" data-slide-clone="head"
                    class="slider-clone min-w-[90%] flex-shrink-0 relative flex items-center justify-center rounded-xl overflow-hidden aspect-[16/9] bg-gray-200"> {{-- ✅ Kembali ke min-w-[90%] --}}
                    <img src="{{ $imageUrl }}" alt="{{ $slide->alt_text ?? 'Slider Image' }}"
                        class="absolute inset-0 w-full h-full object-cover" loading="lazy" decoding="async"
                        onerror="this.onerror=null; this.src='{{ $fallbackImageUrl }}';">
                </a>
            @endforeach

            {{-- Slides asli --}}
            @foreach ($sliders as $slide)
                @php
                    $fallbackImageUrl = 'https://placehold.co/1600x900/4CAF50/FFFFFF?text=' . urlencode(site_setting('site_name_highlight', 'Slider') . ' ' . site_setting('site_name_rest', 'Image'));
                    $imageUrl = $slide->gambar ? asset('storage/' . $slide->gambar) : $fallbackImageUrl;
                @endphp
                <a href="{{ $slide->url ?? '#' }}" data-slide-index="{{ $loop->index }}"
                    class="slider-item min-w-[90%] flex-shrink-0 relative flex items-center justify-center rounded-xl overflow-hidden aspect-[16/9] bg-gray-200"> {{-- ✅ Kembali ke min-w-[90%] --}}
                    <img src="{{ $imageUrl }}" alt="{{ $slide->alt_text ?? 'Slider Image' }}"
                        class="absolute inset-0 w-full h-full object-cover" 
                        @if($loop->first) fetchpriority="high" @else loading="lazy" @endif
                        decoding="async"
                        onerror="this.onerror=null; this.src='{{ $fallbackImageUrl }}';">
                </a>
            @endforeach

            {{-- Clone slides pertama di akhir --}}
            @foreach ($sliders as $slide)
                @php
                    $fallbackImageUrl = 'https://placehold.co/1600x900/4CAF50/FFFFFF?text=' . urlencode(site_setting('site_name_highlight', 'Slider') . ' ' . site_setting('site_name_rest', 'Image'));
                    $imageUrl = $slide->gambar ? asset('storage/' . $slide->gambar) : $fallbackImageUrl;
                @endphp
                <a href="{{ $slide->url ?? '#' }}" data-slide-clone="tail"
                    class="slider-clone min-w-[90%] flex-shrink-0 relative flex items-center justify-center rounded-xl overflow-hidden aspect-[16/9] bg-gray-200"> {{-- ✅ Kembali ke min-w-[90%] --}}
                    <img src="{{ $imageUrl }}" alt="{{ $slide->alt_text ?? 'Slider Image' }}"
                        class="absolute inset-0 w-full h-full object-cover" loading="lazy" decoding="async"
                        onerror="this.onerror=null; this.src='{{ $fallbackImageUrl }}';">
                </a>
            @endforeach
        </div>
    </div>
</header>

<div class="flex justify-center space-x-2 z-30 mt-1 relative px-4">
    @foreach ($sliders as $index => $slide)
        <button class="slider-dot w-3 h-3 rounded-full bg-gray-400 opacity-50 {{ $index === 0 ? 'active' : '' }}"></button>
    @endforeach
</div>