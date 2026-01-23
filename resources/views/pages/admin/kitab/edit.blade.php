@extends('components.layout.admin')

@section('title', 'Edit Kitab - ' . site_name())

@section('content')
    <div class="p-6">
        <div class="mb-6">
            <a href="{{ route('admin.kitab.index') }}" class="text-gray-600 hover:text-primary text-sm flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kitab
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Edit Kitab: {{ $kitab->name }}</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
            <form action="{{ route('admin.kitab.update', $kitab) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kitab <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $kitab->name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Contoh: Nashaihul Ibad">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Penulis</label>
                        <input type="text" name="author" value="{{ old('author', $kitab->author) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Contoh: Syekh Nawawi Al-Bantani">
                        @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                                  placeholder="Deskripsi singkat tentang kitab...">{{ old('description', $kitab->description) }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image</label>
                        @if($kitab->cover_image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $kitab->cover_image) }}" alt="Cover" class="w-32 h-32 object-cover rounded-lg">
                                <p class="text-xs text-gray-500 mt-1">Cover saat ini</p>
                            </div>
                        @endif
                        <input type="file" name="cover_image" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti. Format: JPG, PNG, WebP. Maks: 2MB</p>
                        @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                        <input type="number" name="urutan" value="{{ old('urutan', $kitab->urutan) }}" min="1"
                               class="w-32 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                        @error('urutan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ $kitab->is_active ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary focus:ring-primary">
                        <label for="is_active" class="text-sm text-gray-700">Aktif (tampil di public)</label>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas fa-save mr-2"></i> Update
                    </button>
                    <a href="{{ route('admin.kitab.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
