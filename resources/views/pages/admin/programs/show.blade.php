@extends('components.layout.admin')

@section('title', 'Detail Program: ' . $program->title)
@section('page-title', 'Detail Program Donasi')

@section('content')
    @php use Illuminate\Support\Str; @endphp

    <div class="max-w-7xl mx-auto mt-6 space-y-6">

        {{-- 1. STATISTIK UTAMA (POSISI DI ATAS SEPERTI GAMBAR) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Card 1: Total Dilihat --}}
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
                    <i class="fas fa-eye text-lg"></i>
                </div>
            <div>
                    <p class="text-xs text-gray-500 font-medium uppercase">Total Dilihat</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($program->view_count ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Card 2: Total Donatur --}}
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-600 flex-shrink-0">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase">Total Donatur</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($donors_count ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Card 3: Terkumpul --}}
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600 flex-shrink-0">
                    <i class="fas fa-wallet text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase">Terkumpul</p>
                    <p class="text-xl font-bold text-green-600">Rp {{ number_format($total_amount ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Card 4: Target --}}
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-600 flex-shrink-0">
                    <i class="fas fa-bullseye text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase">Target Donasi</p>
                    <p class="text-xl font-bold text-gray-900">Rp {{ number_format($program->target_amount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- 2. DETAIL INFORMASI PROGRAM --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="flex flex-col justify-between h-full">
                <div class="space-y-4">

                    {{-- Judul & Status --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">
                            {{ $program->title }}
                        </h2>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-user-circle text-gray-400"></i>
                                {{ $program->penggalang->nama ?? '-' }}
                            </span>
                            @if ($program->verified)
                                <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100 text-xs font-semibold flex items-center gap-1">
                                    <i class="fas fa-check-circle"></i> Verified
                                </span>
                            @endif
                            <span class="px-2 py-0.5 rounded text-xs font-semibold border
                                {{ $program->status === 'active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                {{ ucfirst($program->status) }}
                            </span>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed">
                        {{ $program->short_description ?? 'Tidak ada deskripsi singkat.' }}
                    </div>

                    {{-- Info Tanggal --}}
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="far fa-calendar-alt text-gray-400"></i>
                        <span class="font-semibold">Periode:</span>
                        <span>
                            {{ $program->end_date ? \Carbon\Carbon::parse($program->end_date)->format('d M Y') : 'Tanpa Batas Waktu' }}
                        </span>
                        <span class="text-xs text-gray-400">
                            (Dibuat: {{ $program->created_at->format('d M Y') }})
                        </span>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex flex-wrap gap-3 pt-6 mt-2 border-t border-gray-100">
                    <a href="{{ route('admin.programs.edit', $program->id) }}"
                        class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 hover:text-blue-600 transition">
                        <i class="fas fa-edit"></i> Edit Program
                    </a>

                    <form id="delete-form-{{ $program->id }}" action="{{ route('admin.programs.destroy', $program->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmDelete('delete-form-{{ $program->id }}', 'ANDA YAKIN INGIN MENGHAPUS PROGRAM INI?')"
                            class="inline-flex items-center gap-2 bg-white border border-red-200 text-red-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-50 transition">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. TABEL RIWAYAT DONASI --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h3 class="text-lg font-bold text-gray-900">Riwayat Donasi Sukses</h3>

                <form method="GET" class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="donor_search" placeholder="Cari donatur..."
                        value="{{ request('donor_search') }}"
                        class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-64 transition" />
                </form>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donatur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($donations as $donation)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $donation->created_at->format('d M Y') }}
                                    <span class="text-xs text-gray-400 block">{{ $donation->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $donation->donor_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $donation->donor_email ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $donation->donor_phone ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">
                                    Rp {{ number_format($donation->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ ucfirst($donation->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="far fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                        <p>Belum ada data donasi sukses.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @forelse ($donations as $donation)
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $donation->donor_name }}</p>
                                <p class="text-xs text-gray-500">{{ $donation->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">
                                {{ ucfirst($donation->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-end">
                            <div class="text-xs text-gray-500">
                                <p>{{ $donation->donor_email ?? '-' }}</p>
                                <p>{{ $donation->donor_phone ?? '-' }}</p>
                            </div>
                            <p class="text-base font-bold text-green-600">
                                Rp {{ number_format($donation->amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500 text-sm">
                        Belum ada data donasi sukses.
                    </div>
                @endforelse
            </div>

            @if($donations->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $donations->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
