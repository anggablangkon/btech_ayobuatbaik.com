@extends('components.layout.admin')

@section('title', 'Edit Peserta Qurban — ' . site_name())
@section('page-title', 'Edit Peserta Qurban')

@section('content')
    @php
        $qurbanTypes = \App\Models\QurbanParticipantItem::QURBAN_TYPES;
    @endphp

    <div class="max-w-4xl mx-auto mt-8">
        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Ubah data peserta</h2>
            <p class="text-xs text-gray-500 mb-6 font-mono">Kode kupon: {{ $qurbanParticipant->coupon_code ?? '—' }}</p>

            @if ($errors->any())
                <div class="p-3 mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <strong>Periksa kembali input:</strong>
                    <ul class="list-disc mt-2 ml-5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.qurban.update', $qurbanParticipant) }}" method="POST" class="space-y-6"
                id="qurbanFormEdit">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama lengkap <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $qurbanParticipant->full_name) }}"
                            required
                            class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0 px-2 py-2">
                    </div>

                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik', $qurbanParticipant->nik) }}"
                            class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0 px-0 py-2">
                    </div> --}}

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor kontak <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="contact_number"
                            value="{{ old('contact_number', $qurbanParticipant->contact_number) }}" required
                            class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0 px-2 py-2">
                    </div>

                    {{-- <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $qurbanParticipant->email) }}"
                            class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0 px-0 py-2">
                    </div> --}}

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Daerah <span
                                class="text-red-500">*</span></label>
                        <textarea name="address" rows="2" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary">{{ old('address', $qurbanParticipant->address) }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Paket <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="total_coupon"
                            value="{{ old('total_coupon', $qurbanParticipant->total_coupon) }}" required
                            class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0 px-2 py-2">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengambilan <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="pickup_date" value="{{ old('pickup_date', $qurbanParticipant->pickup_date) }}" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Pengambilan (WIB) <span
                                class="text-red-500">*</span></label>
                        <input type="time" name="pickup_time" value="{{ old('pickup_time', $qurbanParticipant->pickup_time) }}" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary">
                    </div>

                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <input type="text" name="city" value="{{ old('city', $qurbanParticipant->city) }}"
                            class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0 px-0 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                        <input type="text" name="province" value="{{ old('province', $qurbanParticipant->province) }}"
                            class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0 px-0 py-2">
                    </div> --}}

                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode pos</label>
                        <input type="text" name="postal_code"
                            value="{{ old('postal_code', $qurbanParticipant->postal_code) }}"
                            class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0 px-0 py-2">
                    </div> --}}
                    {{--
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Negara</label>
                        <input type="text" name="country"
                            value="{{ old('country', $qurbanParticipant->country ?? 'Indonesia') }}"
                            class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0 px-0 py-2">
                    </div> --}}
                    {{--
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="note" rows="2"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-primary">{{ old('note', $qurbanParticipant->note) }}</textarea>
                    </div> --}}

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                            class="w-full border-0 border-b border-gray-300 focus:border-primary focus:ring-0 px-0 py-2">
                            @foreach (['pending' => 'Pending', 'taken' => 'Diambil', 'rejected' => 'Ditolak'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('status', $qurbanParticipant->status) === $val ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- <div class="border-t border-gray-100 pt-6">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <h3 class="text-sm font-semibold text-gray-800">Item qurban</h3>
                        <button type="button" id="addQurbanRowEdit"
                            class="text-sm inline-flex items-center gap-1 text-primary hover:underline">
                            <i class="fas fa-plus-circle"></i> Tambah baris
                        </button>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium">Jenis</th>
                                    <th class="px-4 py-3 text-left font-medium w-40">Total kupon</th>
                                    <th class="px-4 py-3 text-center font-medium w-14" aria-label="Hapus baris"></th>
                                </tr>
                            </thead>
                            <tbody id="qurbanRowsEdit" class="divide-y divide-gray-100">
                                @php
                                    $items = $qurbanParticipant->items ?? collect();
                                    $oldItems = old('qurban_items');
                                    $oldTotals = old('total_coupon');
                                    if (is_array($oldItems) && count($oldItems)) {
                                        $rowCount = max(count($oldItems), is_array($oldTotals) ? count($oldTotals) : 0, 1);
                                    } else {
                                        $rowCount = max($items->count(), 1);
                                    }
                                @endphp
                                @for ($i = 0; $i < $rowCount; $i++)
                                    @php
                                        $typeVal = is_array($oldItems)
                                            ? $oldItems[$i] ?? ''
                                            : optional($items->get($i))->qurban_type;
                                        $couponVal = is_array($oldTotals)
                                            ? $oldTotals[$i] ?? 1
                                            : optional($items->get($i))->total_coupon ?? 1;
                                    @endphp
                                    <tr class="qurban-row bg-white hover:bg-gray-50/80">
                                        <td class="px-4 py-3 align-middle">
                                            <select name="qurban_items[]" required
                                                class="w-full min-w-[10rem] border rounded px-3 py-2 text-sm focus:ring-1 focus:ring-primary">
                                                <option value="">— Pilih —</option>
                                                @foreach ($qurbanTypes as $type)
                                                    <option value="{{ $type }}" {{ $typeVal === $type ? 'selected' : '' }}>
                                                        {{ ucfirst($type) }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 align-middle w-40">
                                            <input type="number" name="total_coupon[]" min="1" value="{{ $couponVal }}"
                                                required
                                                class="w-full border rounded px-3 py-2 text-sm focus:ring-1 focus:ring-primary">
                                        </td>
                                        <td class="px-2 py-3 align-middle text-center">
                                            <button type="button"
                                                class="remove-row inline-flex items-center justify-center text-red-600 hover:text-red-800 p-2 rounded hover:bg-red-50 {{ $rowCount <= 1 ? 'opacity-40 cursor-not-allowed' : '' }}"
                                                title="Hapus baris"
                                                {{ $rowCount <= 1 ? 'disabled' : '' }}>
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div> --}}

                <div class="flex flex-wrap justify-between gap-3 pt-4">
                    <a href="{{ route('admin.qurban.show', $qurbanParticipant) }}"
                        class="text-sm text-gray-600 hover:text-blue-600 inline-flex items-center gap-2">
                        <i class="fas fa-eye"></i> Lihat detail
                    </a>
                    <div class="flex flex-wrapgap-3">
                        <a href="{{ route('admin.qurban.index') }}"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</a>
                        <button type="submit"
                            class="px-5 py-2 bg-primary text-white rounded-lg hover:opacity-95 transition">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <template id="qurbanRowTemplateEdit">
        <tr class="qurban-row bg-white hover:bg-gray-50/80">
            <td class="px-4 py-3 align-middle">
                <select name="qurban_items[]" required
                    class="w-full min-w-[10rem] border rounded px-3 py-2 text-sm focus:ring-1 focus:ring-primary">
                    <option value="">— Pilih —</option>
                    @foreach ($qurbanTypes as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-4 py-3 align-middle w-40">
                <input type="number" name="total_coupon[]" min="1" value="1" required
                    class="w-full border rounded px-3 py-2 text-sm focus:ring-1 focus:ring-primary">
            </td>
            <td class="px-2 py-3 align-middle text-center">
                <button type="button"
                    class="remove-row inline-flex items-center justify-center text-red-600 hover:text-red-800 p-2 rounded hover:bg-red-50"
                    title="Hapus baris">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('qurbanRowsEdit');
            const tpl = document.getElementById('qurbanRowTemplateEdit');
            document.getElementById('addQurbanRowEdit')?.addEventListener('click', function() {
                container.appendChild(tpl.content.cloneNode(true));
                refreshRemoveButtons();
            });
            container?.addEventListener('click', function(e) {
                const btn = e.target.closest('.remove-row');
                if (!btn || btn.disabled) return;
                btn.closest('.qurban-row')?.remove();
                refreshRemoveButtons();
            });

            function refreshRemoveButtons() {
                const rows = container.querySelectorAll('.qurban-row');
                rows.forEach((row) => {
                    const rm = row.querySelector('.remove-row');
                    if (!rm) return;
                    const disable = rows.length <= 1;
                    rm.disabled = disable;
                    rm.classList.toggle('opacity-40', disable);
                    rm.classList.toggle('cursor-not-allowed', disable);
                });
            }
        });
    </script>
@endsection
