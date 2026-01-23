<nav class="mobile-top-bar bg-primary">
    <div class="flex items-center justify-between h-full px-2 py-2">

        <a href="{{ route('home') }}" class="flex-shrink-0 mr-4">
            <h1 class="text-xl font-bold text-white"><span class="text-secondary">{{ site_setting('site_name_highlight', 'Ayo') }}</span>{{ site_setting('site_name_rest', 'buatbaik') }}</h1>
        </a>

        <!-- Search Bar -->
        <div class="flex-1 relative">
            <form action="{{ route('home.search') }}" method="get"
                class="flex items-center bg-white bg-opacity-20 rounded-lg px-3 py-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari program . . ."
                    class="w-full bg-transparent text-white placeholder-white placeholder-opacity-70 outline-none text-xs" />
                <button type="submit">
                    <i class="fas fa-search text-white text-opacity-70 mr-2 text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</nav>
