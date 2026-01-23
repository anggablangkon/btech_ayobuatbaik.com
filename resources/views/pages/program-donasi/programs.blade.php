@extends('components.layout.app')

@section('title', 'Program Donasi - ' . site_name())

@section('og_title', 'Program Donasi - ' . site_name())
@section('og_description', 'Program Donasi - ' . site_name())
@section('og_url', url()->current())
@section('og_image', asset(site_setting('site_logo', 'img/icon_ABBI.png')))

@section('header-content')
    @include('components.layout.header')
@endsection

@section('content')
    <div class="content">
        <!-- Header Section -->
        <div class="bg-primary text-white pt-6 pb-8 px-4">
            <div class="flex justify-center items-center mb-4">
                <h1 class="text-2xl font-bold">Program Donasi</h1>
                <!-- <button class="w-10 h-10 flex items-center justify-center bg-white bg-opacity-20 rounded-full">
                              <i class="fas fa-sliders-h text-white"></i>
                            </button> -->
            </div>

            <!-- Search Bar -->
            <div class="relative">
                <div class="search-bar flex items-center">
                    <i class="fas fa-search text-gray-400 mr-3"></i>
                    <input type="text" placeholder="Cari program donasi..."
                        class="search-input w-full bg-transparent outline-none text-gray-700 placeholder-gray-500">
                </div>
            </div>

            <!-- Quick Stats -->
            {{-- <div class="flex justify-between text-center">
                <div>
                    <p class="text-2xl font-bold">150+</p>
                    <p class="text-xs text-gray-300">Program Aktif</p>
                </div>
                <div>
                    <p class="text-2xl font-bold">25K+</p>
                    <p class="text-xs text-gray-300">Donatur</p>
                </div>
                <div>
                    <p class="text-2xl font-bold">Rp 5M+</p>
                    <p class="text-xs text-gray-300">Terkumpul</p>
                </div>
            </div> --}}
        </div>

        <!-- Filter Section -->
        <div class="bg-white px-4 py-3 border-b">
            <div class="flex space-x-2 overflow-x-auto pb-2 filter-section">
                <button class="filter-option whitespace-nowrap active" data-filter="all">Semua Program</button>

                @foreach ($kategories as $kategori)
                    <button class="filter-option whitespace-nowrap" data-filter="{{ $kategori->slug }}">
                        {{ $kategori->name }}
                    </button>
                @endforeach
            </div>
        </div>


        <!-- Programs List -->
        <div class="p-4 pb-24">
            @foreach ($programs as $program)
                @php
                    $terkumpul = $program->collected_amount ?? 0;
                    $target = $program->target_amount ?? 1;
                    $progress = min(100, ($terkumpul / $target) * 100);

                    $sisaHari = $program->end_date
                        ? floor(max(0, now()->diffInDays($program->end_date, false))) . ' hari'
                        : '∞';
                @endphp

                <a href="{{ route('home.program.show', $program->slug) }}"
                    class="program-card bg-white overflow-hidden shadow-md flex items-stretch h-28 mb-4 rounded-xl transition-all duration-300 hover:shadow-lg hover:scale-[1.01]"
                    data-category="{{ $program->kategori->slug }}">

                    <div class="w-1/2 h-full flex-shrink-0 overflow-hidden">
                        <img src="{{ asset('storage/' . $program->gambar) }}" class="w-full h-full object-cover"
                            alt="{{ $program->title }}" loading="lazy">
                    </div>

                    <div class="py-2 px-3 flex flex-col justify-between w-full">
                        <div>
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
                            <p class="text-gray-600 text-[10px] mt-0.5 line-clamp-1">
                                {{ $program->short_description }}
                            </p>
                        </div>

                        <div class="mt-1">
                            <div class="flex justify-between text-[10px] text-gray-800 mb-0.5">
                                <span class="font-semibold">
                                    Terkumpul: Rp {{ number_format($terkumpul, 0, ',', '.') }}
                                </span>

                                <span class="text-gray-500">
                                    Sisa: {{ $sisaHari }}
                                </span>
                            </div>

                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-secondary h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const filterButtons = document.querySelectorAll(".filter-option");
            const programCards = document.querySelectorAll(".program-card");
            const searchInput = document.querySelector(".search-input");

            let activeFilter = "all"; // menyimpan filter aktif

            // Fungsi untuk update tampilan card
            function updateCards() {
                const searchTerm = searchInput.value.toLowerCase();

                programCards.forEach(card => {
                    const matchesFilter =
                        activeFilter === "all" ||
                        card.getAttribute("data-category") === activeFilter;
                    const title = card.querySelector("h3").textContent.toLowerCase();
                    const description = card.querySelector("p").textContent.toLowerCase();
                    const matchesSearch =
                        title.includes(searchTerm) || description.includes(searchTerm);

                    if (matchesFilter && matchesSearch) {
                        card.classList.remove("hidden");
                        card.classList.add("flex");
                    } else {
                        card.classList.add("hidden");
                        card.classList.remove("flex");
                    }
                });
            }

            // Klik tombol kategori
            filterButtons.forEach(button => {
                button.addEventListener("click", function() {
                    activeFilter = this.getAttribute("data-filter");

                    // update tombol aktif
                    filterButtons.forEach(btn => btn.classList.remove("active", "bg-primary",
                        "text-white"));
                    this.classList.add("active", "bg-primary", "text-white");

                    updateCards();
                });
            });

            // Input pencarian
            searchInput.addEventListener("input", updateCards);

            // Inisialisasi awal (tampilkan semua)
            updateCards();
        });
    </script>

@endsection
