@extends('components.layout.admin')

@section('page-title', 'Broadcast WhatsApp')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Broadcast</h1>
        <div class="flex gap-2">
            <form action="{{ route('admin.broadcast.process_queue') }}" method="POST">
                @csrf
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2" title="Jalankan antrian manual jika status pending lama">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                    </svg>
                    Proses Antrian
                </button>
            </form>
            
            <a href="{{ route('admin.broadcast.create') }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Buat Broadcast Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Desktop Table (Hidden on Mobile) --}}
    <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Subjek</th>
                        <th class="px-6 py-4">Target</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Progress</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($broadcasts as $broadcast)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">
                                {{ $broadcast->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $broadcast->subject }}
                                @if($broadcast->image_path)
                                    <span class="ml-2 text-xs bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded">Image</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($broadcast->target == 'donors')
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">Donatur</span>
                                @elseif($broadcast->target == 'test')
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-700">Test ({{ $broadcast->target_data['test_number'] ?? '-' }})</span>
                                @else
                                    {{ ucfirst($broadcast->target) }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'processing' => 'bg-blue-100 text-blue-700',
                                        'completed' => 'bg-green-100 text-green-700',
                                        'failed' => 'bg-red-100 text-red-700',
                                    ];
                                    $color = $statusColors[$broadcast->status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                    {{ ucfirst($broadcast->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="text-xs font-medium">{{ $broadcast->processed_count }} / {{ $broadcast->total_count }}</span>
                                </div>
                                @if($broadcast->total_count > 0)
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                        <div class="bg-primary h-1.5 rounded-full" style="width: {{ ($broadcast->processed_count / $broadcast->total_count) * 100 }}%"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.broadcast.create', ['duplicate_id' => $broadcast->id]) }}" 
                                   class="text-gray-500 hover:text-primary transition-colors" title="Edit & Resend">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mx-auto">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 italic">
                                Belum ada riwayat broadcast.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Card View (Visible on Mobile) --}}
    <div class="md:hidden space-y-4">
        @forelse($broadcasts as $broadcast)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 relative">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">{{ $broadcast->subject }}</h3>
                        <p class="text-xs text-gray-500">{{ $broadcast->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'processing' => 'bg-blue-100 text-blue-700',
                            'completed' => 'bg-green-100 text-green-700',
                            'failed' => 'bg-red-100 text-red-700',
                        ];
                        $color = $statusColors[$broadcast->status] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $color }}">
                        {{ ucfirst($broadcast->status) }}
                    </span>
                </div>

                <div class="flex items-center gap-2 mb-3">
                    @if($broadcast->target == 'donors')
                        <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">Donatur</span>
                    @elseif($broadcast->target == 'test')
                        <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-700">Test</span>
                    @else
                        <span class="text-xs text-gray-600">{{ ucfirst($broadcast->target) }}</span>
                    @endif

                    @if($broadcast->image_path)
                        <span class="text-xs bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded flex items-center gap-1">
                            <i class="fas fa-image"></i> Image
                        </span>
                    @endif
                </div>

                {{-- Progress Bar --}}
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Progress</span>
                        <span>{{ $broadcast->processed_count }} / {{ $broadcast->total_count }}</span>
                    </div>
                    @if($broadcast->total_count > 0)
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-primary h-1.5 rounded-full transition-all duration-500" style="width: {{ ($broadcast->processed_count / $broadcast->total_count) * 100 }}%"></div>
                        </div>
                    @endif
                </div>

                {{-- Action --}}
                <div class="border-t border-gray-100 pt-3 flex justify-end">
                    <a href="{{ route('admin.broadcast.create', ['duplicate_id' => $broadcast->id]) }}" 
                       class="text-sm font-medium text-primary hover:text-primary-dark flex items-center gap-2 px-3 py-1.5 hover:bg-primary-50 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Resend / Edit
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-400 italic bg-white rounded-xl shadow-sm border border-gray-100">
                Belum ada riwayat broadcast.
            </div>
        @endforelse
    </div>
    
    {{-- Pagination (Shared) --}}
    <div class="mt-4">
        {{ $broadcasts->links() }}
    </div>
@endsection
