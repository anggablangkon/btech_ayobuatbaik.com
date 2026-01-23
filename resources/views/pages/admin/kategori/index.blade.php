@extends('components.layout.admin')

@section('title', 'Kelola Kategori Donasi - ' . site_name())
@section('page-title', 'Kelola Kategori Donasi')

@section('content')
    @php use Illuminate\Support\Str; @endphp

    <div class="max-w-7xl mx-auto mt-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <h3 class="text-base font-semibold text-gray-900">Daftar Kategori Donasi</h3>

                <a href="{{ route('admin.kategori_donasi.create') }}"
                    class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus"></i> Tambah Kategori
                </a>
            </div>
        </div>

        <!-- Table Desktop -->
        <div class="mt-4 hidden md:block bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-gray-600 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Slug</th>
                            <th class="px-4 py-3 text-left">Deskripsi</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse ($kategories as $kategori)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $kategori->name }}</td>
                                <td class="px-4 py-3">{{ $kategori->slug }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ Str::limit($kategori->deskripsi, 50) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.kategori_donasi.edit', $kategori->id) }}"
                                        class="text-blue-600 hover:text-blue-800 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form id="delete-form-{{ $kategori->id }}" action="{{ route('admin.kategori_donasi.destroy', $kategori->id) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-form-{{ $kategori->id }}', 'Hapus kategori ini?')" 
                                            class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-gray-500">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $kategories->links() }}
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-3 mt-4">
            @forelse ($kategories as $kategori)
                <div class="bg-white rounded-lg shadow p-3 border border-gray-100">
                    <div class="flex justify-between items-start mb-1">
                        <div class="text-sm font-semibold">{{ $kategori->name }}</div>
                    </div>

                    <div class="text-xs text-gray-600">
                        <span class="font-medium">Slug:</span> {{ $kategori->slug }}
                    </div>

                    <div class="text-xs text-gray-600 mt-1">
                        {{ Str::limit($kategori->deskripsi, 80) }}
                    </div>

                    <div class="flex justify-end items-center gap-4 mt-3 text-sm">
                        <a href="{{ route('admin.kategori_donasi.edit', $kategori->id) }}"
                            class="text-blue-600"><i class="fas fa-edit"></i></a>

                        <form id="delete-form-mobile-{{ $kategori->id }}" action="{{ route('admin.kategori_donasi.destroy', $kategori->id) }}" method="POST"
                            class="inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-form-mobile-{{ $kategori->id }}', 'Hapus kategori ini?')"
                            class="text-red-600"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-500">Belum ada kategori.</div>
            @endforelse

            <div class="mt-4">
                {{ $kategories->links() }}
            </div>
        </div>
    </div>
@endsection
