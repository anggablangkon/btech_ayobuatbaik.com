@extends('components.layout.admin')

@section('title', 'Dashboard Admin - ' . site_name())
@section('page-title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Programs -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Program</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_programs'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hand-holding-heart text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-600">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span>Active programs</span>
                </div>
            </div>

            <!-- Total Donations -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Donasi</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['total_donations'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-donate text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-600">
                    <i class="fas fa-users mr-1"></i>
                    <span>Total transaksi</span>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Dana</p>
                        <p class="text-xl font-bold text-gray-900 mt-2">
                            Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-600">
                    <i class="fas fa-chart-line mr-1"></i>
                    <span>Terkumpul</span>
                </div>
            </div>

            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Pengguna</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-600">
                    <i class="fas fa-user-plus mr-1"></i>
                    <span>Registered users</span>
                </div>
            </div>
        </div>

        <!-- Recent Activity Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Donations -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Donasi Terbaru</h3>
                    <a href="#" class="text-sm text-secondary hover:text-goldDark font-medium">
                        Lihat semua
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($recent_donations as $donation)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-bold">
                                        {{ isset($donation['donor_name']) && !empty($donation['donor_name'])
                                            ? strtoupper(substr($donation['donor_name'], 0, 1))
                                            : '?' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $donation['donor_name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $donation['program'] }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-secondary">
                                    Rp {{ number_format($donation['amount'], 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $donation['time'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            <i class="fas fa-donate text-3xl mb-2"></i>
                            <p>Belum ada donasi</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Programs -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Program Terbaru</h3>
                    <a href="#" class="text-sm text-secondary hover:text-goldDark font-medium">
                        Lihat semua
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($recent_programs as $program)
                        @php
                            $progress =
                                $program->target_amount > 0
                                    ? round(($program->collected_amount / $program->target_amount) * 100)
                                    : 0;
                        @endphp

                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-12 h-12 bg-cover bg-center rounded-lg"
                                style="background-image: url('{{ asset('storage/' . $program->gambar) }}')"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ $program->title }}</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-secondary h-2 rounded-full" style="width: {{ $progress }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $progress }}%</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            <i class="fas fa-hand-holding-heart text-3xl mb-2"></i>
                            <p>Belum ada program</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Quick Actions</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="#"
                    class="flex items-center space-x-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-plus text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Tambah Program</p>
                        <p class="text-sm text-gray-600">Buat program donasi baru</p>
                    </div>
                </a>

                <a href="#"
                    class="flex items-center space-x-3 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-donate text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Kelola Donasi</p>
                        <p class="text-sm text-gray-600">Lihat semua transaksi</p>
                    </div>
                </a>

                <a href="#"
                    class="flex items-center space-x-3 p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Data Pengguna</p>
                        <p class="text-sm text-gray-600">Kelola user & donatur</p>
                    </div>
                </a>

                <a href="{{ route('home') }}" target="_blank"
                    class="flex items-center space-x-3 p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                    <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-external-link-alt text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Preview Website</p>
                        <p class="text-sm text-gray-600">Lihat tampilan user</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
