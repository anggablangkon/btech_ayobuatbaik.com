@extends('components.layout.admin')

@section('title', 'Pengaturan Situs - Admin')

@section('page-title', 'Pengaturan Situs')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Konfigurasi Website</h3>
            <p class="text-sm text-gray-500 mt-1">Atur nama, logo, dan pengaturan lainnya untuk website Anda.</p>
        </div>

        @if (session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            {{-- Brand Name --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Brand (Highlight)</label>
                    <input type="text" name="site_name_highlight" 
                           value="{{ $settings['site_name_highlight'] ?? 'Ayo' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="Ayo">
                    <p class="text-xs text-gray-400 mt-1">Bagian yang diwarnai (misal: <span class="text-secondary font-bold">Ayo</span>)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Brand (Sisa)</label>
                    <input type="text" name="site_name_rest" 
                           value="{{ $settings['site_name_rest'] ?? 'buatbaik' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="buatbaik">
                    <p class="text-xs text-gray-400 mt-1">Bagian normal (misal: buatbaik)</p>
                </div>
            </div>

            {{-- Site Title & URL --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Website</label>
                    <input type="text" name="site_title" 
                           value="{{ $settings['site_title'] ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="Ayobuatbaik - Platform Donasi Digital">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL Website</label>
                    <input type="url" name="site_url" 
                           value="{{ $settings['site_url'] ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="https://example.com">
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Website</label>
                <textarea name="site_description" rows="2"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                          placeholder="Deskripsi singkat website...">{{ $settings['site_description'] ?? '' }}</textarea>
            </div>

            {{-- Logo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Logo Website</label>
                <div class="flex items-center gap-4">
                    @if (!empty($settings['site_logo']))
                        <img src="{{ asset($settings['site_logo']) }}" alt="Logo" class="h-16 w-16 object-contain border rounded-lg bg-gray-50 p-2">
                    @endif
                    <input type="file" name="site_logo" 
                           class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                </div>
                <p class="text-xs text-gray-400 mt-1">Format: PNG, JPG, WEBP. Maks: 1MB</p>
            </div>

            <hr class="border-gray-200">

            {{-- Meta Pixel --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Facebook Meta Pixel ID</label>
                <input type="text" name="meta_pixel_id" 
                       value="{{ $settings['meta_pixel_id'] ?? '' }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="1234567890123456">
                <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak menggunakan Facebook Pixel</p>
            </div>

            {{-- WhatsApp --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp</label>
                    <input type="text" name="whatsapp_number" 
                           value="{{ $settings['whatsapp_number'] ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="6281234567890">
                    <p class="text-xs text-gray-400 mt-1">Format: 628xxx (tanpa + atau spasi)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pesan Default WhatsApp</label>
                    <input type="text" name="whatsapp_message" 
                           value="{{ $settings['whatsapp_message'] ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="Halo, saya ingin bertanya...">
                </div>
            </div>

            <hr class="border-gray-200">

            {{-- Footer --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Footer</label>
                <textarea name="footer_description" rows="2"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                          placeholder="Deskripsi di bagian footer...">{{ $settings['footer_description'] ?? '' }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Teks Copyright</label>
                <input type="text" name="footer_copyright" 
                       value="{{ $settings['footer_copyright'] ?? '' }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="© 2025 Nama Website. All rights reserved.">
            </div>

            <hr class="border-gray-200">

            {{-- Theme Colors --}}
            <div>
                <h4 class="font-bold text-gray-800 mb-4">Pengaturan Tema (Warna)</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Utama (Primary)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="theme_primary" id="input_theme_primary"
                                   value="{{ $settings['theme_primary'] ?? '#242124' }}"
                                   class="h-10 w-16 p-1 border border-gray-300 rounded cursor-pointer"
                                   oninput="document.getElementById('text_theme_primary').value = this.value">
                            <input type="text" id="text_theme_primary" value="{{ $settings['theme_primary'] ?? '#242124' }}" 
                                   class="w-24 text-sm p-2 bg-white rounded border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="#000000"
                                   oninput="let v=this.value; if(!v.startsWith('#')) v='#'+v; if(/^#[0-9A-F]{6}$/i.test(v)) document.getElementById('input_theme_primary').value = v;">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Warna dasar web (footer, tombol gelap).</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Sekunder (Secondary)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="theme_secondary" id="input_theme_secondary"
                                   value="{{ $settings['theme_secondary'] ?? '#8a6d3b' }}"
                                   class="h-10 w-16 p-1 border border-gray-300 rounded cursor-pointer"
                                   oninput="document.getElementById('text_theme_secondary').value = this.value">
                             <input type="text" id="text_theme_secondary" value="{{ $settings['theme_secondary'] ?? '#8a6d3b' }}" 
                                    class="w-24 text-sm p-2 bg-white rounded border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="#000000"
                                    oninput="let v=this.value; if(!v.startsWith('#')) v='#'+v; if(/^#[0-9A-F]{6}$/i.test(v)) document.getElementById('input_theme_secondary').value = v;">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Warna aksen utama (highlight, tombol aksi).</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Indikator (Success/WA)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="theme_hijau" id="input_theme_hijau"
                                   value="{{ $settings['theme_hijau'] ?? '#16a34a' }}"
                                   class="h-10 w-16 p-1 border border-gray-300 rounded cursor-pointer"
                                   oninput="document.getElementById('text_theme_hijau').value = this.value">
                             <input type="text" id="text_theme_hijau" value="{{ $settings['theme_hijau'] ?? '#16a34a' }}" 
                                    class="w-24 text-sm p-2 bg-white rounded border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="#000000"
                                    oninput="let v=this.value; if(!v.startsWith('#')) v='#'+v; if(/^#[0-9A-F]{6}$/i.test(v)) document.getElementById('input_theme_hijau').value = v;">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Warna untuk tombol WhatsApp dan pesan sukses.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Variasi Terang</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="theme_gold_light" id="input_theme_gold_light"
                                   value="{{ $settings['theme_gold_light'] ?? '#F7EF8A' }}"
                                   class="h-10 w-16 p-1 border border-gray-300 rounded cursor-pointer"
                                   oninput="document.getElementById('text_theme_gold_light').value = this.value">
                             <input type="text" id="text_theme_gold_light" value="{{ $settings['theme_gold_light'] ?? '#F7EF8A' }}" 
                                    class="w-24 text-sm p-2 bg-white rounded border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="#000000"
                                    oninput="let v=this.value; if(!v.startsWith('#')) v='#'+v; if(/^#[0-9A-F]{6}$/i.test(v)) document.getElementById('input_theme_gold_light').value = v;">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Warna variasi gradasi terang.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Variasi Gelap</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="theme_gold_dark" id="input_theme_gold_dark"
                                   value="{{ $settings['theme_gold_dark'] ?? '#B8860B' }}"
                                   class="h-10 w-16 p-1 border border-gray-300 rounded cursor-pointer"
                                   oninput="document.getElementById('text_theme_gold_dark').value = this.value">
                             <input type="text" id="text_theme_gold_dark" value="{{ $settings['theme_gold_dark'] ?? '#B8860B' }}" 
                                    class="w-24 text-sm p-2 bg-white rounded border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="#000000"
                                    oninput="let v=this.value; if(!v.startsWith('#')) v='#'+v; if(/^#[0-9A-F]{6}$/i.test(v)) document.getElementById('input_theme_gold_dark').value = v;">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Warna variasi gradasi gelap (hover).</p>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end pt-4">
                <button type="submit" 
                        class="bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-dark transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection
