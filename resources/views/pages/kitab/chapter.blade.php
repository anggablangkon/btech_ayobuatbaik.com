@extends('components.layout.app')

@section('title', $kitab->name . ' Bab ' . $chapter->nomor_bab . ($chapter->judul_bab ? ' : ' . $chapter->judul_bab : '') . ' - ' . site_name())

@section('og_title', $kitab->name . ' Bab ' . $chapter->nomor_bab . ($chapter->judul_bab ? ' : ' . $chapter->judul_bab : '') . ' - ' . site_name())
@section('og_description', strip_tags($chapter->deskripsi))
@section('og_url', url()->current())
@section('og_image', asset($kitab->cover_image ? 'storage/' . $kitab->cover_image : site_setting('site_logo', 'img/icon_ABBI.png')))

@section('header-content')
    @include('components.layout.header')
@endsection

@section('content')
    <div class="content bg-gray-50 min-h-screen">
        {{-- Header Section --}}
        <div class="bg-gradient-to-br from-primary via-gray-800 to-gray-900 text-white pt-4 pb-10 px-4 relative overflow-hidden">
            {{-- Decorative Elements --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-secondary rounded-full filter blur-3xl -translate-y-1/2 translate-x-1/2"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto">
                <div class="flex justify-between items-center mb-6 gap-4">
                    {{-- Back Button --}}
                    <a href="{{ route('home.kitab.show', $kitab->slug) }}" 
                        class="inline-flex items-center text-sm text-gray-300 hover:text-secondary transition-colors font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke {{ $kitab->name }}
                    </a>

                    {{-- Pencarian maqolah cepat --}}
                    <div class="flex-shrink-0">
                        <div class="bg-white/10 backdrop-blur-md p-1.5 px-3 rounded-xl border border-white/10 shadow-lg flex items-center gap-3">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                <i class="fa-solid fa-magnifying-glass text-secondary mr-1"></i> Maqolah
                            </p>
                            <div class="relative bg-white/5 rounded-lg flex items-center px-2 py-1 focus-within:ring-1 focus-within:ring-secondary/50 transition-all border border-white/5">
                                <input type="number" id="search-maqolah" placeholder="No..." 
                                    class="bg-transparent border-0 text-white text-xs placeholder-gray-500 focus:ring-0 p-0 w-10 font-bold appearance-none text-center"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-16 h-16 bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="text-secondary font-black text-2xl drop-shadow-sm">{{ $chapter->nomor_bab }}</span>
                        </div>
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded-md bg-secondary/20 text-secondary text-[10px] font-bold tracking-wider mb-2 border border-secondary/20">
                                BAB {{ $chapter->nomor_bab }}
                            </span>
                            <h1 class="text-xl font-bold leading-tight mb-2">{{ $kitab->name }} Bab {{ $chapter->nomor_bab }}{{ $chapter->judul_bab ? ' : ' . $chapter->judul_bab : '' }}</h1>
                            <div class="flex items-center text-xs text-gray-400 font-medium">
                                <i class="fas fa-scroll text-secondary mr-2"></i>
                                <span>{{ $chapter->maqolahs->count() }} Maqolah dalam bab ini</span>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>

        {{-- Maqolah List --}}
        <div class="px-4 -mt-6 pb-24 relative z-20">
            <div class="max-w-7xl mx-auto space-y-3">
                
                @if($chapter->deskripsi)
                    <div class="flex justify-center mb-8">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-8 py-6 text-center relative overflow-hidden group max-w-4xl w-fit">
                            <div class="prose prose-xl max-w-none text-gray-800 leading-loose font-serif line-clamp-4 mx-auto">
                                 {!! strip_tags($chapter->deskripsi) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <div id="empty-state" class="hidden text-center py-12">
                     <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-search text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Maqolah tidak ditemukan</p>
                </div>
                
                @forelse ($chapter->maqolahs as $index => $maqolah)
                    <a href="{{ route('home.kitab.maqolah', ['kitabSlug' => $kitab->slug, 'chapterSlug' => $chapter->slug, 'id' => $maqolah->id]) }}"
                        data-nomor="{{ $maqolah->nomor_maqolah }}"
                        class="maqolah-card block bg-white rounded-xl shadow-sm border border-gray-100 hover:border-secondary hover:shadow-md transition-all duration-300 overflow-hidden group">
                        <div class="p-4">
                            <div class="flex items-start gap-3">
                                {{-- Number Badge --}}
                                <div class="flex-shrink-0 w-8 h-8 bg-gray-50 border border-gray-100 group-hover:bg-secondary/10 group-hover:border-secondary/20 rounded-lg flex items-center justify-center transition-colors">
                                    <span class="text-gray-500 group-hover:text-secondary font-bold text-xs">{{ $maqolah->nomor_maqolah }}</span>
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0 pt-1">
                                    <h3 class="font-bold text-gray-800 text-sm group-hover:text-secondary transition-colors mb-1 line-clamp-2">
                                        Terjemah Kitab {{ $kitab->name }} {{ $maqolah->judul ?: 'Bab ' . $chapter->nomor_bab . ' Maqolah ' . $maqolah->nomor_maqolah }}
                                    </h3>
                                    <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed font-light">
                                        {{ Str::limit(html_entity_decode(strip_tags($maqolah->konten)), 120) }}
                                    </p>
                                </div>

                                {{-- Arrow --}}
                                <div class="flex-shrink-0 ml-1 self-center">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center bg-gray-50 group-hover:bg-secondary group-hover:text-white transition-all">
                                        <i class="fas fa-chevron-right text-[10px] text-gray-300 group-hover:text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-scroll text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-base font-semibold text-gray-600">Belum ada maqolah</p>
                        <p class="text-xs text-gray-400">Maqolah untuk bab ini belum tersedia.</p>
                    </div>
                @endforelse

                {{-- 🤲 Donation CTA Banner - Soft Approach --}}
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-100 p-5 mt-6 relative overflow-hidden">
                    {{-- Decorative Pattern --}}
                    <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-200/30 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-16 h-16 bg-teal-200/30 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-hands-praying text-emerald-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-gray-800 mb-1">Dukung Dakwah Digital</h3>
                                <p class="text-xs text-gray-600 leading-relaxed mb-3">
                                    Bantu kami terus menerjemahkan dan menyebarkan ilmu kitab kuning secara gratis. 
                                    <span class="text-emerald-700 font-medium">Sedekah ilmu, pahalanya terus mengalir.</span>
                                </p>
                                <a href="{{ route('home.program') }}" 
                                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition-all shadow-sm shadow-emerald-200"
                                   onclick="fbq('track', 'InitiateCheckout', { content_name: 'Kitab {{ $kitab->name }}', content_category: 'Donation CTA from Kitab' });">
                                    <i class="fas fa-heart"></i>
                                    <span>Dukung Kajian Ini</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-maqolah');
            const cards = document.querySelectorAll('.maqolah-card');
            const emptyState = document.getElementById('empty-state');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const value = this.value.trim();
                    let hasVisible = false;
                    
                    cards.forEach(card => {
                        const nomor = card.getAttribute('data-nomor');
                        
                        // Remove previous highlight
                        card.classList.remove('border-secondary', 'ring-1', 'ring-secondary/50', 'bg-secondary/5');
                        
                        if (value === '' || nomor.includes(value)) {
                            card.style.display = 'block';
                            hasVisible = true;
                            
                            // Add highlight if searching
                            if (value !== '') {
                                card.classList.add('border-secondary', 'ring-1', 'ring-secondary/50', 'bg-secondary/5');
                            }
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    // Show empty state if no cards visible
                    if (emptyState) {
                        emptyState.style.display = hasVisible ? 'none' : 'block';
                    }
                });
            }
        });
    </script>
@endsection
