<x-app-layout>
    <x-slot name="title">Pengguna</x-slot>
    <div class="mb-7"><h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengguna</h1><p class="mt-1 text-sm text-slate-500">Kelola hak akses admin dan user.</p></div>
    <div class="card overflow-x-auto">
        <table class="w-full text-left">
            <thead><tr class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><th class="px-5 py-3">Pengguna</th><th class="px-5 py-3">Laporan</th><th class="px-5 py-3">Bergabung</th><th class="px-5 py-3">Role</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($users as $user)
                    <tr>
                        <td class="px-5 py-4"><p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p><p class="text-xs text-slate-500">{{ $user->email }}</p></td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $user->work_reports_count }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2">@csrf @method('PATCH')
                                <select name="role" class="rounded-lg border-slate-200 py-2 text-sm" {{ $user->is(auth()->user()) ? 'disabled' : '' }}><option value="user" @selected($user->role === 'user')>User</option><option value="admin" @selected($user->role === 'admin')>Admin</option></select>
                                @if(!$user->is(auth()->user()))<button class="rounded-lg px-3 py-2 text-xs font-semibold text-teal-700 hover:bg-teal-50">Simpan</button>@else<span class="text-xs text-slate-400">Anda</span>@endif
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
