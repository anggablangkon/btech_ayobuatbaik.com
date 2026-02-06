@extends('components.layout.app')

@section('title', 'Status Donasi - ' . $donation->program->title)  

@section('og_title', 'Status Donasi - ' . $donation->program->title)
@section('og_description', 'Status Donasi - ' . $donation->program->title)
@section('og_url', url()->current())
@section('og_image', asset(site_setting('site_logo', 'img/icon_ABBI.png')))

@section('header-content')
    @include('components.layout.header')
@endsection

@section('content')
    <div class="content pb-20">
        <div class="bg-white p-6 shadow-md rounded-lg mx-auto max-w-lg mt-4">

            {{-- Status Section --}}
            <div class="text-center mb-6">
                @if ($donation->status === 'success')
                    <i class="fas fa-check-circle text-green-500 text-6xl mb-3"></i>
                    <h2 class="text-2xl font-bold text-green-600">Donasi Berhasil!</h2>
                    <p class="text-gray-600 mt-1">Terima kasih atas kontribusi Anda.</p>
                @elseif ($donation->status === 'pending')
                    <i class="fas fa-clock text-yellow-500 text-6xl mb-3"></i>
                    <h2 class="text-2xl font-bold text-yellow-600">Menunggu Pembayaran</h2>
                    <p class="text-gray-600 mt-1">Segera selesaikan pembayaran sebelum batas waktu.</p>
                @elseif ($donation->status === 'unpaid')
                    <i class="fas fa-hourglass-half text-blue-500 text-6xl mb-3"></i>
                    <h2 class="text-2xl font-bold text-blue-600">Belum Dibayar</h2>
                    <p class="text-gray-600 mt-1">Klik tombol di bawah untuk melanjutkan pembayaran.</p>
                @elseif ($donation->status === 'expired')
                    <i class="fas fa-times-circle text-red-500 text-6xl mb-3"></i>
                    <h2 class="text-2xl font-bold text-red-600">Pembayaran Kadaluarsa</h2>
                    <p class="text-gray-600 mt-1">Batas waktu pembayaran telah habis.</p>
                    <p class="text-sm text-gray-500 mt-2">Silakan buat donasi baru untuk program ini.</p>
                @elseif ($donation->status === 'failed')
                    <i class="fas fa-exclamation-triangle text-red-500 text-6xl mb-3"></i>
                    <h2 class="text-2xl font-bold text-red-600">Pembayaran Gagal</h2>
                    <p class="text-gray-600 mt-1">Pembayaran Anda tidak dapat diproses.</p>
                @else
                    <i class="fas fa-question-circle text-gray-500 text-6xl mb-3"></i>
                    <h2 class="text-2xl font-bold text-gray-600">Status Tidak Diketahui</h2>
                @endif
            </div>

            {{-- META PIXEL EVENTS --}}
            @if ($donation->status === 'success')
                <script>
                    // Prevent duplicate events on page refresh
                    const donateEventKey = 'donate_tracked_{{ $donation->donation_code }}';
                    if (!sessionStorage.getItem(donateEventKey)) {
                        fbq('track', 'Donate', {
                            value: {{ $donation->amount }},
                            currency: 'IDR',
                            content_name: '{{ $donation->program->title }}',
                            content_category: 'Donasi',
                            content_ids: ['{{ $donation->program->id }}'],
                            content_type: 'product'
                        }, { eventID: '{{ $donation->donation_code }}' });
                        sessionStorage.setItem(donateEventKey, 'true');
                    }
                </script>
            @endif

            <hr class="my-4">

            {{-- Detail Donasi --}}
            <h3 class="font-bold text-lg mb-3 text-primary">Detail Transaksi</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Kode Donasi:</span>
                    <span class="font-mono text-xs font-semibold">{{ $donation->donation_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Program Donasi:</span>
                    <span class="font-semibold text-right">{{ $donation->program->title }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Nominal Donasi:</span>
                    <span class="font-bold text-xl text-secondary">Rp
                        {{ number_format($donation->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Nama Donatur:</span>
                    <span class="font-medium">{{ $donation->donor_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">No. HP:</span>
                    <span class="font-medium">{{ $donation->donor_phone }}</span>
                </div>
                @if($donation->donor_email)
                <div class="flex justify-between">
                    <span class="text-gray-500">Email:</span>
                    <span class="font-medium text-sm">{{ $donation->donor_email }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center pt-2 border-t">
                    <span class="text-gray-500">Status:</span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold
                        {{ $donation->status === 'success' ? 'bg-green-100 text-green-700' : 
                           ($donation->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                           ($donation->status === 'unpaid' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                        {{ strtoupper($donation->status) }}
                    </span>
                </div>
                @if (in_array($donation->status, ['pending']) && $donation->status_change_at)
                    <div class="flex justify-between text-xs text-red-600 pt-2 border-t bg-red-50 -mx-3 px-3 py-2 rounded">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-clock"></i>
                            Batas Waktu:
                        </span>
                        <span class="font-semibold">
                            {{ $donation->expires_at->translatedFormat('d M Y, H:i') }} WIB
                        </span>
                    </div>
                @endif
            </div>

            {{-- Info Penghapusan Otomatis --}}
            @if ($isDeletable)
                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Data donasi ini akan <strong>dihapus otomatis</strong> karena telah melewati batas 24 jam.
                </div>
            @endif

            {{-- Tombol Bayar (jika unpaid/pending/failed) --}}
            @if (in_array($donation->status, ['unpaid', 'pending', 'failed']) && $snapToken)
                <button id="pay-button"
                    class="w-full block py-3 rounded-lg font-bold text-center bg-secondary text-white hover:bg-goldDark transition shadow-md mt-6 flex items-center justify-center gap-2">
                    <i class="fas fa-credit-card"></i>
                    <span>Bayar Sekarang</span>
                </button>

                {{-- Panduan Pembayaran --}}
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 text-xs">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-info-circle mt-0.5"></i>
                        <div>
                            <p class="font-semibold mb-1">Panduan Pembayaran:</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                <li>Klik tombol "Bayar Sekarang" di atas</li>
                                <li>Pilih metode pembayaran yang tersedia</li>
                                <li>Selesaikan pembayaran sesuai instruksi</li>
                                <li>Notifikasi akan dikirim via WhatsApp</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tombol Aksi Lainnya --}}
            <div class="mt-6 space-y-3">
                @if ($donation->status === 'expired')
                    <a href="{{ route('home.program.show', $donation->program->slug) }}"
                        class="w-full block py-3 rounded-lg font-bold text-center bg-secondary text-white hover:bg-goldDark transition shadow-md">
                        <i class="fas fa-redo mr-2"></i>Donasi Lagi
                    </a>
                @else
                    <a href="{{ route('home.program.show', $donation->program->slug) }}"
                        class="w-full block py-3 rounded-lg font-bold text-center border-2 border-secondary text-secondary hover:bg-secondary hover:text-white transition shadow-md">
                        <i class="fas fa-eye mr-2"></i>Lihat Program Donasi
                    </a>
                @endif
                
                <a href="{{ route('home') }}"
                    class="w-full block py-3 rounded-lg font-semibold text-center border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-home mr-2"></i>Kembali ke Beranda
                </a>
            </div>
        </div>

        {{-- Daftar Donatur Lain --}}
        <div class="bg-white p-6 shadow-md rounded-lg mx-auto max-w-lg mt-4">
            <h3 class="font-bold text-lg mb-4 text-primary flex items-center gap-2">
                <i class="fas fa-users"></i>
                Donatur Lain dalam Program Ini
            </h3>
            <div class="space-y-3">
                @forelse($recentDonation as $donor)
                    <div class="flex items-center justify-between py-2 border-b last:border-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-secondary/20 to-secondary/10 text-secondary flex items-center justify-center text-sm font-bold mr-3 border-2 border-secondary/20">
                                {{ $donor->donor_initial }}
                            </div>
                            <div>
                                <p class="font-medium text-sm">{{ $donor->donor_name }}</p>
                                <p class="text-xs text-gray-500 flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full {{ $donor->status === 'success' ? 'bg-green-500' : ($donor->status === 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                                    <span class="capitalize">{{ $donor->status }}</span>
                                    <span class="mx-1">•</span>
                                    {{ $donor->status_change_at ? $donor->status_change_at->diffForHumans() : $donor->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <p class="font-bold text-secondary text-sm">
                            Rp {{ number_format($donor->amount, 0, ',', '.') }}
                        </p>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">Belum ada donatur lain untuk program ini.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Midtrans Snap JS (hanya jika ada snapToken) --}}
    @if($snapToken)
    @if (config('services.midtrans.is_production'))
        {{-- Script Production --}}
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}">
        </script>
    @else
        {{-- Script Sandbox --}}
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
    @endif
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const payButton = document.getElementById("pay-button");

            payButton.addEventListener("click", function() {
                payButton.disabled = true;
                payButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memuat...';

                // TRACK INITIATE CHECKOUT
                fbq('track', 'InitiateCheckout', {
                    value: {{ $donation->amount }},
                    currency: 'IDR',
                    content_name: '{{ $donation->program->title }}',
                    content_ids: ['{{ $donation->program->id }}'],
                    content_type: 'product'
                });

                snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        window.location.reload();
                    },
                    onPending: function(result) {
                        window.location.reload();
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal. Silakan coba lagi.');
                        payButton.disabled = false;
                        payButton.innerHTML = '<i class="fas fa-credit-card"></i> <span>Bayar Sekarang</span>';
                    },
                    onClose: function() {
                        payButton.disabled = false;
                        payButton.innerHTML = '<i class="fas fa-credit-card"></i> <span>Bayar Sekarang</span>';
                    }
                });
            });
        });
    </script>
    @endif
@endsection