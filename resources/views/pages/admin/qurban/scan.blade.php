@extends('components.layout.admin')

@section('title', 'Scan Kupon Qurban — ' . site_name())
@section('page-title', 'Scan Kupon Qurban')

@section('content')
    <div class="max-w-5xl mx-auto w-screen sm:w-full min-w-0 mt-4 space-y-6 overflow-auto">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.qurban.index') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i> Kembali ke daftar peserta
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 min-w-0">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 space-y-4 min-w-0 overflow-hidden">
                <h2 class="text-base font-semibold text-gray-900">Input kupon</h2>
                <p class="text-sm text-gray-600">Ketik kode atau pindai QR pada voucher peserta.</p>

                <form id="scan-coupon-form" class="space-y-3">
                    @csrf
                    <div>
                        <label for="coupon_code" class="block text-xs font-medium text-gray-600 mb-1">Kode kupon</label>
                        <input type="text" name="coupon_code" id="coupon_code" autocomplete="off"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono uppercase focus:ring-2 focus:ring-secondary/30 focus:border-secondary"
                            placeholder="Contoh: AB12CD34">
                    </div>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-secondary text-white px-4 py-2.5 rounded-lg text-sm font-semibold hover:opacity-95">
                        <i class="fas fa-qrcode"></i> Verifikasi kupon
                    </button>
                </form>

                <div class="border-t border-gray-100 pt-4 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 mb-2">
                        <button type="button" id="btn-start-qr"
                            class="inline-flex items-center justify-center gap-2 w-full sm:w-auto border border-gray-300 bg-white text-gray-800 px-3 py-2.5 rounded-lg text-sm hover:bg-gray-50">
                            <i class="fas fa-camera"></i> Aktifkan kamera
                        </button>
                        <button type="button" id="btn-stop-qr" disabled
                            class="inline-flex items-center justify-center gap-2 w-full sm:w-auto border border-gray-200 text-gray-400 px-3 py-2.5 rounded-lg text-sm cursor-not-allowed">
                            <i class="fas fa-stop"></i> Matikan kamera
                        </button>
                    </div>
                    <div id="qr-reader"
                        class="rounded-lg overflow-hidden bg-gray-50 w-full max-w-full sm:max-w-md mx-auto lg:mx-0 min-h-[220px] sm:min-h-[240px]"></div>
                    <p id="qr-status" class="hidden text-xs text-amber-700 mt-2"></p>
                </div>
            </div>

            <div id="detail-panel"
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 min-w-0 overflow-hidden hidden">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Detail pemilik kupon</h2>
                <div id="detail-alert"
                    class="hidden mb-4 p-3 rounded-lg text-sm border border-red-200 bg-red-50 text-red-800"></div>
                <div id="detail-success"
                    class="hidden mb-4 p-3 rounded-lg text-sm border border-green-200 bg-green-50 text-green-800"></div>
                <dl id="detail-dl" class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 sm:gap-x-6 gap-y-3 text-sm break-words">
                </dl>
                <div id="detail-items" class="mt-4 hidden">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Item qurban</h3>
                    <ul class="text-sm text-gray-800 space-y-1 list-disc list-inside" id="detail-items-list"></ul>
                </div>
                <a id="detail-admin-link" href="#"
                    class="mt-6 inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 hidden">
                    <i class="fas fa-external-link-alt"></i> Buka halaman lengkap di admin
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 min-w-0 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4 min-w-0">
                <h2 class="text-base font-semibold text-gray-900 min-w-0">Riwayat scan hari ini</h2>
                <span class="text-xs text-gray-500 shrink-0">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
            <div class="overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0 overscroll-x-contain touch-pan-x">
                <table class="w-full min-w-[42rem] text-sm">
                    <thead class="text-gray-600 bg-gray-50">
                        <tr>
                            <th class="px-2 sm:px-3 py-2 text-left whitespace-nowrap">Waktu</th>
                            <th class="px-2 sm:px-3 py-2 text-left whitespace-nowrap">Kode</th>
                            <th class="px-2 sm:px-3 py-2 text-left">Pemilik</th>
                            <th class="px-2 sm:px-3 py-2 text-left whitespace-nowrap">Kontak</th>
                            <th class="px-2 sm:px-3 py-2 text-left whitespace-nowrap">Total Paket</th>
                            <th class="px-2 sm:px-3 py-2 text-left whitespace-nowrap">Status</th>
                            <th class="px-2 sm:px-3 py-2 text-left">Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="history-body">
                        @if ($todayScans->isEmpty())
                            <tr id="history-placeholder">
                                <td colspan="7" class="px-3 py-4 text-sm text-gray-500">Belum ada kupon yang di-scan
                                    hari ini.</td>
                            </tr>
                        @else
                            @foreach ($todayScans as $scan)
                                <tr data-scan-row="{{ $scan->id }}">
                                    <td class="px-2 sm:px-3 py-2 whitespace-nowrap text-gray-700">
                                        {{ $scan->created_at->format('H:i:s') }}
                                    </td>
                                    <td class="px-2 sm:px-3 py-2 font-mono font-medium break-all max-w-[9rem]">
                                        {{ $scan->coupon_code }}
                                    </td>
                                    <td class="px-2 sm:px-3 py-2 break-words max-w-[10rem] sm:max-w-none">
                                        {{ $scan->participant?->full_name ?? '—' }}
                                    </td>
                                    <td class="px-2 sm:px-3 py-2 whitespace-nowrap">
                                        {{ $scan->participant?->contact_number ?? '—' }}
                                    </td>
                                    <td class="px-2 sm:px-3 py-2 whitespace-nowrap">
                                        {{ $scan->participant?->total_coupon ?? '—' }}
                                    </td>
                                    <td class="px-2 sm:px-3 py-2 whitespace-nowrap">
                                        @php($st = $scan->participant?->status)
                                        @if ($st)
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                                {{ $st === 'taken' ? 'bg-green-100 text-green-800' : ($st === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                                {{ ucfirst($st) }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-2 sm:px-3 py-2 text-gray-600 break-words max-w-[8rem] sm:max-w-none">
                                        {{ $scan->scanner?->name ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" crossorigin="anonymous"></script>
    <script>
        (function() {
            const form = document.getElementById('scan-coupon-form');
            const input = document.getElementById('coupon_code');
            const token = form.querySelector('input[name="_token"]').value;
            const submitUrl = @json(route('admin.qurban.scan_coupon.submit'));
            const scannerName = @json(Auth::user()->name);
            const panel = document.getElementById('detail-panel');
            const detailDl = document.getElementById('detail-dl');
            const detailAlert = document.getElementById('detail-alert');
            const detailSuccess = document.getElementById('detail-success');
            const detailItems = document.getElementById('detail-items');
            const detailItemsList = document.getElementById('detail-items-list');
            const detailAdminLink = document.getElementById('detail-admin-link');
            const historyBody = document.getElementById('history-body');
            const qrReaderEl = document.getElementById('qr-reader');
            const btnStartQr = document.getElementById('btn-start-qr');
            const btnStopQr = document.getElementById('btn-stop-qr');
            const qrStatus = document.getElementById('qr-status');

            let html5QrCode = null;
            let scanning = false;

            function showError(msg) {
                panel.classList.remove('hidden');
                detailAlert.textContent = msg;
                detailAlert.classList.remove('hidden');
                detailSuccess.classList.add('hidden');
                detailDl.innerHTML = '';
                detailItems.classList.add('hidden');
                detailAdminLink.classList.add('hidden');
            }

            function rowsLabel(key) {
                const m = {
                    full_name: 'Nama',
                    coupon_code: 'Kode kupon',
                    nik: 'NIK',
                    contact_number: 'Kontak',
                    email: 'Email',
                    address: 'Alamat / tempat',
                    city: 'Kota',
                    province: 'Provinsi',
                    status: 'Status',
                    total_coupon: 'Total kupon',
                    pickup_date: 'Tanggal ambil',
                    pickup_time: 'Waktu ambil',
                    note: 'Catatan',
                };
                return m[key] || key;
            }

            function renderDetail(p, msg) {
                panel.classList.remove('hidden');
                detailAlert.classList.add('hidden');
                detailSuccess.textContent = msg || 'Kupon tervalidasi.';
                detailSuccess.classList.remove('hidden');
                detailDl.innerHTML = '';

                const order = ['full_name', 'coupon_code', 'nik', 'contact_number', 'email', 'address', 'city',
                    'province', 'status', 'total_coupon', 'pickup_date', 'pickup_time', 'note'
                ];
                order.forEach(function(key) {
                    if (p[key] === null || p[key] === undefined || p[key] === '') return;
                    let val = p[key];
                    if (key === 'status') val = String(val).charAt(0).toUpperCase() + String(val).slice(1);
                    const dt = document.createElement('dt');
                    dt.className = 'text-gray-500';
                    dt.textContent = rowsLabel(key);
                    const dd = document.createElement('dd');
                    dd.className = 'text-gray-900 font-medium mt-0.5 sm:col-span-1';
                    dd.textContent = val;
                    detailDl.appendChild(dt);
                    detailDl.appendChild(dd);
                });

                if (p.items && p.items.length) {
                    detailItems.classList.remove('hidden');
                    detailItemsList.innerHTML = '';
                    p.items.forEach(function(it) {
                        const li = document.createElement('li');
                        li.textContent = (it.qurban_type || '—') + ' × ' + (it.total_coupon ?? '—');
                        detailItemsList.appendChild(li);
                    });
                } else {
                    detailItems.classList.add('hidden');
                }

                if (p.admin_url) {
                    detailAdminLink.href = p.admin_url;
                    detailAdminLink.classList.remove('hidden');
                } else {
                    detailAdminLink.classList.add('hidden');
                }
            }

            function escapeHtml(s) {
                const d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
            }

            function prependHistoryRow(p, scannedAt) {
                if (!historyBody) return;
                const ph = document.getElementById('history-placeholder');
                if (ph) ph.remove();
                const tr = document.createElement('tr');
                const t = scannedAt ? new Date(scannedAt) : new Date();
                const timeStr = t.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                });
                const st = p.status || '';
                const stClass = st === 'taken' ? 'bg-green-100 text-green-800' : (st === 'rejected' ?
                    'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800');
                const totalPaket = (p.total_coupon !== undefined && p.total_coupon !== null && p.total_coupon !== '') ?
                    String(p.total_coupon) : '—';
                tr.innerHTML =
                    '<td class="px-2 sm:px-3 py-2 whitespace-nowrap text-gray-700">' + timeStr + '</td>' +
                    '<td class="px-2 sm:px-3 py-2 font-mono font-medium break-all max-w-[9rem]">' + escapeHtml(p.coupon_code || '') + '</td>' +
                    '<td class="px-2 sm:px-3 py-2 break-words max-w-[10rem] sm:max-w-none">' + escapeHtml(p.full_name || '—') + '</td>' +
                    '<td class="px-2 sm:px-3 py-2 whitespace-nowrap">' + escapeHtml(p.contact_number || '—') + '</td>' +
                    '<td class="px-2 sm:px-3 py-2 whitespace-nowrap">' + escapeHtml(totalPaket) + '</td>' +
                    '<td class="px-2 sm:px-3 py-2 whitespace-nowrap"><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium ' +
                    stClass + '">' + (st ? st.charAt(0).toUpperCase() + st.slice(1) : '—') + '</span></td>' +
                    '<td class="px-2 sm:px-3 py-2 text-gray-600 break-words max-w-[8rem] sm:max-w-none">' + escapeHtml(scannerName) + '</td>';
                historyBody.insertBefore(tr, historyBody.firstChild);
            }

            async function submitCode(code) {
                const raw = (code || '').trim();
                if (!raw) return;
                detailAlert.classList.add('hidden');
                const fd = new FormData();
                fd.append('coupon_code', raw);
                fd.append('_token', token);

                try {
                    const res = await fetch(submitUrl, {
                        method: 'POST',
                        body: fd,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json().catch(function() {
                        return {};
                    });

                    if (!res.ok || !data.ok) {
                        showError(data.message || 'Kupon tidak valid atau terjadi kesalahan.');
                        return;
                    }

                    renderDetail(data.participant, data.message);
                    input.value = '';
                    prependHistoryRow(data.participant, data.scan && data.scan.scanned_at);
                } catch (e) {
                    showError('Gagal menghubungi server. Coba lagi.');
                }
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                submitCode(input.value);
            });

            btnStartQr.addEventListener('click', async function() {
                if (typeof Html5Qrcode === 'undefined') {
                    qrStatus.textContent = 'Library pemindai tidak termuat. Segarkan halaman.';
                    qrStatus.classList.remove('hidden');
                    return;
                }
                if (scanning) return;
                qrStatus.classList.add('hidden');
                html5QrCode = new Html5Qrcode('qr-reader');
                try {
                    await html5QrCode.start({
                        facingMode: 'environment'
                    }, {
                        fps: 10,
                        qrbox: function(viewfinderWidth, viewfinderHeight) {
                            var edge = Math.min(viewfinderWidth, viewfinderHeight);
                            var size = Math.max(140, Math.min(260, Math.floor(edge * 0.72)));
                            return {
                                width: size,
                                height: size
                            };
                        }
                    }, function(decodedText) {
                        submitCode(decodedText);
                        stopQr();
                    }, function() {});
                    scanning = true;
                    btnStartQr.disabled = true;
                    btnStopQr.disabled = false;
                    btnStopQr.classList.remove('text-gray-400', 'cursor-not-allowed');
                    btnStopQr.classList.add('text-gray-800', 'hover:bg-gray-50', 'border-gray-300',
                        'bg-white', 'cursor-pointer');
                } catch (err) {
                    qrStatus.textContent =
                        'Kamera tidak bisa dibuka (izin ditolak atau perangkat tidak mendukung). Gunakan input manual.';
                    qrStatus.classList.remove('hidden');
                    html5QrCode = null;
                }
            });

            async function stopQr() {
                if (!html5QrCode || !scanning) return;
                try {
                    await html5QrCode.stop();
                    await html5QrCode.clear();
                } catch (e) {}
                html5QrCode = null;
                scanning = false;
                btnStartQr.disabled = false;
                btnStopQr.disabled = true;
                btnStopQr.classList.add('text-gray-400', 'cursor-not-allowed');
                btnStopQr.classList.remove('text-gray-800', 'hover:bg-gray-50', 'cursor-pointer');
            }

            btnStopQr.addEventListener('click', stopQr);
        })();
    </script>
@endsection
