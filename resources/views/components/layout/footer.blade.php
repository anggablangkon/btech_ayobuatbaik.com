<footer class="bg-primary text-white py-8 px-4">
    <div class="grid grid-cols-1 gap-6">
        <div>
            <h3 class="text-lg font-bold mb-3 text-white"><span class="text-secondary">{{ site_setting('site_name_highlight', 'Ayo') }}</span>{{ site_setting('site_name_rest', 'buatbaik') }}</h3>
            <p class="text-gray-300 text-sm">
                {{ site_setting('footer_description', 'Platform donasi digital yang menghubungkan para dermawan dengan berbagai program kemanusiaan.') }}
            </p>
        </div>

        <div>
            <h4 class="font-semibold mb-3 text-goldLight">Tautan Cepat</h4>
            <ul class="space-y-2">
                <li><a href="#" class="text-gray-300 text-sm hover:text-secondary transition-colors">Tentang Kami</a>
                </li>
                <li><a href="#program" class="text-gray-300 text-sm hover:text-secondary transition-colors">Program</a>
                </li>
                <li><a href="#berita" class="text-gray-300 text-sm hover:text-secondary transition-colors">Berita</a>
                </li>
            </ul>
        </div>

        <div class="border-t border-gray-700 pt-6 text-center text-gray-400 text-xs">
            <p>{{ site_setting('footer_copyright', '© ' . date('Y') . ' Platform Donasi. All rights reserved.') }}</p>
        </div>
    </div>
</footer>
