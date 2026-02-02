@extends('components.layout.admin')

@section('title', 'Edit Donasi Manual - ' . site_name())
@section('page-title', 'Edit Donasi Manual')

@section('content')
<div class="max-w-4xl mx-auto rounded-xl bg-white shadow-sm border border-gray-100 p-6 md:p-8">
    <div class="mb-6 flex justify-between items-center border-b pb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit Data Donasi</h2>
            <p class="text-sm text-gray-500">Kode: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $donation->donation_code }}</span></p>
        </div>
        <a href="{{ route('admin.donasi.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.donasi.updateManual', $donation->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Left Column: Donor Info --}}
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wider border-l-4 border-secondary pl-3">Data Donatur</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Donatur <span class="text-red-500">*</span></label>
                    <input type="text" name="donor_name" value="{{ old('donor_name', $donation->donor_name) }}" required
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP <span class="text-red-500">*</span></label>
                    <input type="text" name="donor_phone" value="{{ old('donor_phone', $donation->donor_phone) }}" required
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-gray-400 text-xs">(Opsional)</span></label>
                    <input type="email" name="donor_email" value="{{ old('donor_email', $donation->donor_email) }}"
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
                </div>
            </div>

            {{-- Right Column: Mutation Info --}}
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wider border-l-4 border-green-500 pl-3">Info Transaksi</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Program Donasi <span class="text-red-500">*</span></label>
                    <select name="program_donasi_id" required class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ old('program_donasi_id', $donation->program_donasi_id) == $program->id ? 'selected' : '' }}>
                                {{ Str::limit($program->title, 40) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Donasi (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $donation->amount) }}" required min="1000"
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50 font-mono font-bold text-gray-800">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
                            <option value="pending" {{ $donation->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="success" {{ $donation->status == 'success' ? 'selected' : '' }}>Success</option>
                            <option value="failed" {{ $donation->status == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="expire" {{ $donation->status == 'expire' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Donasi</label>
                         <input type="datetime-local" name="created_at" 
                             value="{{ old('created_at', $donation->created_at ? $donation->created_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                             class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Admin <span class="text-gray-400 text-xs">(Opsional)</span></label>
                    <textarea name="note" rows="2" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">{{ old('note', $donation->note) }}</textarea>
                </div>
            </div>
        </div>

        {{-- UTM Tracking Info (Read-Only) --}}
        @if($donation->utm_source || $donation->utm_medium || $donation->utm_campaign)
        <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-lg">
            <h3 class="font-semibold text-blue-800 text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fas fa-chart-line"></i> Sumber Traffic (UTM)
            </h3>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-blue-600 font-medium block text-xs">Source</span>
                    <span class="text-gray-800 font-semibold">{{ $donation->utm_source ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-blue-600 font-medium block text-xs">Medium</span>
                    <span class="text-gray-800 font-semibold">{{ $donation->utm_medium ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-blue-600 font-medium block text-xs">Campaign</span>
                    <span class="text-gray-800 font-semibold">{{ $donation->utm_campaign ?? '-' }}</span>
                </div>
            </div>
        </div>
        @endif

        <div class="mt-8 flex justify-end gap-3 pt-6 border-t">
            <a href="{{ route('admin.donasi.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 shadow-lg hover:shadow-xl transition transform active:scale-95">
                <i class="fas fa-save mr-2"></i> Update Donasi
            </button>
        </div>
    </form>
</div>
@endsection
