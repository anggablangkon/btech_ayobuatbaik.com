@extends('components.layout.app')

@section('title', 'Terjemah Kitab ' . $kitab->name . ' Bab ' . $chapter->nomor_bab . ' Maqolah ' . $maqolah->nomor_maqolah . ' - ' . site_name())

@section('og_title', 'Terjemah Kitab ' . $kitab->name . ' Bab ' . $chapter->nomor_bab . ' Maqolah ' . $maqolah->nomor_maqolah . ' - ' . site_name())
@section('og_description', Str::limit(strip_tags($maqolah->konten), 160))
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
                {{-- Back Button --}}
                <a href="{{ route('home.kitab.chapter', [$kitab->slug, $chapter->slug]) }}" 
                    class="inline-flex items-center text-sm text-gray-300 hover:text-secondary mb-4 transition-colors font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Bab {{ $chapter->nomor_bab }}
                </a>

                {{-- Breadcrumb like info --}}
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2 py-0.5 rounded-md bg-white/10 border border-white/10 text-[10px] font-bold tracking-wider text-gray-200">
                        BAB {{ $chapter->nomor_bab }}
                    </span>
                    <i class="fas fa-chevron-right text-[8px] text-gray-500"></i>
                    <span class="px-2 py-0.5 rounded-md bg-secondary/20 border border-secondary/20 text-[10px] font-bold tracking-wider text-secondary">
                        MAQOLAH {{ $maqolah->nomor_maqolah }}
                    </span>
                </div>

                <h1 class="text-xl font-bold leading-tight text-white/90">
                    Terjemah Kitab {{ $kitab->name }} {{ $maqolah->judul ?: 'Bab ' . $chapter->nomor_bab . ' Maqolah ' . $maqolah->nomor_maqolah }}
                </h1>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="px-4 -mt-6 pb-48 relative z-20">
            <div class="max-w-7xl mx-auto">
                {{-- Konten Maqolah --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">
                    <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed font-light">
                        {!! $maqolah->konten !!}
                    </div>
                </div>

                {{-- 🤲 Donation CTA Banner - Soft Approach --}}
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-100 p-5 mb-4 relative overflow-hidden">
                    {{-- Decorative Pattern --}}
                    <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-200/30 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-16 h-16 bg-teal-200/30 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-hands-praying text-emerald-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-gray-800 mb-1">Ingin Berbagi Kebaikan?</h3>
                                <p class="text-xs text-gray-600 leading-relaxed mb-3">
                                    Dukung program-program sosial dan dakwah kami. 
                                    <span class="text-emerald-700 font-medium">Setiap donasi adalah investasi akhirat.</span>
                                </p>
                                <a href="{{ route('home.program') }}" 
                                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition-all shadow-sm shadow-emerald-200"
                                   onclick="fbq('track', 'InitiateCheckout', { content_name: 'Kitab {{ $kitab->name }}', content_category: 'Donation CTA from Kitab' });">
                                    <i class="fas fa-heart"></i>
                                    <span>Lihat Program Donasi</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Share Section --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <h3 class="font-bold text-gray-800 text-xs uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-share-nodes text-secondary"></i> Bagikan Kebaikan
                    </h3>
                    <div class="flex gap-2">
                        <a href="https://wa.me/?text={{ urlencode('*' . $maqolah->judul . "*\n\nBaca selengkapnya di: " . route('home.kitab.maqolah', ['kitabSlug' => $kitab->slug, 'chapterSlug' => $chapter->slug, 'id' => $maqolah->id])) }}" 
                            target="_blank"
                            class="flex-1 bg-[#25D366] hover:bg-[#128C7E] text-white text-center py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm shadow-green-200"
                            onclick="fbq('track', 'Contact');">
                            <i class="fab fa-whatsapp mr-1.5 text-lg align-middle"></i> WhatsApp
                        </a>
                        <button onclick="copyToClipboard()" 
                            class="flex-1 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 text-center py-2.5 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-copy mr-1.5 text-gray-500"></i> Salin
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Bottom --}}
        <div class="fixed bottom-[80px] inset-x-0 mx-auto w-full max-w-[480px] px-4 z-30">
            <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-white/50 p-2 flex items-center justify-between gap-3">
                
                {{-- Previous Button --}}
                <div class="flex-1">
                    @if($previous)
                        <a href="{{ route('home.kitab.maqolah', ['kitabSlug' => $kitab->slug, 'chapterSlug' => $chapter->slug, 'id' => $previous->id]) }}" 
                            class="w-full h-11 flex items-center justify-center gap-2 bg-gray-50 hover:bg-white hover:border-secondary/30 text-gray-600 hover:text-secondary rounded-xl text-xs font-bold transition-all border border-transparent group">
                            <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                            <span>Sebelumnya</span>
                        </a>
                    @else
                        <a href="{{ route('home.kitab.chapter', [$kitab->slug, $chapter->slug]) }}" 
                            class="w-full h-11 flex items-center justify-center gap-2 bg-gray-50 text-gray-400 rounded-xl text-xs font-bold transition-all border border-transparent">
                            <i class="fas fa-list"></i>
                            <span>Daftar BAB {{ $chapter->nomor_bab }}</span>
                        </a>
                    @endif
                </div>

                {{-- Pages Indicator --}}
                <div class="flex-shrink-0 px-2 flex flex-col items-center justify-center min-w-[50px]">
                    <span class="text-[8px] text-gray-400 font-bold uppercase tracking-widest mb-0.5">HAL</span>
                    <span class="text-lg font-black text-primary leading-none">{{ $maqolah->nomor_maqolah }}</span>
                </div>

                {{-- Next Button --}}
                <div class="flex-1">
                    @if($next)
                        <a href="{{ route('home.kitab.maqolah', ['kitabSlug' => $kitab->slug, 'chapterSlug' => $chapter->slug, 'id' => $next->id]) }}" 
                            class="w-full h-11 flex items-center justify-center gap-2 bg-secondary text-white hover:bg-yellow-600 rounded-xl text-xs font-bold transition-all shadow-lg shadow-orange-200 group">
                            <span>Selanjutnya</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    @else
                        <a href="{{ route('home.kitab.chapter', [$kitab->slug, $chapter->slug]) }}" 
                            class="w-full h-11 flex items-center justify-center gap-2 bg-gray-800 text-white hover:bg-gray-700 rounded-xl text-xs font-bold transition-all shadow-lg group">
                            <span>Daftar BAB {{ $chapter->nomor_bab }}</span>
                            <i class="fas fa-list group-hover:scale-110 transition-transform"></i>
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
// Event Tracking Jamaah Kajian
fbq('track', 'JamaahKajian', {
    kitab: '{{ $kitab->name }}',
    chapter: 'Bab {{ $chapter->nomor_bab }}',
    maqolah: '{{ $maqolah->judul ?: "Maqolah " . $maqolah->nomor_maqolah }}'
});

function copyToClipboard() {
    // Ambil konten text only
    const title = "{{ $maqolah->judul }}";
    const content = `{{ strip_tags($maqolah->konten) }}`; 
    const footer = "- Kitab {{ $kitab->name }}, " + "{{ site_setting('site_url', url('/')) }}";
    
    const text = `${title}\n\n${content.trim()}\n\n${footer}`;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Maqolah berhasil disalin!');
        });
    } else {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Maqolah berhasil disalin!');
    }
}
</script>
@endsection
