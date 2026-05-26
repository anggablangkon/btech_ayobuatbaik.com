@extends('components.layout.app')

@section('title', 'Kupon Qurban — ' . site_name())

@section('header-content')
    <header class="px-4 pt-4 pb-2 border-b border-gray-100 bg-white">
        <h1 class="text-lg font-semibold text-gray-900">Kupon pengambilan qurban</h1>
        <p class="text-xs text-gray-500 mt-1">{{ $participant->full_name ?? 'Peserta' }}</p>
    </header>
@endsection

@section('content')
    @php
        $voucherDownloadName = 'kupon-qurban-' . preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($participant->coupon_code ?? 'kupon')) . '.png';
    @endphp
    <div class="px-4 py-6 space-y-6 pb-24">
        <div id="qurban-voucher-card"
            class="rounded-2xl overflow-hidden border-4 border-[#0d2847] shadow-md bg-[#0d2847]">
            <table class="w-full border-collapse">
                <tr>
                    <td class="align-top w-[62%] bg-[#c7e3f5] p-3 md:p-4">
                        <p class="text-[11px] md:text-xs font-bold uppercase text-[#0d2847] leading-tight mb-3">
                            Kupon pengambilan daging qurban
                        </p>
                        <div class="bg-[#f7d54a] border border-[#d4b228] rounded-md p-3 text-[11px] md:text-xs text-gray-900 space-y-1.5">
                            <div><span class="inline-block text-[8pt] font-semibold min-w-[3.5rem]">Nama</span>
                                {{ ucwords(strtolower($participant->full_name)) }}</div>
                            <div><span class="inline-block text-[8pt] font-semibold min-w-[3.5rem]">Tempat</span>
                                {{ ucwords(strtolower($participant->address)) }}</div>
                            <div><span class="inline-block text-[8pt] font-semibold min-w-[3.5rem]">Tanggal</span>
                                {{$participant->pickup_date}}</div>
                            <div><span class="inline-block text-[8pt] font-semibold min-w-[3.5rem]">Waktu</span>
                                {{$participant->pickup_time}}</div>
                        </div>
                    </td>
                    <td
                        class="align-middle w-[38%] bg-[#0d2847] p-2 md:p-3 text-center border-l-[3px] border-dashed border-white">
                        <div class="bg-white rounded-xl px-2 py-3 mx-auto max-w-[140px]">
                            <img src="{{ $voucher['qr_src'] }}" alt="QR kode kupon" class="w-24 h-24 mx-auto object-contain mb-2"
                                width="96" height="96">
                            <p class="text-[10px] font-mono text-gray-700 break-all">{{ $participant->coupon_code }}</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="flex justify-center">
            <button type="button" id="btn-download-qurban-voucher"
                data-download-name="{{ $voucherDownloadName }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#0d2847] text-white text-sm font-medium px-4 py-2.5 shadow-sm active:opacity-90">
                <i class="fa-solid fa-download text-xs opacity-90" aria-hidden="true"></i>
                Unduh kupon
            </button>
        </div>
        <p class="text-center text-[11px] text-gray-400">{{ site_name() }}</p>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <script>
        (function() {
            const btn = document.getElementById('btn-download-qurban-voucher');
            const card = document.getElementById('qurban-voucher-card');
            if (!btn || !card || typeof html2canvas !== 'function') return;

            btn.addEventListener('click', function() {
                btn.disabled = true;
                const prev = btn.innerHTML;
                btn.innerHTML =
                    '<i class="fa-solid fa-spinner fa-spin text-xs" aria-hidden="true"></i> Menyiapkan…';

                html2canvas(card, {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                }).then(function(canvas) {
                    const name = btn.getAttribute('data-download-name') || 'kupon-qurban.png';
                    const link = document.createElement('a');
                    link.download = name;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                }).catch(function() {
                    alert('Gagal membuat gambar. Coba lagi atau gunakan tangkapan layar.');
                }).finally(function() {
                    btn.disabled = false;
                    btn.innerHTML = prev;
                });
            });
        })();
    </script>
@endsection
