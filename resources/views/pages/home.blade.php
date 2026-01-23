@extends('components.layout.app')

@section('title', site_name() . ' - ' . site_setting('site_description', 'Platform Donasi Digital'))

@section('header-content')
    @include('components.layout.header-with-search')
    @include('components.sections.hero-sliders')
@endsection

@section('content')
    <!-- Pilihan Section -->
    <section class="px-4 py-8" aria-labelledby="featured-section">
        <div class="max-w-7xl mx-auto">
            <header class="mb-6">
                <h2 id="featured-section" class="text-xl font-bold text-primary flex items-center gap-2">
                    <i class="fas fa-star text-secondary"></i>
                    Ayo Berbuat Baik
                </h2>
                <div class="h-1 w-16 bg-secondary rounded-full mt-2"></div>
            </header>
            @forelse ($featuredPrograms as $programPilihan)
                <div class="grid grid-cols-1 gap-1 mt-4">
                    <a href="{{ route('home.program.show', $programPilihan->slug) }}" class="block">
                        <div class="bg-white overflow-hidden shadow-md card-hover-effect flex items-stretch h-28">
                            <div class="w-1/2 h-full flex-shrink-0 overflow-hidden">
                                <img src="{{ asset('storage/' . $programPilihan->gambar) }}"
                                    alt="{{ $programPilihan->title }}" class="w-full h-full object-cover"
                                    @if($loop->first) fetchpriority="high" @else loading="lazy" @endif 
                                    decoding="async">
                            </div>

                            <div class="py-2 px-3 flex flex-col justify-between w-full">
                                <div>
                                    <h3 class="text-xs font-semibold text-gray-800 flex items-center gap-1p">
                                        {{ strlen($programPilihan->title) > 37 ? substr($programPilihan->title, 0, 37) . '...' : $programPilihan->title }}
                                        @if ($programPilihan->verified)
                                            <span class="ml-1 inline-flex items-center">
                                                {{-- SVG Icon Verifikasi ala Instagram --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="currentColor" class="w-4 h-4 text-blue-500">
                                                    <path fill-rule="evenodd"
                                                        d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                    </h3>
                                </div>
                                <div class="mt-1">
                                    <div class="flex justify-between text-xs text-gray-800 mb-0.5">
                                        <span class="font-semibold text-[10px]">Terkumpul: Rp
                                            {{ number_format($programPilihan->collected_amount, 0, ',', '.') }}</span>
                                        <span class="text-gray-500 text-[10px]">
                                            Sisa:
                                            {{ $programPilihan->end_date
                                                ? floor(max(0, \Carbon\Carbon::now()->diffInDays($programPilihan->end_date, false))) . ' hari'
                                                : '∞' }}
                                        </span>
                                    </div>
                                    @php
                                        $progress =
                                            $programPilihan->target_amount > 0
                                                ? ($programPilihan->collected_amount / $programPilihan->target_amount) *
                                                    100
                                                : 0;
                                    @endphp
                                    <div class="progress-bar-simple">
                                        <div class="progress-fill-simple bg-secondary" style="width: {{ $progress }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-xs text-gray-500">Belum ada program pilihan.</p>
            @endforelse
        </div>
        </div>
    </section>

    {{-- Kategori Section --}}
    <section class="px-4 py-6 bg-white" id="kategori" aria-labelledby="category-section">
        <div class="max-w-7xl mx-auto">
            <header class="text-center mb-8">
                <h2 id="category-section" class="text-xl font-bold text-primary mb-2">Kategori Program Ayobuatbaik</h2>
                <p class="text-sm text-gray-600">Pilih kategori sesuai kebutuhan Anda</p>
            </header>
            <div class="grid grid-cols-4 gap-3 mt-6 text-center">
                <!-- Tombol Semua -->
                <div class="kategori-btn flex flex-col items-center cursor-pointer space-y-1" data-kat="all"
                    onclick="filterProgram('all', this)">
                    <div
                        class="icon-wrapper w-10 h-10 flex items-center justify-center rounded-full bg-orange-50 text-gray-700 border border-orange-200 transition-colors duration-300">
                        <i class="fas fa-folder text-lg"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-700">Semua</span>
                </div>

                <!-- Loop 2 kategori -->
                @foreach ($kategori->take(2) as $kat)
                    <div class="kategori-btn flex flex-col items-center cursor-pointer space-y-1"
                        data-kat="{{ $kat->slug }}" onclick="filterProgram('{{ $kat->slug }}', this)">
                        <div
                            class="icon-wrapper w-10 h-10 flex items-center justify-center rounded-full bg-orange-50 text-gray-700 border border-orange-200 transition-colors duration-300">
                            <i class="fas fa-folder text-lg"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-700">{{ $kat->name }}</span>
                    </div>
                @endforeach

                <!-- Tombol Lainnya -->
                <div class="kategori-btn flex flex-col items-center cursor-pointer space-y-1"
                    onclick="openKategoriModal(this)">
                    <div
                        class="icon-wrapper w-10 h-10 flex items-center justify-center rounded-full bg-orange-50 text-gray-700 border border-orange-200 transition-colors duration-300">
                        <i class="fas fa-ellipsis-h text-lg"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-700">Lainnya</span>
                </div>

            </div>
        </div>
    </section>

    <!-- Program Section -->
    <section class="px-4 pt-6 pb-12 bg-blue-50/30" id="program" aria-labelledby="program-section">
        <div class="max-w-7xl mx-auto">
            <header class="mb-6">
                <h2 id="program-section" class="text-xl font-bold text-primary flex items-center gap-2">
                    <i class="fas fa-hand-holding-heart text-secondary"></i>
                    Program Donasi
                </h2>
                <div class="h-1 w-16 bg-secondary rounded-full mt-2"></div>
            </header>
        <div class="grid grid-cols-1 gap-7">
            @foreach ($otherPrograms as $program)
                @php
                    $terkumpul = $program->collected_amount ?? 0;
                    $target = $program->target_amount ?? 0;
                    $progress = $target > 0 ? min(100, ($terkumpul / $target) * 100) : 0;
                    $sisaHari = $program->end_date
                        ? floor(max(0, now()->diffInDays($program->end_date, false))) . ' hari'
                        : '∞';
                @endphp

                <a href="{{ route('home.program.show', $program->slug) }}" class="block">
                    <div class="program-card bg-white overflow-hidden shadow-md flex items-stretch h-28"
                        data-category="{{ $program->kategori->slug }}">
                        <div class="w-1/2 h-full flex-shrink-0 overflow-hidden">
                            <img src="{{ asset('storage/' . $program->gambar) }}" alt="{{ $program->title }}"
                                class="w-full h-full object-cover" loading="lazy" decoding="async">
                        </div>

                        <div class="py-2 px-3 flex flex-col justify-between w-full">
                            <h3 class="text-xs font-semibold text-gray-800 flex items-center gap-1">
                                {{ strlen($program->title) > 37 ? substr($program->title, 0, 37) . '...' : $program->title }}
                                @if ($program->verified)
                                    <span class="ml-1 inline-flex items-center">
                                        {{-- SVG Icon Verifikasi ala Instagram --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-4 h-4 text-blue-500">
                                            <path fill-rule="evenodd"
                                                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @endif
                            </h3>

                            <div class="mt-1">
                                <div class="flex justify-between text-xs text-gray-800 mb-0.5">
                                    <span class="font-semibold text-[10px]">
                                        Terkumpul: Rp {{ number_format($terkumpul, 0, ',', '.') }}
                                    </span>
                                    <span class="text-gray-500 text-[10px]">
                                        Sisa hari: {{ $sisaHari }}
                                    </span>
                                </div>

                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-secondary h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

            <div class="text-center mt-8">
                <a href="{{ route('home.program') }}"
                    class="inline-flex items-center gap-2 bg-primary text-white font-semibold px-6 py-3 rounded-lg text-sm hover:bg-grayDark transition-all shadow-md hover:shadow-lg">
                    <span>Lihat Program Lainnya</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Modal Popup Kategori -->
    <div id="kategoriModal"
        class="hidden fixed inset-0 bg-black/60 flex items-end justify-center z-[9999] transition-all duration-300">

        <div class="bg-white rounded-t-2xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-gray-900">Pilih kategori program</h3>
                <button onclick="closeKategoriModal()">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-6 text-center">

                @foreach ($kategori as $kat)
                    <div class="modal-kategori-btn flex flex-col items-center cursor-pointer space-y-1"
                        data-kat="{{ $kat->slug }}"
                        onclick="filterProgram('{{ $kat->slug }}'); closeKategoriModal()">

                        <div
                            class="icon-wrapper w-14 h-14 flex items-center justify-center rounded-full bg-orange-50 text-gray-700 border border-orange-200 transition-colors duration-300">
                            <i class="fas fa-folder text-xl"></i>
                        </div>

                        <span class="text-xs mt-2 font-medium text-gray-700">{{ $kat->name }}</span>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <!-- DOA & DUKUNGAN SECTION -->
    @if($prayers->isNotEmpty())
    <section class="py-8 mt-8 bg-gradient-to-br from-primary via-gray-800 to-gray-900 mb-4 border-y-2 border-secondary/30 relative overflow-hidden" aria-labelledby="prayer-section-title">
        <!-- Decorative accent -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-secondary to-transparent"></div>
        
        <div class="px-4">
            <header class="mb-4">
                <h2 id="prayer-section-title" class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-hands-praying text-secondary"></i>
                    Doa & Dukungan
                </h2>
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-300">Pesan kebaikan dari para donatur</p>
                    <p class="text-[10px] text-secondary/80 flex items-center gap-1">
                        <i class="fas fa-hand-pointer"></i>
                        <span>Geser untuk melihat lebih banyak</span>
                    </p>
                </div>
            </header>
            
            <div class="flex overflow-x-auto space-x-3 pb-4 snap-x hide-scrollbar" role="list">
                @foreach($prayers as $prayer)
                <article class="min-w-[280px] h-[240px] bg-white p-4 rounded-xl border border-gray-100 shadow-sm snap-center hover:shadow-md transition-shadow flex flex-col" role="listitem">
                    <header class="flex items-start gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center text-secondary font-bold text-sm flex-shrink-0" aria-hidden="true">
                            {{ $prayer->donor_initial }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-gray-800 leading-tight">{{ $prayer->donor_name ?: 'Hamba Allah' }}</h3>
                            <p class="text-[10px] text-gray-500 mt-0.5">{{ \Carbon\Carbon::setLocale('id') && false ?: $prayer->created_at->diffForHumans() }}</p>
                        </div>
                    </header>
                    
                    <blockquote class="relative flex-1">
                        <p class="text-sm text-gray-700 italic leading-relaxed line-clamp-4">
                            {{ Str::limit($prayer->note, 120) }}
                        </p>
                    </blockquote>
                    
                    <footer class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-[10px] text-gray-500">
                            <span class="font-medium text-gray-700 truncate inline-block max-w-[200px] align-bottom">{{ Str::limit($prayer->program->title, 32) ?? 'Program' }}</span>
                        </p>
                    </footer>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif


    <!-- Features Section -->
    {{-- <section class="features-section py-8 rounded-2xl px-4">
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-primary mb-2">Fitur Lainnya ABBI</h2>
            <p class="text-gray-500 text-xs">
                Nikmati berbagai fitur menarik untuk mendukung ibadah dan aktivitas kebaikan Anda.
            </p>
        </div>

        <div class="grid grid-cols-4 gap-3">
            <a href="/quran" class="group flex flex-col items-center text-center space-y-1 transition-all duration-200">
                <div
                    class="w-12 h-12 flex items-center justify-center rounded-full group-hover:bg-secondary/10 border border-secondary/40 transition-colors">
                    <i class="fas fa-book-quran text-secondary text-base"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Al-Quran</span>
            </a>

            <a href="/nashohul-ibad"
                class="group flex flex-col items-center text-center space-y-1 transition-all duration-200">
                <div
                    class="w-12 h-12 flex items-center justify-center rounded-full group-hover:bg-secondary/10 border border-secondary/40 transition-colors">
                    <i class="fas fa-book text-secondary text-base"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Nashohul Ibad</span>
            </a>

            <a href="/doa" class="group flex flex-col items-center text-center space-y-1 transition-all duration-200">
                <div
                    class="w-12 h-12 flex items-center justify-center rounded-full group-hover:bg-secondary/10 border border-secondary/40 transition-colors">
                    <i class="fas fa-hands-praying text-secondary text-base"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Doa</span>
            </a>

            <a href="/webtoon" class="group flex flex-col items-center text-center space-y-1 transition-all duration-200">
                <div
                    class="w-12 h-12 flex items-center justify-center rounded-full group-hover:bg-secondary/10 border border-secondary/40 transition-colors">
                    <i class="fas fa-images text-secondary text-base"></i>
                </div>
                <span class="text-[11px] font-medium text-gray-700">Webtoon</span>
            </a>
        </div>
    </section> --}}

    <!-- Berita Section -->
    <section class="px-4 py-12 bg-gray-50 mb-4" id="berita" aria-labelledby="news-section">
        <div class="max-w-7xl mx-auto">
            <header class="mb-8">
                <h2 id="news-section" class="text-xl font-bold text-primary flex items-center gap-2">
                    <i class="fas fa-newspaper text-secondary"></i>
                    Berita & Artikel
                </h2>
                <div class="h-1 w-16 bg-secondary rounded-full mt-2"></div>
                <p class="text-gray-600 text-sm mt-3">Update terbaru program dan artikel inspiratif</p>
            </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse ($berita as $item)
                <div class="bg-white rounded-lg border border-gray-100 p-4 hover:border-secondary hover:shadow-md transition-all">
                    <div class="w-full h-32 rounded-lg overflow-hidden mb-3">
                        <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->judul }}"
                            class="w-full h-full object-cover" loading="lazy" decoding="async" />
                    </div>
                    <span
                        class="text-xs text-secondary font-medium">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</span>
                    <h3 class="text-sm font-bold text-primary mt-1 mb-2 line-clamp-2">{{ $item->judul }}</h3>
                    <p class="text-gray-600 text-xs line-clamp-2">{{ $item->deskripsi_singkat }}</p>
                    <a href=""
                        class="inline-block mt-2 text-secondary text-xs font-medium hover:text-goldDark transition-colors">
                        Baca selengkapnya
                    </a>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada berita untuk saat ini.</p>
            @endforelse
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('home.berita') }}"
                class="inline-flex items-center gap-2 text-secondary font-semibold text-sm hover:text-goldDark transition-colors">
                <span>Lihat Semua Berita</span>
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        </div>
    </section>



    @include('components.layout.footer')
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // =======================================================
            // 1️⃣ MODAL KATEGORI
            // =======================================================
            const kategoriModal = document.getElementById("kategoriModal");

            window.openKategoriModal = function() {
                if (kategoriModal) kategoriModal.classList.remove("hidden");
            };

            window.closeKategoriModal = function() {
                if (kategoriModal) kategoriModal.classList.add("hidden");
            };

            // =======================================================
            // 2️⃣ INFINITE SLIDER / BANNER (Alpine.js Style) - FIXED CENTER
            // =======================================================
            const sliderTrack = document.querySelector(".slider-track");
            const sliderDots = document.querySelectorAll(".slider-dot");
            const sliderItems = document.querySelectorAll(".slider-item");

            if (sliderTrack && sliderItems.length > 0) {
                const totalSlides = sliderItems.length;
                let currentSlide = 0;
                let internalIndex = totalSlides;
                let useTransition = true;
                let autoSlideInterval;
                let isTransitioning = false;

                // 🔥 Hitung lebar slide + gap dengan benar
                function getSlideWidth() {
                    const slide = sliderItems[0];
                    const trackStyle = getComputedStyle(sliderTrack);
                    const gap = parseFloat(trackStyle.gap) || 0;

                    return slide.offsetWidth + gap;
                }

                // 🔥 Hitung offset untuk centering (kompensasi padding kiri)
                function getCenterOffset() {
                    const containerWidth = sliderTrack.parentElement.offsetWidth;
                    const slide = sliderItems[0];
                    const slideWidth = slide.offsetWidth;
                    
                    const trackStyle = getComputedStyle(sliderTrack);
                    const paddingLeft = parseFloat(trackStyle.paddingLeft) || 0;

                    // Rumus centering: (Lebar Container - Lebar Slide) / 2
                    // Dikurangi paddingLeft karena track dimulai setelah padding
                    return (containerWidth - slideWidth) / 2 - paddingLeft;
                }

                // Update posisi track dengan centering
                function updateTrackPosition(withTransition = true) {
                    useTransition = withTransition;
                    const slideFullWidth = getSlideWidth(); // lebar slide + gap
                    const centerOffset = getCenterOffset();

                    // Hitung translateX
                    // Posisi slide = internalIndex * slideFullWidth
                    // Geser ke kiri sebanyak posisi slide, lalu tambah offset biar center
                    const translateX = -(internalIndex * slideFullWidth) + centerOffset;

                    sliderTrack.style.transition = useTransition ? 'transform 0.5s ease-in-out' : 'none';
                    sliderTrack.style.transform = `translateX(${translateX}px)`;
                }

                // Update dots indicator
                function updateDots() {
                    sliderDots.forEach((dot, index) => {
                        dot.classList.toggle("active", index === currentSlide);
                        dot.classList.toggle("opacity-50", index !== currentSlide);
                    });
                }

                // Handle transition end untuk infinite loop jump
                function onTransitionEnd() {
                    if (!useTransition || isTransitioning) return;

                    isTransitioning = true;

                    // Jika melewati clone terakhir (tail) → jump ke awal yang asli
                    if (internalIndex >= totalSlides * 2) {
                        internalIndex = totalSlides;
                        updateTrackPosition(false);
                    }
                    // Jika melewati clone pertama (head) → jump ke akhir yang asli
                    else if (internalIndex < totalSlides) {
                        internalIndex = totalSlides * 2 - 1;
                        updateTrackPosition(false);
                    }

                    setTimeout(() => {
                        isTransitioning = false;
                    }, 50);
                }

                // Next slide
                function nextSlide() {
                    if (isTransitioning) return;

                    currentSlide = (currentSlide + 1) % totalSlides;
                    internalIndex += 1;

                    updateTrackPosition(true);
                    updateDots();
                }

                // Previous slide
                function prevSlide() {
                    if (isTransitioning) return;

                    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                    internalIndex -= 1;

                    updateTrackPosition(true);
                    updateDots();
                }

                // Go to specific slide (untuk dots)
                function goToSlide(index) {
                    if (isTransitioning) return;

                    currentSlide = index;
                    internalIndex = totalSlides + index;

                    updateTrackPosition(true);
                    updateDots();
                }

                // Auto slide
                function startAutoSlide() {
                    autoSlideInterval = setInterval(() => {
                        if (!isTransitioning) {
                            nextSlide();
                        }
                    }, 5000);
                }

                function stopAutoSlide() {
                    clearInterval(autoSlideInterval);
                }

                // Initialize
                updateTrackPosition(false);
                updateDots();

                // Event listeners
                sliderTrack.addEventListener('transitionend', onTransitionEnd);

                sliderDots.forEach((dot, index) => {
                    dot.addEventListener("click", () => {
                        stopAutoSlide();
                        goToSlide(index);
                        startAutoSlide();
                    });
                });

                // Touch/Swipe support
                let touchStartX = 0;
                let touchEndX = 0;

                sliderTrack.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                    stopAutoSlide();
                });

                sliderTrack.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe();
                    startAutoSlide();
                });

                function handleSwipe() {
                    const swipeThreshold = 50;
                    if (touchStartX - touchEndX > swipeThreshold) {
                        nextSlide();
                    } else if (touchEndX - touchStartX > swipeThreshold) {
                        prevSlide();
                    }
                }

                // Pause on hover
                sliderTrack.addEventListener('mouseenter', stopAutoSlide);
                sliderTrack.addEventListener('mouseleave', startAutoSlide);

                // Handle resize
                window.addEventListener('resize', () => {
                    updateTrackPosition(false);
                });

                // Start auto slide
                startAutoSlide();
            }


            // =======================================================
            // 3️⃣ FUNGSI SET ACTIVE BUTTON (UTAMA SAJA)
            // =======================================================
            function setActiveButton(el) {
                // Ambil hanya tombol kategori utama, bukan yang di modal
                const allButtons = document.querySelectorAll(".kategori-btn:not(.modal-kategori-btn)");

                allButtons.forEach(btn => {
                    btn.classList.remove("active");

                    const iconWrapper = btn.querySelector(".icon-wrapper");
                    const isAllButton = btn.dataset.kat === "all";

                    if (iconWrapper) {
                        // Reset tampilan icon
                        iconWrapper.classList.remove(
                            "bg-green-600",
                            "text-white",
                            "border-green-600",
                            "bg-orange-200"
                        );
                        iconWrapper.classList.add("bg-orange-50");
                    }
                });

                // Tombol yang diklik → aktif
                if (el) {
                    el.classList.add("active");

                    const icon = el.querySelector(".icon-wrapper");
                    if (icon) {
                        icon.classList.remove("bg-orange-50");
                        icon.classList.add("bg-green-600", "text-white", "border-green-600");
                    }
                }
            }

            // =======================================================
            // 4️⃣ FILTER PROGRAM
            // =======================================================
            window.filterProgram = function(filter, el = null) {
                // Temukan tombol kategori utama yang sesuai
                const mainBtn = document.querySelector(`.kategori-btn[data-kat="${filter}"]`);

                if (mainBtn) {
                    // Jika klik dari modal → set active tombol utama
                    setActiveButton(mainBtn);
                } else if (el) {
                    // Klik dari tombol “Lainnya” (bukan kategori utama)
                    setActiveButton(el);
                }

                // Filter kartu program
                document.querySelectorAll(".program-card").forEach(card => {
                    const cat = card.dataset.category;

                    if (filter === "all" || !filter || cat === filter) {
                        card.style.display = "flex";
                    } else {
                        card.style.display = "none";
                    }
                });
            };

            // =======================================================
            // 5️⃣ SET DEFAULT (KATEGORI “SEMUA”)
            // =======================================================
            const defaultBtn = document.querySelector(".kategori-btn[data-kat='all']");
            if (defaultBtn) setActiveButton(defaultBtn);
        });
    </script>


@endsection
