@extends('components.layout.admin')

@section('title', 'Qurban — Peserta — ' . site_name())
@section('page-title', 'Qurban — Peserta')

@section('content')
    @php
        $exportQuery = [
            'start_date' => request('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')),
            'end_date' => request('end_date', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')),
        ];
        if (request()->filled('status')) {
            $exportQuery['status'] = request('status');
        }
    @endphp
    <div class="mx-auto mt-8 max-w-7xl">
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900">Daftar Peserta Qurban</h3>
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.qurban.create') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm text-white transition hover:bg-blue-700">
                        <i class="fas fa-plus"></i> Tambah
                    </a>
                    <a href="{{ route('admin.qurban.scan_coupon') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-secondary px-3 py-2 text-sm text-white transition hover:opacity-95">
                        <i class="fas fa-qrcode"></i>Scan kupon
                    </a>
                    <a href="{{ route('admin.qurban.export', $exportQuery) }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-3 py-2 text-sm text-white shadow-sm transition hover:bg-emerald-800"
                        title="Unduh Excel sesuai tanggal & status filter">
                        <i class="fas fa-file-excel"></i> Ekspor Excel
                    </a>
                </div>
            </div>
            <h3 class="mt-3 text-base font-semibold text-gray-900">Filter</h3>
            <form id="qurbanFilters" method="GET"
                class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="sm:flex flex-col flex-wrap items-end gap-2 text-sm sm:flex-row space-y-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs text-gray-600">Status</label>
                        <select name="status" class="min-w-[8rem] rounded border px-2 py-2 text-sm">
                            <option value="">Semua Status</option>
                            @foreach (['pending' => 'Pending', 'taken' => 'Diambil', 'rejected' => 'Ditolak'] as $val => $label)
                                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs text-gray-600">Dari</label>
                        <input type="date" name="start_date"
                            value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}"
                            class="rounded border px-2 py-2 text-sm">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs text-gray-600">Sampai</label>
                        <input type="date" name="end_date"
                            value="{{ request('end_date', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')) }}"
                            class="rounded border px-2 py-2 text-sm">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs text-gray-600">Per halaman</label>
                        <select name="perPage" id="perPage" class="rounded border px-2 py-2 text-sm">
                            @foreach ([10, 30, 50] as $n)
                                <option value="{{ $n }}"
                                    {{ (int) request('perPage', 10) === $n ? 'selected' : '' }}>
                                    {{ $n }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="sm:flex flex-col flex-wrap items-end gap-2 text-sm sm:flex-row space-y-3">
                    <div class="flex min-w-[16rem] flex-col">
                        <label for="search">Cari nama</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="rounded-lg border px-2 py-2 text-sm">
                    </div>
                    <div class="flex flex-col">
                        <button type="submit"
                            class="d-block mb-auto self-end rounded-lg bg-secondary px-4 py-2 text-sm text-white transition hover:opacity-95 w-full">
                            Terapkan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Desktop -->
        <div class="mt-4 hidden rounded-xl border border-gray-100 bg-white p-4 shadow-sm md:block">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Kontak</th>
                            <th class="px-4 py-3 text-left">Kupon</th>
                            <th class="px-4 py-3 text-left">Item Qurban</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($qurbanParticipants as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $p->full_name ?? '—' }}
                                    @if ($p->nik)
                                        <div class="text-xs font-normal text-gray-500">NIK: {{ $p->nik }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-gray-800">{{ $p->contact_number ?? '—' }}</div>
                                    @if ($p->email)
                                        <div class="text-xs text-gray-500">{{ $p->email }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $p->coupon_code ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">
                                    {{ $p->total_coupon ?? '—' }} kupon
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $cls = App\Models\QurbanParticipant::getStatusClass($p->status);
                                    @endphp
                                    <span class="{{ $cls }} inline-flex rounded-md px-2 py-1 text-xs font-medium">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <a href="{{ route('admin.qurban.show', $p) }}"
                                        class="mr-3 text-gray-700 hover:text-blue-600" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.qurban.edit', $p) }}"
                                        class="mr-3 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if ($p->coupon_code)
                                        <a href="{{ route('qurban.voucher.public', ['coupon_code' => $p->coupon_code]) }}"
                                            target="_blank" rel="noopener"
                                            class="mr-3 text-emerald-600 hover:text-emerald-800" title="Voucher publik">
                                            <i class="fas fa-link"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.qurban.send_whatsapp', $p) }}" method="POST"
                                        class="inline" onclick="return confirm('Kirim kupon kepada peserta ini?')">
                                        @csrf
                                        <button type="submit" class="mr-3 text-green-600 hover:text-green-800"><i
                                                class="fa-brands fa-whatsapp"></i></button>
                                    </form>

                                    @if ($p->status !== 'taken')
                                        <form id="del-q-{{ $p->id }}"
                                            action="{{ route('admin.qurban.destroy', $p) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('del-q-{{ $p->id }}', 'Hapus peserta ini?')"
                                                class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">Belum ada peserta qurban.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between">
                <div class="mt-4 text-xs font-medium text-gray-950">{{ $qurbanParticipants->firstItem() }} -
                    {{ $qurbanParticipants->lastItem() }} dari {{ $qurbanParticipants->total() }} peserta</div>
                <div class="mt-4">{{ $qurbanParticipants->withQueryString()->links() }}</div>
            </div>

        </div>

        <!-- Mobile -->
        <div class="mt-4 space-y-3 md:hidden">
            @forelse ($qurbanParticipants as $p)
                <div class="rounded-lg border border-gray-100 bg-white p-4 shadow">
                    <div class="flex justify-between gap-2">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $p->full_name ?? '—' }}</div>
                            <div class="mt-1 text-xs text-gray-600">{{ $p->contact_number ?? '—' }}</div>
                            <div class="mt-1 font-mono text-xs text-gray-500">{{ $p->coupon_code ?? '—' }}</div>
                        </div>
                        @php
                            $st = $p->status ?? 'pending';
                            $cls =
                                $st === 'taken'
                                    ? 'bg-green-100 text-green-800'
                                    : ($st === 'rejected'
                                        ? 'bg-red-100 text-red-800'
                                        : 'bg-amber-100 text-amber-800');
                        @endphp
                        <span class="{{ $cls }} self-start rounded px-2 py-1 text-xs font-medium">
                            {{ ucfirst($st) }}
                        </span>
                    </div>
                    @if ($p->relationLoaded('items') && $p->items->isNotEmpty())
                        <ul class="mt-3 list-inside list-disc text-xs text-gray-700">
                            @foreach ($p->items as $item)
                                <li class="capitalize">{{ $item->qurban_type }} — {{ $item->total_coupon }} kupon</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="mt-3 flex justify-end gap-4 text-sm">
                        <a href="{{ route('admin.qurban.show', $p) }}" class="text-gray-700"><i
                                class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.qurban.edit', $p) }}" class="text-blue-600"><i
                                class="fas fa-edit"></i></a>
                        @if ($p->coupon_code)
                            <a href="{{ route('qurban.voucher.public', ['coupon_code' => $p->coupon_code]) }}"
                                target="_blank" rel="noopener" class="text-emerald-600" title="Voucher publik"><i
                                    class="fas fa-link"></i></a>
                        @endif
                        <form action="{{ route('admin.qurban.send_whatsapp', $p) }}" method="POST" class="inline"
                            onclick="return confirm('Kirim kupon kepada peserta ini?')">
                            @csrf
                            <button type="submit" class="text-success-600 hover:text-success-800"><i
                                    class="fas fa-whatsapp"></i></button>
                        </form>

                        <form id="del-q-m-{{ $p->id }}" action="{{ route('admin.qurban.destroy', $p) }}"
                            method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                onclick="confirmDelete('del-q-m-{{ $p->id }}', 'Hapus peserta ini?')"
                                class="text-red-600"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-gray-100 bg-white py-8 text-center text-gray-500">Belum ada data.
                </div>
            @endforelse
            <div class="d-flex justify-content-between">
                <div class="mt-4 text-xs font-medium text-gray-950">{{ $qurbanParticipants->firstItem() }} -
                    {{ $qurbanParticipants->lastItem() }} dari {{ $qurbanParticipants->total() }} peserta</div>
                <div class="mt-4">{{ $qurbanParticipants->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>
@endsection
