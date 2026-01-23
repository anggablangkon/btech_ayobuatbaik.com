@extends('components.layout.admin')

@section('title', 'Kelola Bab Kitab - ' . site_name())
@section('page-title', 'Kelola Bab Kitab')

@section('content')
    @php use Illuminate\Support\Str; @endphp

    <div class="max-w-7xl mx-auto mt-8">
        <!-- Header & Filter -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-base font-semibold text-gray-900">Daftar Bab Kitab</h3>
                    <a href="{{ route('admin.kitab_chapter.create') }}"
                        class="ml-2 inline-flex items-center gap-2 bg-primary text-white px-3 py-2 rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-plus"></i> <span>Tambah Bab</span>
                    </a>
                </div>

                <!-- Filter Form -->
                <form id="controlsForm" method="GET" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                    <input type="text" name="search" placeholder="Cari judul bab..." value="{{ request('search') }}"
                        class="w-full sm:w-64 border px-3 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-primary" />

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

        <!-- Table -->
        <div class="mt-4">
            <!-- Desktop Table -->
            <div class="hidden md:block bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-gray-600 bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left w-16">No</th>
                                <th class="px-4 py-3 text-left">Judul Bab</th>
                                <th class="px-4 py-3 text-left">Deskripsi</th>
                                <th class="px-4 py-3 text-center">Maqolah</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($chapters as $chapter)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-semibold text-primary">{{ $chapter->nomor_bab }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold">Nashaihul Ibad Bab {{ $chapter->nomor_bab }}{{ $chapter->judul_bab ? ' : ' . $chapter->judul_bab : '' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ Str::limit(strip_tags($chapter->deskripsi), 60) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('admin.kitab_maqolah.index', ['chapter' => $chapter->id]) }}"
                                            class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800">
                                            <span class="font-semibold">{{ $chapter->maqolahs_count }}</span>
                                            <i class="fas fa-external-link-alt text-xs"></i>
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.kitab_chapter.edit', $chapter->id) }}"
                                            class="text-blue-600 hover:text-blue-800 mr-3"><i class="fas fa-edit"></i></a>
                                        <form id="delete-form-{{ $chapter->id }}" action="{{ route('admin.kitab_chapter.destroy', $chapter->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-{{ $chapter->id }}', 'Hapus bab ini? Semua maqolah di dalamnya juga akan terhapus.')" 
                                                class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-gray-500">Belum ada bab.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $chapters->links() }}
                </div>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-3">
                @forelse($chapters as $chapter)
                    <div class="bg-white rounded-lg shadow p-4 border border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-primary font-bold">{{ $chapter->nomor_bab }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-sm">Nashaihul Ibad Bab {{ $chapter->nomor_bab }}{{ $chapter->judul_bab ? ' : ' . $chapter->judul_bab : '' }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ Str::limit($chapter->deskripsi, 50) }}</div>
                                <div class="flex items-center justify-between mt-3">
                                    <a href="{{ route('admin.kitab_maqolah.index', ['chapter' => $chapter->id]) }}"
                                        class="text-xs text-blue-600">
                                        <i class="fas fa-scroll mr-1"></i> {{ $chapter->maqolahs_count }} Maqolah
                                    </a>
                                    <div class="flex gap-3 text-xs">
                                        <a href="{{ route('admin.kitab_chapter.edit', $chapter->id) }}" class="text-blue-600">
                                            <i class="fas fa-edit"></i></a>
                                        <form id="delete-form-mobile-{{ $chapter->id }}" action="{{ route('admin.kitab_chapter.destroy', $chapter->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-mobile-{{ $chapter->id }}', 'Hapus bab ini?')" 
                                                class="text-red-600">
                                                <i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-500">Belum ada bab.</div>
                @endforelse

                <div class="mt-4">
                    {{ $chapters->links() }}
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                ['perPage'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.addEventListener('change', () => document.getElementById('controlsForm').submit());
                });
            });
        </script>
    @endsection
@endsection
