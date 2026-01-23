@extends('components.layout.admin')

@section('title', 'Tambah Donasi Manual - ' . site_name())
@section('page-title', 'Tambah Donasi Manual')

@section('content')
    <div class="max-w-3xl mx-auto mt-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">

            {{-- Header --}}
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900">Input Donasi Manual</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Untuk donasi yang diterima melalui transfer bank manual atau metode lainnya
                </p>
            </div>

            {{-- Form --}}
            <form action="{{ route('admin.donasi.storeManual') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Program Donasi --}}
                <div>
                    <label for="program_donasi_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Program Donasi <span class="text-red-500">*</span>
                    </label>
                    <select name="program_donasi_id" id="program_donasi_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-secondary focus:border-transparent @error('program_donasi_id') border-red-500 @enderror">
                        <option value="">-- Pilih Program --</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}"
                                {{ old('program_donasi_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_donasi_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Donatur --}}
                <div>
                    <label for="donor_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Donatur <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="donor_name" id="donor_name" value="{{ old('donor_name') }}" required
                        placeholder="John Doe"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-secondary focus:border-transparent @error('donor_name') border-red-500 @enderror">
                    @error('donor_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="donor_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor HP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="donor_phone" id="donor_phone" value="{{ old('donor_phone') }}" required
                        placeholder="08123456789"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-secondary focus:border-transparent @error('donor_phone') border-red-500 @enderror">
                    @error('donor_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email (Optional) --}}
                <div>
                    <label for="donor_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email (Opsional)
                    </label>
                    <input type="email" name="donor_email" id="donor_email" value="{{ old('donor_email') }}"
                        placeholder="johndoe@example.com"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-secondary focus:border-transparent @error('donor_email') border-red-500 @enderror">
                    @error('donor_email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nominal Donasi --}}
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Nominal Donasi <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <span
                            class="text-gray-700 font-medium text-sm bg-gray-50 px-3 py-2.5 border border-r-0 border-gray-300 rounded-l-lg">
                            Rp
                        </span>
                        <input type="text" name="amount_display" id="amount_display" value="{{ old('amount') ? number_format(old('amount'), 0, ',', '.') : '' }}" required
                            placeholder="100000"
                            class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2.5 focus:ring-2 focus:ring-secondary focus:border-transparent @error('amount') border-red-500 @enderror">
                        <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Minimal donasi Rp 1.000 (gunakan titik sebagai pemisah ribuan, contoh: 20.008)</p>
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tipe Donasi --}}
                <div>
                    <label for="donation_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Metode Pembayaran
                    </label>
                    <select name="donation_type" id="donation_type"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-secondary focus:border-transparent">
                        <option value="manual">Transfer Bank Manual</option>
                        <option value="cash">Tunai</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                {{-- Note --}}
                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea name="note" id="note" rows="3" placeholder="Catatan tambahan tentang donasi ini..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-secondary focus:border-transparent @error('note') border-red-500 @enderror">{{ old('note') }}</textarea>
                    @error('note')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 pt-4 border-t">
                    <a href="{{ route('admin.donasi.index') }}"
                        class="flex-1 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-200 transition text-center font-medium">
                        Batal
                    </a>
                    <button type="submit"
                        class="flex-1 bg-secondary text-white px-4 py-2.5 rounded-lg hover:opacity-90 transition font-medium">
                        Simpan Donasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountDisplay = document.getElementById('amount_display');
            const amountHidden = document.getElementById('amount');
            const form = amountDisplay.closest('form');

            // Format angka dengan pemisah ribuan (titik)
            function formatNumber(value) {
                // Hapus semua karakter non-digit
                const numericValue = value.replace(/\D/g, '');
                
                // Format dengan pemisah ribuan
                return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Event saat user mengetik
            amountDisplay.addEventListener('input', function(e) {
                const cursorPosition = e.target.selectionStart;
                const oldValue = e.target.value;
                const oldLength = oldValue.length;
                
                // Format value
                const formatted = formatNumber(oldValue);
                e.target.value = formatted;
                
                // Update hidden input dengan angka murni (tanpa titik)
                amountHidden.value = formatted.replace(/\./g, '');
                
                // Adjust cursor position setelah formatting
                const newLength = formatted.length;
                const newPosition = cursorPosition + (newLength - oldLength);
                e.target.setSelectionRange(newPosition, newPosition);
            });

            // Pastikan value yang dikirim adalah angka murni (tanpa titik)
            form.addEventListener('submit', function(e) {
                // Ambil value dari display input, hapus semua titik
                const cleanValue = amountDisplay.value.replace(/\./g, '');
                amountHidden.value = cleanValue;
                
                // Validasi minimal 1000
                if (parseInt(cleanValue) < 1000) {
                    e.preventDefault();
                    alert('Minimal donasi adalah Rp 1.000');
                    return false;
                }
            });
        });
    </script>
@endsection
