@extends('components.layout.app')

@section('title', 'Selesaikan Pembayaran - ' . site_name())
@section('og_title', 'Selesaikan Pembayaran - ' . site_name())
@section('og_description', 'Selesaikan Pembayaran - ' . site_name())
@section('og_url', url()->current())
@section('og_image', asset(site_setting('site_logo', 'img/icon_ABBI.png')))

@section('content')
    <div class="min-h-[calc(100vh-56px)] flex items-center justify-center bg-gray-50 px-3 pb-[90px]">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
            {{-- Brand Header --}}
            <div class="text-center mb-8">
                <p class="text-sm text-gray-500 mt-2">Selesaikan Pembayaran Donasi</p>
            </div>

            {{-- Donation Info Card --}}
            <div class="bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-xl p-6 mb-6 border border-secondary/20">
                {{-- Program Icon --}}
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-secondary/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-hand-holding-heart text-secondary text-2xl"></i>
                    </div>
                </div>

                {{-- Program Details --}}
                <div class="space-y-3 text-center">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Program Donasi</p>
                        <h3 class="text-lg font-bold text-gray-900">{{ $donation->program->title }}</h3>
                    </div>

                    <div class="pt-3 border-t border-secondary/20">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Donasi</p>
                        <p class="text-2xl font-extrabold text-secondary">
                            Rp {{ number_format($donation->amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Donor Info --}}
            <div class="bg-gray-50 rounded-lg p-4 mb-6 space-y-2">
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-user text-gray-400 w-4"></i>
                    <span class="text-gray-600">Donatur:</span>
                    <span class="font-semibold text-gray-900">{{ $donation->donor_name }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-phone text-gray-400 w-4"></i>
                    <span class="text-gray-600">No. HP:</span>
                    <span class="font-semibold text-gray-900">{{ $donation->donor_phone }}</span>
                </div>
                @if ($donation->donor_email)
                    <div class="flex items-center gap-2 text-sm">
                        <i class="fas fa-envelope text-gray-400 w-4"></i>
                        <span class="text-gray-600">Email:</span>
                        <span class="font-semibold text-gray-900">{{ $donation->donor_email }}</span>
                    </div>
                @endif
            </div>

            {{-- Payment Instructions --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Panduan Pembayaran:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Klik tombol "Bayar Sekarang" di bawah</li>
                            <li>Pilih metode pembayaran yang tersedia</li>
                            <li>Selesaikan pembayaran sesuai instruksi</li>
                            <li>Notifikasi akan dikirim via WhatsApp</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Pay Button --}}
            <button id="pay-button"
                class="w-full bg-secondary text-white py-3 px-4 rounded-lg font-semibold
                       hover:bg-goldDark hover:shadow-lg
                       focus:outline-none focus:ring-2 focus:ring-secondary/40 focus:ring-offset-1
                       transition-all duration-300 ease-in-out
                       flex items-center justify-center gap-2">
                <i class="fas fa-credit-card"></i>
                <span>Bayar Sekarang</span>
            </button>

            {{-- Back to Status Link --}}
            <div class="text-center mt-4">
                <a href="{{ route('donation.status', $donation->donation_code) }}"
                    class="text-sm text-gray-500 hover:text-secondary transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Kembali ke Status Donasi
                </a>
            </div>
        </div>
    </div>

    {{-- Midtrans Snap JS --}}
     @if (config('services.midtrans.is_production'))
        {{-- Script Production --}}
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}">
        </script>
    @else
        {{-- Script Sandbox --}}
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
    @endif

    {{-- Payment Handler Script --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const payButton = document.getElementById("pay-button");

            payButton.addEventListener("click", function() {
                // Disable button to prevent double click
                payButton.disabled = true;
                payButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memuat...';

                // Open Midtrans Snap payment popup
                snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        window.location.href =
                            "{{ route('donation.status', $donation->donation_code) }}";
                    },
                    onPending: function(result) {
                        window.location.href =
                            "{{ route('donation.status', $donation->donation_code) }}";
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal. Silakan coba lagi.');

                        // Re-enable button
                        payButton.disabled = false;
                        payButton.innerHTML =
                            '<i class="fas fa-credit-card"></i> <span>Bayar Sekarang</span>';
                    },
                    onClose: function() {
                        payButton.disabled = false;
                        payButton.innerHTML =
                            '<i class="fas fa-credit-card"></i> <span>Bayar Sekarang</span>';
                    }
                });
            });
        });
    </script>
@endsection
