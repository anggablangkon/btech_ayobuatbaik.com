@extends('components.layout.admin')

@section('page-title', $broadcast ? 'Resend / Edit Broadcast' : 'Buat Broadcast Baru')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.broadcast.index') }}" class="text-gray-500 hover:text-primary flex items-center gap-1 text-sm mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Riwayat
        </a>
        <h1 class="text-2xl font-bold text-gray-800">{{ $broadcast ? 'Resend / Edit Broadcast' : 'Buat Broadcast WhatsApp' }}</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
        <form action="{{ route('admin.broadcast.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- Subject --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Internal</label>
                <input type="text" name="subject" value="{{ old('subject', $broadcast->subject ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="Contoh: Info Kajian Akbar Januari">
                <p class="text-xs text-gray-400 mt-1">Hanya untuk catatan admin, tidak dikirim ke user.</p>
                @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Target --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Target Penerima</label>
                <select name="target" id="targetSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="test" {{ old('target', $broadcast->target ?? '') == 'test' ? 'selected' : '' }}>Tes Kirim (Ke Nomor Sendiri)</option>
                    <option value="donors" {{ old('target', $broadcast->target ?? '') == 'donors' ? 'selected' : '' }}>Semua Donatur</option>
                    <option value="csv_audience" {{ old('target', $broadcast->target ?? '') == 'csv_audience' ? 'selected' : '' }}>Upload CSV / Excel (Jamaah)</option>
                </select>
                @error('target') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Test Number Input --}}
            <div id="testNumberField" class="mb-6 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp Tes</label>
                <input type="text" name="test_number" value="{{ old('test_number', isset($broadcast->target_data['test_number']) ? $broadcast->target_data['test_number'] : '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="08123456789">
                @error('test_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- CSV Upload Input --}}
            <div id="csvNumberField" class="mb-6 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload File CSV</label>
                <input type="file" name="csv_file" accept=".csv, .txt"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary hover:file:bg-primary-100">
                <p class="text-xs text-gray-400 mt-1">Format: File .csv atau .txt. Sistem akan otomatis mendeteksi nomor HP di dalamnya.</p>
                @error('csv_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Image --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Gambar (Opsional)</label>
                
                @if(isset($broadcast) && $broadcast->image_path)
                    <div class="mb-2 p-2 border rounded bg-gray-50 inline-block">
                        <p class="text-xs text-gray-500 mb-1">Gambar Sebelumnya:</p>
                        <img src="{{ asset($broadcast->image_path) }}" alt="Preview" class="h-24 rounded object-cover">
                        {{-- Hidden input to keep old image if not replaced? 
                             For simplicity, we require re-upload if they want to change. 
                             Or we can handle logic in controller "if no new image, reuse old path".
                             Let's keep it simple: "Upload new to replace". 
                             Wait, if they just want to resend SAAT INI without upload, we need to handle that.
                             I'll add a hidden input 'old_image_path'.
                        --}}
                        <input type="hidden" name="old_image_path" value="{{ $broadcast->image_path }}">
                    </div>
                @endif

                <input type="file" name="image" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary hover:file:bg-primary-100">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG. Max 2MB. @if(isset($broadcast) && $broadcast->image_path) (Biarkan kosong jika ingin pakai gambar lama) @endif</p>
                @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Message --}}
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Isi Pesan</label>
                <textarea name="message" rows="6"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                          placeholder="Ketik pesan broadcast Anda di sini...">{{ old('message', $broadcast->message ?? '') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Tips: Gunakan *text* untuk bold, _text_ untuk italic.</p>
                @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Submit --}}
            <div class="flex justify-end">
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-lg font-medium transition-colors shadow-lg shadow-primary/30">
                    Kirim Broadcast 🚀
                </button>
            </div>
        </form>
    </div>

    <script>
        const targetSelect = document.getElementById('targetSelect');
        const testNumberField = document.getElementById('testNumberField');
        const csvNumberField = document.getElementById('csvNumberField');

        function toggleTestField() {
            testNumberField.style.display = 'none';
            csvNumberField.style.display = 'none';

            if (targetSelect.value === 'test') {
                testNumberField.style.display = 'block';
            } else if (targetSelect.value === 'csv_audience') {
                csvNumberField.style.display = 'block';
            }
        }

        targetSelect.addEventListener('change', toggleTestField);
        toggleTestField(); // Init
    </script>
@endsection
