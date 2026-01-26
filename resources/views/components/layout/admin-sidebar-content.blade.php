<div class="flex flex-col justify-between h-full overflow-y-auto">

    <!-- User Info -->
    <div class="p-4 border-b border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-secondary rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
            </div>
            <div class="overflow-hidden">
                <p class="text-white text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                <p class="text-gray-300 text-[10px] truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-2">
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span>Dashboard</span>
        </a>


        <a href="{{ route('admin.programs.index') }}"
            class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.programs.*') ? 'bg-gray-800' : '' }}">
            <i class="fas fa-hand-holding-heart w-5"></i>
            <span>Program Donasi</span>
        </a>

        <a href="{{ route('admin.kitab.index') }}"
            class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.kitab.*') ? 'bg-gray-800' : '' }}">
            <i class="fas fa-book-quran w-5"></i>
            <span>Kelola Kitab</span>
        </a>



        <a href="{{ route('admin.donasi.index') }}"
            class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.donasi.*') ? 'bg-gray-800' : '' }}">
            <i class="fas fa-receipt w-5"></i>
            <span>Transaksi Donasi</span>
        </a>

        <a href="{{ route('admin.sliders.index') }}"
            class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.sliders.*') ? 'bg-gray-800' : '' }}">
            <i class="fas fa-images"></i>
            <span>Sliders</span>
        </a>

        <a href="{{ route('admin.kategori_donasi.index') }}"
            class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.kategori_donasi.*') ? 'bg-gray-800' : '' }}">
            <i class="fas fa-tags w-5"></i>
            <span>Kategori Donasi</span>
        </a>

        <a href="{{ route('admin.penggalang_dana.index') }}"
            class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.users') ? 'bg-gray-800' : '' }}">
            <i class="fas fa-users w-5"></i>
            <span>Penggalang Donasi</span>
        </a>

        <a href="{{ route('admin.berita.index') }}"
            class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.berita.*') ? 'bg-gray-800' : '' }}">
            <i class="fas fa-newspaper w-5"></i>
            <span>Berita</span>
        </a>

        <a href="{{ route('admin.users') }}"
            class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.users') ? 'bg-gray-800' : '' }}">
            <i class="fas fa-users w-5"></i>
            <span>Daftar Pengguna</span>
        </a>

        <a href="{{ route('admin.broadcast.index') }}"
            class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.broadcast.*') ? 'bg-gray-800' : '' }}">
            <i class="fas fa-bullhorn w-5"></i>
            <span>Broadcast WA</span>
        </a>

        <a href="{{ route('admin.settings.index') }}"
            class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-gray-800 transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-gray-800' : '' }}">
            <i class="fas fa-cog w-5"></i>
            <span>Pengaturan Situs</span>
        </a>
    </nav>

    <!-- Logout -->
    <div class="p-4 border-t border-gray-700">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-red-600 transition-colors w-full text-left">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</div>
