    @php
        $isMobile = $type === 'mobile';
    @endphp

    <aside
        class="admin-sidebar {{ $isMobile ? 'admin-sidebar--mobile fixed inset-y-0 left-0 z-50 w-64 bg-primary shadow-lg flex flex-col lg:hidden' : 'admin-sidebar fixed inset-y-0 left-0 z-50 w-64 bg-primary shadow-lg hidden lg:flex flex-col' }}">
        {{-- Header / Logo --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center">
                    <img src="{{ asset('img/icon ABBI.png') }}" alt="">
                </div>
                <span class="text-white font-bold text-lg">
                    <span class="text-secondary">{{ site_setting('site_name_highlight', 'Ayo') }}</span>{{ site_setting('site_name_rest', 'buatbaik') }}
                </span>
            </div>

            @if ($isMobile)
                <button id="mobile-sidebar-close" class="text-gray-300 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>

        {{-- Sidebar Content --}}
        @include('components.layout.admin-sidebar-content')
    </aside>
