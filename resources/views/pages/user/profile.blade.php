@extends('components.layout.app')

@section('title', 'Profil Saya - ' . site_name())
@section('og_title', 'Profil Saya - ' . site_name())

@section('content')
    {{-- Main Container --}}
    <div class="min-h-screen bg-gray-50 py-8 px-4 pb-32" x-data="{ activeTab: 'riwayat' }">
        <div class="max-w-5xl mx-auto space-y-6">
            
            {{-- 1. HEADER PROFILE & STATS --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row gap-8 items-start md:items-center">
                        {{-- Avatar & Info --}}
                        <div class="flex items-center gap-5 flex-1">
                            <div class="relative">
                                <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg text-white text-3xl md:text-4xl font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                @if(auth()->user()->is_admin)
                                    <div class="absolute -bottom-1 -right-1 bg-yellow-400 text-white rounded-full p-1.5 border-4 border-white shadow-sm" title="Admin">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                                <p class="text-gray-500 text-sm mb-3">{{ $user->email }}</p>
                                <div class="flex gap-3">
                                    @if(auth()->user()->is_admin)
                                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold hover:bg-gray-200 transition">
                                            <i class="fas fa-tachometer-alt"></i> Dashboard
                                        </a>
                                    @endif
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-50 text-red-600 text-xs font-semibold hover:bg-red-100 transition">
                                            <i class="fas fa-sign-out-alt"></i> Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                {{-- Divider --}}
                <div class="hidden md:block w-px h-24 bg-gray-100" style="display: none;"></div>
            </div>

            {{-- Tab Navigation Bar --}}
            <div class="flex border-t border-gray-100 bg-gray-50/50">
                <button @click="activeTab = 'riwayat'" 
                    :class="activeTab === 'riwayat' ? 'text-green-600 border-green-500 bg-white' : 'text-gray-500 hover:text-gray-700 border-transparent hover:bg-gray-50'"
                    class="flex-1 py-4 text-sm font-semibold border-b-2 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-history"></i> Riwayat
                </button>
                <button @click="activeTab = 'profil'" 
                    :class="activeTab === 'profil' ? 'text-green-600 border-green-500 bg-white' : 'text-gray-500 hover:text-gray-700 border-transparent hover:bg-gray-50'"
                    class="flex-1 py-4 text-sm font-semibold border-b-2 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-user-edit"></i> Profil
                </button>
                <button @click="activeTab = 'keamanan'" 
                    :class="activeTab === 'keamanan' ? 'text-green-600 border-green-500 bg-white' : 'text-gray-500 hover:text-gray-700 border-transparent hover:bg-gray-50'"
                    class="flex-1 py-4 text-sm font-semibold border-b-2 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-lock"></i> Password
                </button>
            </div>
        </div>

        {{-- 2. CONTENT AREA --}}
        <div class="min-h-[400px]">
            {{-- Flash Message --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg shadow-sm mb-6 flex items-center justify-between" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
                </div>
            @endif

            {{-- TAB: RIWAYAT --}}
            <div x-show="activeTab === 'riwayat'" x-transition.opacity.duration.300ms>
                {{-- Stats Grid (Moved Here) --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex flex-col justify-center">
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-2">Total Donasi</p>
                        <p class="text-xl md:text-2xl font-bold text-green-600">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex flex-col justify-center">
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-2">Frekuensi</p>
                        <p class="text-xl md:text-2xl font-bold text-blue-600">{{ $totalDonations }} <span class="text-sm text-gray-400 font-normal">kali</span></p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="font-bold text-gray-900">Riwayat Donasi Terbaru</h3>
                            {{-- Optional: View All Link --}}
                            {{-- <a href="#" class="text-sm text-green-600 hover:underline">Lihat Semua</a> --}}
                        </div>

                        @if($recentDonations->count() > 0)
                            {{-- Unified Card List (Mobile Style for All) --}}
                            <div class="divide-y divide-gray-100">
                                @foreach($recentDonations as $donation)
                                    <div class="p-4 space-y-3 hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <span class="px-2 py-1 bg-gray-100 rounded text-[10px] font-mono text-gray-500">{{ $donation->donation_code }}</span>
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                                {{ $donation->status === 'success' ? 'bg-green-50 text-green-700' : '' }}
                                                {{ $donation->status === 'pending' ? 'bg-yellow-50 text-yellow-700' : '' }}
                                                {{ in_array($donation->status, ['failed', 'expire', 'unpaid']) ? 'bg-red-50 text-red-700' : '' }}">
                                                {{ ucfirst($donation->status) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-normal text-gray-700 text-sm line-clamp-2">{{ $donation->program ? $donation->program->title : 'Program Dihapus' }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $donation->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                        <div class="pt-2 border-t border-gray-50 flex justify-between items-center">
                                            <span class="text-xs text-gray-500">Total Donasi</span>
                                            <span class="font-bold text-gray-900">Rp {{ number_format($donation->amount, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16 px-4">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <i class="fas fa-hand-holding-heart text-3xl"></i>
                                </div>
                                <h4 class="text-gray-900 font-medium">Belum ada donasi</h4>
                                <p class="text-gray-500 text-sm mt-1">Jejak kebaikan Anda akan muncul di sini.</p>
                                <a href="{{ route('home') }}" class="mt-4 inline-block px-6 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">Mulai Berdonasi</a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- TAB: EDIT PROFILE --}}
                <div x-show="activeTab === 'profil'" x-transition.opacity.duration.300ms style="display: none;">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 max-w-2xl mx-auto">
                        <h3 class="font-bold text-gray-900 text-lg mb-6 flex items-center gap-2">
                            <i class="fas fa-user-circle text-green-600"></i> Edit Informasi Profil
                        </h3>
                        <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
                            @csrf
                            <div class="space-y-1">
                                <label class="text-sm font-semibold text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all outline-none text-sm placeholder-gray-400">
                                @error('name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-semibold text-gray-700">Alamat Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all outline-none text-sm placeholder-gray-400">
                                @error('email') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            </div>
                            <div class="pt-4">
                                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold shadow-md active:transform active:scale-95 transition-all text-sm">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TAB: KEAMANAN --}}
                <div x-show="activeTab === 'keamanan'" x-transition.opacity.duration.300ms style="display: none;">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 max-w-2xl mx-auto">
                        <h3 class="font-bold text-gray-900 text-lg mb-6 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-green-600"></i> Ubah Password
                        </h3>
                        <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-4 mb-6 text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i> Pastikan password baru Anda kuat dan tidak mudah ditebak.
                        </div>
                        <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                            @csrf
                            <div class="grid md:grid-cols-2 gap-5">
                                <div class="space-y-1">
                                    <label class="text-sm font-semibold text-gray-700">Password Baru</label>
                                    <input type="password" name="new_password" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all outline-none text-sm">
                                    @error('new_password') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-1">
                                    <label class="text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                                    <input type="password" name="new_password_confirmation" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all outline-none text-sm">
                                </div>
                            </div>
                            
                            <div class="pt-4">
                                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-gray-900 hover:bg-black text-white rounded-lg font-semibold shadow-md active:transform active:scale-95 transition-all text-sm">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Alpine.js --}}
    <script src="//unpkg.com/alpinejs" defer></script>
@endsection
