@extends('components.layout.admin')

@section('title', 'Edit Maqolah')
@section('page-title', 'Edit Maqolah')

@section('content')
    <div class="max-w-4xl mx-auto mt-8">
        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Edit Maqolah #{{ $kitabMaqolah->nomor_maqolah }}</h2>

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

            <form action="{{ route('admin.kitab_maqolah.update', $kitabMaqolah->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Pilih Bab -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bab</label>
                    <select name="chapter_id" id="chapter_id" required
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="">Pilih Bab</option>
                        @foreach($chapters as $ch)
                            <option value="{{ $ch->id }}" {{ old('chapter_id', $kitabMaqolah->chapter_id) == $ch->id ? 'selected' : '' }}>
                                {{ $ch->kitab->name ?? 'Kitab' }} - Bab {{ $ch->nomor_bab }} - {{ $ch->judul_bab }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nomor Maqolah -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Maqolah</label>
                    <input type="number" name="nomor_maqolah" value="{{ old('nomor_maqolah', $kitabMaqolah->nomor_maqolah) }}"
                        class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0"
                        placeholder="Nomor urut maqolah dalam bab" min="1" required>
                </div>

                <!-- Judul -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Maqolah</label>
                    <input type="text" name="judul" value="{{ old('judul', $kitabMaqolah->judul) }}"
                        class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0"
                        placeholder="Contoh: Iman dan Kepedulian Sosial" required>
                </div>

                <!-- Konten -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konten Maqolah</label>
                    <p class="text-xs text-gray-500 mb-2">Gunakan format HTML untuk teks Arab dan terjemahan.</p>
                    <textarea name="konten" id="konten" class="w-full">{{ old('konten', $kitabMaqolah->konten) }}</textarea>
                </div>

                <!-- Tombol -->
                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('admin.kitab_maqolah.index', ['chapter' => $kitabMaqolah->chapter_id]) }}"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</a>
                    <button type="submit"
                        class="px-5 py-2 bg-primary text-white rounded-lg hover:bg-gray-700 transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- CKEditor -->
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        try {
            CKEDITOR.replace('konten', {
                height: 400,
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
