@extends('components.layout.admin')

@section('title', 'Tambah Bab Kitab')
@section('page-title', 'Tambah Bab Kitab')

@section('content')
    <div class="max-w-4xl mx-auto mt-8">
        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Tambah Bab Baru</h2>

            {{-- Alert Error --}}
            @if ($errors->any())
                <div class="p-3 mb-4 bg-red-50 border border-red-200 text-red-700 rounded">
                    <strong>Terdapat kesalahan:</strong>
                    <ul class="list-disc ml-5 mt-2">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.kitab_chapter.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Pilih Kitab -->
                <!-- Pilih Kitab -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kitab <span class="text-red-500">*</span></label>
                    <select name="kitab_id" id="kitab_id" required
                        class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0 bg-white {{ isset($selectedKitabId) ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                        {{ isset($selectedKitabId) ? 'disabled' : '' }}>
                        <option value="">-- Pilih Kitab --</option>
                        @foreach($kitabs as $kitab)
                            <option value="{{ $kitab->id }}" {{ (old('kitab_id') ?? $selectedKitabId) == $kitab->id ? 'selected' : '' }}>
                                {{ $kitab->name }}
                            </option>
                        @endforeach
                    </select>
                    @if(isset($selectedKitabId))
                        <input type="hidden" name="kitab_id" value="{{ $selectedKitabId }}">
                    @endif
                </div>

                <!-- Nomor Bab -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Bab</label>
                    <input type="number" name="nomor_bab" value="{{ old('nomor_bab', $nextNomor) }}"
                        class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0"
                        placeholder="Masukkan nomor bab" min="1" required>
                </div>

                <!-- Judul Bab -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Bab (Optional)</label>
                    <div class="flex items-center gap-2 border-b border-gray-300 focus-within:border-primary">
                        <span class="text-gray-400 text-sm whitespace-nowrap" id="prefix_label">[Kitab] Bab X :</span>
                        <input type="text" name="judul_bab" value="{{ old('judul_bab') }}"
                            class="w-full border-0 focus:ring-0 p-2"
                            placeholder="Contoh: Nasihat yang Berisi Dua Perkara">
                    </div>
                    <p class="text-xs text-gray-400 mt-1 italic">Prefix "[Nama Kitab] Bab [Nomor]" akan ditambahkan secara otomatis.</p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Optional) <span class="text-xs text-gray-400 font-normal">(Maksimal ~4 baris / 400 karakter)</span></label>
                    <textarea name="deskripsi" id="deskripsi" class="w-full">{{ old('deskripsi') }}</textarea>
                </div>

                <!-- Tombol -->
                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ isset($selectedKitabId) ? route('admin.kitab_chapter.index', ['kitab_id' => $selectedKitabId]) : route('admin.kitab_chapter.index') }}"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</a>
                    <button type="submit"
                        class="px-5 py-2 bg-primary text-white rounded-lg hover:bg-gray-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- CKEditor -->
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        try {
            CKEDITOR.replace('deskripsi', {
                height: 200,
                filebrowserUploadUrl: "{{ route('admin.ckeditor.upload', ['_token' => csrf_token()]) }}",
                filebrowserUploadMethod: 'form',
                toolbar: [{
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat']
                    },
                    {
                        name: 'paragraph',
                        items: ['NumberedList', 'BulletedList', 'Blockquote', 'JustifyLeft', 'JustifyCenter',
                            'JustifyRight', 'JustifyBlock'
                        ]
                    },
                    {
                        name: 'insert',
                        items: ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar']
                    },
                    {
                        name: 'styles',
                        items: ['Styles', 'Format', 'Font', 'FontSize']
                    },
                    {
                        name: 'colors',
                        items: ['TextColor', 'BGColor']
                    },
                    {
                        name: 'tools',
                        items: ['Maximize']
                    }
                ],
                bodyClass: 'prose prose-sm text-gray-700',
                width: '100%'
            });
        } catch (e) {
            console.error('CKEditor init error:', e);
        }
    </script>
@endsection
