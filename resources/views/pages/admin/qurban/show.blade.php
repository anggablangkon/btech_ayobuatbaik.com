@extends('components.layout.admin')

@section('title', 'Detail Peserta Qurban — ' . site_name())
@section('page-title', 'Detail Peserta Qurban')

@section('content')
    <div class="max-w-4xl mx-auto mt-8 space-y-6">
        @if (session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $qurbanParticipant->full_name ?? '—' }}</h2>
                    @if ($qurbanParticipant->nik)
                        <p class="text-sm text-gray-600 mt-1">NIK: {{ $qurbanParticipant->nik }}</p>
                    @endif
                </div>
                @php
                    $st = $qurbanParticipant->status ?? 'pending';
                    $cls =
                        $st === 'taken'
                            ? 'bg-green-100 text-green-800'
                            : ($st === 'rejected'
                                ? 'bg-red-100 text-red-800'
                                : 'bg-amber-100 text-amber-800');
                @endphp
                <span class="inline-flex self-start px-3 py-1 rounded-lg text-xs font-semibold {{ $cls }}">
                    {{ ucfirst($st) }}
                </span>
            </div>

            <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div>
                    <dt class="text-gray-500">Kode kupon</dt>
                    <dd class="font-mono font-semibold text-gray-900 mt-0.5">{{ $qurbanParticipant->coupon_code ?? '—' }}
                    </dd>
                </div>
                @if ($qurbanParticipant->coupon_code)
                    @php
                        $voucherPublicUrl = route('qurban.voucher.public', [
                            'coupon_code' => $qurbanParticipant->coupon_code,
                        ], true);
                    @endphp
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Link voucher (publik)</dt>
                        <dd class="mt-2 space-y-2">
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text" readonly id="qurban-voucher-url" value="{{ $voucherPublicUrl }}"
                                    class="flex-1 text-xs font-mono border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 text-gray-800">
                                <button type="button" onclick="copyQurbanVoucherUrl()"
                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-secondary text-white text-sm shrink-0">
                                    <i class="fas fa-copy"></i> Salin
                                </button>
                            </div>
                            <a href="{{ $voucherPublicUrl }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-external-link-alt"></i> Buka voucher di tab baru
                            </a>
                        </dd>
                    </div>
                @endif
                <div>
                    <dt class="text-gray-500">Total Paket</dt>
                    <dd class="font-mono font-semibold text-gray-900 mt-0.5">{{ $qurbanParticipant->total_coupon ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Kontak</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $qurbanParticipant->contact_number ?? '—' }}</dd>
                </div>
                {{-- <div class="sm:col-span-2">
                    <dt class="text-gray-500">Email</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $qurbanParticipant->email ?: '—' }}</dd>
                </div> --}}
                <div class="">
                    <dt class="text-gray-500">Daerah</dt>
                    <dd class="text-gray-900 mt-0.5 whitespace-pre-line">{{ $qurbanParticipant->address ?: '—' }}</dd>
                </div>
                {{-- <div>
                    <dt class="text-gray-500">Kota</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $qurbanParticipant->city ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Provinsi</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $qurbanParticipant->province ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Kode pos</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $qurbanParticipant->postal_code ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Negara</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $qurbanParticipant->country ?: '—' }}</dd>
                </div> --}}
                @if ($qurbanParticipant->note)
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Catatan</dt>
                        <dd class="text-gray-900 mt-0.5 whitespace-pre-line">{{ $qurbanParticipant->note }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-gray-500">Tanggal Pengambilan</dt>
                    <dd class="text-gray-900 mt-0.5">{{ $qurbanParticipant->pickup_date }} {{ $qurbanParticipant->pickup_time }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Terdaftar</dt>
                    <dd class="text-gray-900 mt-0.5">{{ optional($qurbanParticipant->created_at)->format('d M Y H:i') }}
                    </dd>
                </div>
            </dl>

            {{-- <div class="mt-8 pt-6 border-t border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Item qurban</h3>
                @if ($qurbanParticipant->relationLoaded('items') ? $qurbanParticipant->items->isNotEmpty() : $qurbanParticipant->items()->exists())
                    @php $items = $qurbanParticipant->relationLoaded('items') ? $qurbanParticipant->items : $qurbanParticipant->items()->get(); @endphp
                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-4 py-2 text-left">Jenis</th>
                                    <th class="px-4 py-2 text-right">Total kupon</th>
                                    <th class="px-4 py-2 text-right">Harga</th>
                                    <th class="px-4 py-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($items as $item)
                                    <tr>
                                        <td class="px-4 py-2 capitalize">{{ $item->qurban_type }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($item->total_coupon ?? 0) }}</td>
                                        <td class="px-4 py-2 text-right">
                                            Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-right font-medium">
                                            Rp {{ number_format($item->total_price ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Belum ada item.</p>
                @endif
            </div> --}}

            <div class="flex flex-wrap gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.qurban.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                @if ($qurbanParticipant->coupon_code)
                    <a href="{{ route('qurban.voucher.public', ['coupon_code' => $qurbanParticipant->coupon_code]) }}"
                        target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm hover:bg-emerald-100">
                        <i class="fas fa-link"></i> Buka voucher publik
                    </a>
                @endif
                <a href="{{ route('admin.qurban.edit', $qurbanParticipant) }}"
                    class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form id="del-q-show" action="{{ route('admin.qurban.destroy', $qurbanParticipant) }}" method="POST"
                    class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                        onclick="confirmDelete('del-q-show', 'Hapus peserta ini?')"
                        class="inline-flex items-center gap-2 border border-red-200 text-red-600 px-4 py-2 rounded-lg text-sm hover:bg-red-50">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @if ($qurbanParticipant->coupon_code)
        <script>
            function copyQurbanVoucherUrl() {
                var el = document.getElementById('qurban-voucher-url');
                if (!el) return;
                el.select();
                el.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(el.value).catch(function() {
                    try {
                        document.execCommand('copy');
                    } catch (e) {}
                });
            }
        </script>
    @endif
@endsection
