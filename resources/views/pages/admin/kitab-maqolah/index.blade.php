@extends('components.layout.admin')

@section('title', 'Kelola Maqolah Kitab - ' . site_name())
@section('page-title', 'Kelola Maqolah Kitab')

@section('content')
    @php use Illuminate\Support\Str; @endphp

    <div class="max-w-7xl mx-auto mt-8">
        <!-- Header & Filter -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3 flex-wrap">
                    <h3 class="text-base font-semibold text-gray-900">
                        Daftar Maqolah
                        @if($selectedChapter)
                            <span class="text-primary">- Bab {{ $selectedChapter->nomor_bab }}</span>
                        @endif
                    </h3>
                    <a href="{{ route('admin.kitab_maqolah.create', ['chapter' => request('chapter')]) }}"
                        class="inline-flex items-center gap-2 bg-primary text-white px-3 py-2 rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-plus"></i> <span>Tambah Maqolah</span>
                    </a>
                </div>

                <!-- Filter Form -->
                <form id="controlsForm" method="GET" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                    <select name="chapter" id="chapter" class="border rounded px-3 py-2">
                        <option value="">Semua Bab</option>
                        @foreach($chapters as $ch)
                            <option value="{{ $ch->id }}" {{ request('chapter') == $ch->id ? 'selected' : '' }}>
                                Bab {{ $ch->nomor_bab }} @if($ch->judul_bab) - {{ Str::limit($ch->judul_bab, 25) }} @endif
                            </option>
                        @endforeach
                    </select>

                    <input type="text" name="search" placeholder="Cari judul..." value="{{ request('search') }}"
                        class="w-full sm:w-48 border px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-primary" />

                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Show</label>
                        <select name="perPage" id="perPage" class="border rounded px-2 py-2">
                            @foreach ([10, 30, 50, 100] as $size)
                                <option value="{{ $size }}" {{ request('perPage', 10) == $size ? 'selected' : '' }}>
                                    {{ $size }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="bg-secondary text-white px-3 py-2 rounded-md hover:opacity-95 transition">Terapkan</button>
                </form>
            </div>
        </div>

        <!-- Alert -->
        @if (session('success'))
            <div class="mt-4 p-3 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif

        <!-- List Display -->
        <div class="mt-6">
            @if($isGrouped)
                {{-- GROUPED VIEW: By Chapter --}}
                <div class="space-y-8">
                    @foreach($chaptersWithMaqolahs as $chapter)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            {{-- Chapter Header --}}
                            <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary text-white rounded-lg flex items-center justify-center font-bold shadow-sm">
                                        {{ $chapter->nomor_bab }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Bab {{ $chapter->nomor_bab }}</h4>
                                    </div>
                                </div>
                                <div class="text-xs font-medium text-gray-400">
                                    {{ $chapter->maqolahs->count() }} Maqolah
                                </div>
                            </div>

                            {{-- Maqolah Table for this Chapter --}}
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="text-gray-400 bg-white">
                                        <tr>
                                            <th class="px-6 py-3 text-left font-medium uppercase tracking-wider text-[10px]">No</th>
                                            <th class="px-6 py-3 text-left font-medium uppercase tracking-wider text-[10px]">Judul</th>
                                            <th class="px-6 py-3 text-left font-medium uppercase tracking-wider text-[10px] hidden md:table-cell">Konten</th>
                                            <th class="px-6 py-3 text-right font-medium uppercase tracking-wider text-[10px]">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse($chapter->maqolahs as $maq)
                                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                                <td class="px-6 py-4 font-bold text-primary w-16">{{ $maq->nomor_maqolah }}</td>
                                                <td class="px-6 py-4">
                                                    <div class="font-semibold text-gray-800">{{ Str::limit($maq->judul, 50) }}</div>
                                                </td>
                                                <td class="px-6 py-4 text-gray-500 hidden md:table-cell">
                                                    {{ Str::limit(html_entity_decode(strip_tags($maq->konten)), 80) }}
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <div class="flex items-center justify-end gap-3">
                                                        <a href="{{ route('admin.kitab_maqolah.edit', $maq->id) }}"
                                                            class="text-gray-400 hover:text-blue-600 transition-colors">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form id="delete-form-{{ $maq->id }}" 
                                                            action="{{ route('admin.kitab_maqolah.destroy', $maq->id) }}" 
                                                            method="POST" class="inline">
                                                            @csrf @method('DELETE')
                                                            <button type="button" onclick="confirmDelete('delete-form-{{ $maq->id }}')" 
                                                                class="text-gray-400 hover:text-red-500 transition-colors">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-3 py-5 text-center">
                                                    <div class="flex flex-col items-center gap-2">
                                                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-300">
                                                            <i class="fas fa-folder-open text-xl"></i>
                                                        </div>
                                                        <p class="text-sm text-gray-400">Belum ada maqolah di bab ini.</p>
                                                        <a href="{{ route('admin.kitab_maqolah.create', ['chapter' => $chapter->id]) }}" 
                                                           class="text-xs text-primary font-bold hover:underline">
                                                            <i class="fas fa-plus mr-1"></i> Tambah Sekarang
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $chaptersWithMaqolahs->appends(request()->query())->links() }}
                </div>
            @else
                {{-- FLAT VIEW: Search Results or Filtered Chapter --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-gray-400 bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium uppercase tracking-wider text-[10px] w-16">No</th>
                                    <th class="px-6 py-3 text-left font-medium uppercase tracking-wider text-[10px]">Bab</th>
                                    <th class="px-6 py-3 text-left font-medium uppercase tracking-wider text-[10px]">Judul</th>
                                    <th class="px-6 py-3 text-left font-medium uppercase tracking-wider text-[10px] hidden md:table-cell">Preview</th>
                                    <th class="px-6 py-3 text-right font-medium uppercase tracking-wider text-[10px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($maqolahs as $maqolah)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-6 py-4 font-bold text-primary">{{ $maqolah->nomor_maqolah }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 rounded text-[10px] font-bold text-gray-600">
                                                BAB {{ $maqolah->chapter->nomor_bab }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-800">{{ Str::limit($maqolah->judul, 40) }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">
                                            {{ Str::limit(html_entity_decode(strip_tags($maqolah->konten)), 60) }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('admin.kitab_maqolah.edit', $maqolah->id) }}"
                                                    class="text-gray-400 hover:text-blue-600"><i class="fas fa-edit"></i></a>
                                                <form id="delete-form-{{ $maqolah->id }}" 
                                                    action="{{ route('admin.kitab_maqolah.destroy', $maqolah->id) }}" 
                                                    method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmDelete('delete-form-{{ $maqolah->id }}')" 
                                                        class="text-gray-400 hover:text-red-500">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-12 text-gray-400">
                                            <i class="fas fa-search mb-2 text-2xl block"></i>
                                            Tidak ditemukan maqolah yang sesuai.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6">
                    {{ $maqolahs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                ['chapter', 'perPage'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.addEventListener('change', () => document.getElementById('controlsForm').submit());
                });
            });
        </script>
    @endsection
@endsection
