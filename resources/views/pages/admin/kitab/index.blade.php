@extends('components.layout.admin')

@section('title', 'Kelola Kitab - ' . site_name())

@section('content')
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Kelola Kitab</h1>
                <p class="text-gray-600 text-sm">Daftar semua kitab yang tersedia</p>
            </div>
            <a href="{{ route('admin.kitab.create') }}" 
               class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg font-medium text-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Kitab
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Desktop Table -->
        <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kitab</th>
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Penulis</th>
                        <th class="text-center px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Bab</th>
                        <th class="text-center px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-center px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($kitabs as $index => $kitab)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($kitab->cover_image)
                                        <img src="{{ asset('storage/' . $kitab->cover_image) }}" 
                                             alt="{{ $kitab->name }}" 
                                             class="w-12 h-12 object-cover rounded-lg">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-book text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $kitab->name }}</p>
                                        <p class="text-xs text-gray-500">{{ Str::limit($kitab->description, 50) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $kitab->author ?: '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $kitab->chapters_count }} Bab
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($kitab->is_active)
                                    <span class="bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs font-medium">Aktif</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.kitab_chapter.index', ['kitab_id' => $kitab->id]) }}" 
                                       class="bg-blue-100 hover:bg-blue-200 text-blue-600 p-2 rounded-lg transition-colors"
                                       title="Kelola Isi (Bab & Maqolah)">
                                        <i class="fas fa-list-ul"></i>
                                    </a>
                                    <a href="{{ route('admin.kitab.edit', $kitab) }}" 
                                       class="bg-yellow-100 hover:bg-yellow-200 text-yellow-600 p-2 rounded-lg transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.kitab.destroy', $kitab) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin hapus kitab ini? Semua bab dan maqolah di dalamnya akan ikut terhapus!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-lg transition-colors"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-book text-4xl mb-3 text-gray-300"></i>
                                <p class="font-medium">Belum ada kitab</p>
                                <p class="text-sm">Klik tombol "Tambah Kitab" untuk menambahkan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-4">
            @forelse($kitabs as $kitab)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-start gap-4">
                        @if($kitab->cover_image)
                            <img src="{{ asset('storage/' . $kitab->cover_image) }}" 
                                 alt="{{ $kitab->name }}" 
                                 class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                        @else
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book text-2xl text-gray-400"></i>
                            </div>
                        @endif
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <h3 class="font-bold text-gray-900 truncate pr-2">{{ $kitab->name }}</h3>
                                @if($kitab->is_active)
                                    <span class="bg-green-100 text-green-600 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase">Aktif</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase">Nonaktif</span>
                                @endif
                            </div>
                            
                            <p class="text-sm text-gray-600 mt-1">{{ $kitab->author ?: 'Penulis tidak diketahui' }}</p>
                            
                            <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                                <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded">
                                    <i class="fas fa-layer-group mr-1"></i> {{ $kitab->chapters_count }} Bab
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                        <a href="{{ route('admin.kitab_chapter.index', ['kitab_id' => $kitab->id]) }}" 
                           class="text-sm font-semibold text-blue-600 hover:underline">
                            Kelola Isi
                        </a>
                        
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.kitab.edit', $kitab) }}" 
                               class="bg-yellow-50 text-yellow-600 p-2 rounded-lg hover:bg-yellow-100 transition-colors">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.kitab.destroy', $kitab) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Yakin hapus kitab ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-white rounded-xl border border-dashed border-gray-300">
                    <i class="fas fa-book text-3xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500">Belum ada kitab</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
