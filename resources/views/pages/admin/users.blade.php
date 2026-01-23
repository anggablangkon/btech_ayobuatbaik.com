
@extends('components.layout.admin')

@section('title', 'Manajemen Pengguna - Admin ' . site_name())

@section('page-title', 'Daftar Pengguna')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Semua Pengguna</h3>
            <span class="bg-secondary/10 text-secondary text-xs px-3 py-1 rounded-full font-medium">
                Total: {{ $users->total() }} User
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">No</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">Nama</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b">Email</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b text-center">Peran</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b text-center">Tgl Bergabung</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $index => $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-800 text-sm">{{ $user->name }}</span>
                                    @if($user->id === auth()->id())
                                        <span class="text-[10px] text-primary-light font-medium">(Saya)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($user->is_admin)
                                    <span class="bg-primary/10 text-primary text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-wider">Admin</span>
                                @else
                                    <span class="bg-gray-100 text-gray-500 text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-wider">User</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 text-center">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah peran pengguna ini?')">
                                        @csrf
                                        <button type="submit" class="text-xs {{ $user->is_admin ? 'text-red-600 hover:text-red-800' : 'text-primary hover:text-primary-dark' }} font-bold transition-colors">
                                            {{ $user->is_admin ? 'Jadikan User' : 'Jadikan Admin' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 italic">No Action</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">
                                Belum ada pengguna terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
