<div id="pwa-install-prompt" 
    class="fixed top-0 left-0 right-0 z-[9999] transform -translate-y-[120%] transition-transform duration-700 ease-[cubic-bezier(0.34,1.56,0.64,1)] will-change-transform {{-- Hidden by default --}}">
    
    {{-- Container: Solid White, Rounded Bottom, Shadow --}}
    <div class="mx-auto max-w-md bg-white shadow-2xl rounded-b-[2rem] border-x border-b border-gray-100 p-5 relative overflow-hidden">
        
        <div class="flex items-center gap-4 relative z-10">
            {{-- Icon (Black Metallic Background) --}}
            <div class="flex-shrink-0 bg-gradient-to-br from-gray-800 to-black rounded-2xl p-2 shadow-lg border border-gray-100">
                <img src="{{ asset('icon ABBI.png') }}" alt="App Icon" class="w-12 h-12 object-contain drop-shadow-sm">
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0 pt-1">
                <h3 class="text-sm font-black text-gray-900 leading-none mb-1 tracking-tight">{{ site_name() }} App</h3>
                <p id="pwa-desc" class="text-[11px] text-gray-500 font-medium leading-snug mb-3">
                    Nikmati pengalaman lebih cepat & lancar.
                </p>

                {{-- Action Buttons --}}
                <div class="flex gap-2">
                    {{-- Install Button (Gold Gradient) --}}
                    <button id="pwa-install-yes" class="flex-1 bg-gradient-to-r from-primary to-yellow-600 text-white text-[11px] font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-yellow-500/20 hover:shadow-yellow-500/40 active:scale-95 transition-all text-center tracking-wide">
                        INSTALL SEKARANG
                    </button>
                    {{-- Cancel Button --}}
                    <button id="pwa-install-no" class="flex-none bg-gray-50 text-gray-400 text-[11px] font-bold py-2.5 px-4 rounded-xl border border-gray-200 hover:bg-gray-100 hover:text-gray-600 active:scale-95 transition-all">
                        Nanti
                    </button>
                </div>
            </div>

            {{-- Close Button --}}
            <button id="pwa-close-btn" class="absolute -top-1 -right-1 p-2 text-gray-300 hover:text-gray-500 transition-colors">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        {{-- Fallback Instruction (Hidden by default) --}}
        <div id="manual-instruction" class="hidden mt-3 pt-3 border-t border-gray-50 text-center">
             <p id="instruction-text" class="text-[10px] text-gray-500 animate-pulse">
                <!-- Text will be injected via JS -->
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let deferredPrompt;
        const installPrompt = document.getElementById('pwa-install-prompt');
        const installBtn = document.getElementById('pwa-install-yes');
        const manualInstruction = document.getElementById('manual-instruction');
        const instructionText = document.getElementById('instruction-text');
        const installPromptDesc = document.getElementById('pwa-desc');
        
        // Detect Device
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || (window.navigator.standalone === true);

        // Check if user already dismissed prompt recently (24 hours)
        const dismissedAt = localStorage.getItem('pwa_prompt_dismissed_at');
        const oneDayAgo = Date.now() - (24 * 60 * 60 * 1000);
        const wasDismissedRecently = dismissedAt && parseInt(dismissedAt) > oneDayAgo;

        // JANGAN tampilkan jika: Sudah di install ATAU baru ditutup <24 jam
        if (isStandalone || wasDismissedRecently) {
            console.log('[PWA Prompt] Tidak ditampilkan (Standalone: ' + isStandalone + ', DismissedRecently: ' + wasDismissedRecently + ')');
            return; // Exit early, jangan jalankan kode di bawah
        }

        // 1. Listen for Native Android Event (Always, just in case)
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e; // Save it!
        });

        // 2. FORCE SHOW (Blindly)
        // Kalau bukan standalone DAN belum dismiss, munculkan setelah 2 detik.
        setTimeout(() => {
            showPrompt();
        }, 2000);

        function showPrompt() {
            installPrompt.classList.remove('-translate-y-[120%]');
        }

        function hidePrompt() {
            installPrompt.classList.add('-translate-y-[120%]');
            // Simpan waktu dismiss agar tidak muncul lagi selama 24 jam
            localStorage.setItem('pwa_prompt_dismissed_at', Date.now().toString());
        }

        installBtn.addEventListener('click', async () => {
            
            // SKENARIO 1: Android Native (Event Ready)
            // Ini kondisi ideal (HTTPS OK, Chrome OK)
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                deferredPrompt = null;
                hidePrompt();
                return;
            } 
            
            // SKENARIO 2: iOS (iPhone)
            if (isIOS) {
                showManualGuide(
                    'IKUTI PETUNJUK DI BAWAH 👇',
                    'Ketuk tombol <span class="font-bold text-blue-500"><i class="fas fa-share-square"></i> Share</span> dibawah, <br> lalu pilih <span class="font-bold text-gray-800"><i class="fas fa-plus-square"></i> Tambah ke Layar Utama</span>'
                );
            }
            // SKENARIO 3: Android "Bandel" (Chrome/Browser Lain yg Eventnya Gagal)
            // Biasanya karena HTTP atau browser non-standard.
            else {
                showManualGuide(
                    'IKUTI PETUNJUK MANUAL 👇',
                    'Ketuk <span class="font-bold text-gray-800">Titik Tiga (⋮)</span> di pojok kanan atas, <br> lalu pilih <span class="font-bold text-gray-800"><i class="fas fa-mobile-alt"></i> Install App</span> atau <span class="font-bold text-gray-800">Tambahkan ke Layar Utama</span>'
                );
            }
        });

        // Helper untuk ubah tampilan jadi panduan
        function showManualGuide(btnText, htmlInstruction) {
            installBtn.textContent = btnText;
            installBtn.classList.remove('bg-gradient-to-r', 'from-primary', 'to-yellow-600');
            installBtn.classList.add('bg-gray-800', 'text-white', 'cursor-default');
            
            manualInstruction.classList.remove('hidden');
            instructionText.innerHTML = htmlInstruction;
            
            installPromptDesc.textContent = "Browser ini perlu instalasi manual:";
        }

        document.getElementById('pwa-install-no').addEventListener('click', hidePrompt);
        document.getElementById('pwa-close-btn').addEventListener('click', hidePrompt);
    });
</script>
