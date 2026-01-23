@extends('components.layout.admin')

@section('title', 'Kelola Berita - ' . site_name())
@section('page-title', 'Kelola Berita')

@section('content')
    @php use Illuminate\Support\Str; @endphp

    <div class="max-w-7xl mx-auto mt-8">
        <!-- Header & Filter -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-base font-semibold text-gray-900">Daftar Berita</h3>
                    <a href="{{ route('admin.berita.create') }}"
                        class="ml-2 inline-flex items-center gap-2 bg-primary text-white px-3 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-plus"></i> <span>Tambah</span>
                    </a>
                </div>

                <!-- Filter Form -->
                <form id="controlsForm" method="GET" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                    <input type="text" name="search" placeholder="Cari judul..." value="{{ request('search') }}"
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

                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Sort</label>
                        <select name="sort" id="sort" class="border rounded px-2 py-2">
                            <option value="tanggal" {{ request('sort') == 'tanggal' ? 'selected' : '' }}>Tanggal</option>
                            <option value="judul" {{ request('sort') == 'judul' ? 'selected' : '' }}>Judul</option>
                        </select>

                        <select name="direction" id="direction" class="border rounded px-2 py-2">
                            <option value="desc" {{ request('direction', 'desc') == 'desc' ? 'selected' : '' }}>Desc
                            </option>
                            <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Asc</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="bg-secondary text-white px-3 py-2 rounded-md hover:opacity-95 transition">Terapkan</button>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="mt-4">
            <!-- Desktop Table -->
            <div class="hidden md:block bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-gray-600 bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Judul</th>
                                <th class="px-4 py-3 text-left">Deskripsi</th>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($berita as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-14 h-14 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                                @if ($item->gambar)
                                                    <img src="{{ asset('storage/' . $item->gambar) }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div
                                                        class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-semibold">{{ Str::limit($item->judul, 40, '...') }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-gray-600">
                                        {{ Str::limit($item->deskripsi_singkat, 80, '...') }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.berita.edit', $item->id) }}"
                                            class="text-blue-600 hover:text-blue-800 mr-3"><i class="fas fa-edit"></i></a>
                                        <form id="delete-form-{{ $item->id }}" action="{{ route('admin.berita.destroy', $item->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-{{ $item->id }}', 'Hapus berita ini?')" 
                                                class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-gray-500">Belum ada berita.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $berita->links() }}
                </div>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-3">
                @forelse($berita as $item)
                    <div class="bg-white rounded-lg shadow p-3 border border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="w-20 h-16 rounded overflow-hidden flex-shrink-0 bg-gray-100">
                                @if ($item->gambar)
                                    <img src="{{ asset('storage/' . $item->gambar) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-sm">{{ Str::limit($item->judul, 35, '...') }}</div>
                                <div class="text-xs text-gray-600 mt-1">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ Str::limit($item->deskripsi_singkat, 50) }}
                                </div>
                                <div class="mt-2 flex justify-end gap-3 text-xs">
                                    <a href="{{ route('admin.berita.edit', $item->id) }}" class="text-blue-600">
                                        <i class="fas fa-edit"></i></a>
                                    <form id="delete-form-mobile-{{ $item->id }}" action="{{ route('admin.berita.destroy', $item->id) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-form-mobile-{{ $item->id }}', 'Hapus berita ini?')" 
                                            class="text-red-600">
                                            <i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-500">Belum ada berita.</div>
                @endforelse

                <div class="mt-4">
                    {{ $berita->links() }}
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                ['perPage', 'sort', 'direction'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.addEventListener('change', () => document.getElementById('controlsForm')
                    .submit());
                });
            });
        </script>
    @endsection
@endsection
