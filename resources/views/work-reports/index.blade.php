<x-app-layout>
    <x-slot name="title">Data Laporan</x-slot>
    <div x-data="{ deleteUrl: '', deleteDate: '', deleting: false }">
    <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Data Laporan Kerja</h1>
            <p class="mt-1 text-sm text-slate-500">{{ auth()->user()->isAdmin() ? 'Kelola seluruh laporan kerja pengguna.' : 'Kelola riwayat laporan kerja Anda.' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('work-reports.export', request()->only('month', 'year')) }}" class="btn-secondary">Ekspor PDF</a>
            <a href="{{ route('work-reports.create') }}" class="btn-primary">+ Buat Laporan</a>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="grid gap-3 border-b border-slate-100 p-4 md:grid-cols-[1fr_auto]">
            <div class="relative">
                <svg class="absolute left-3 top-3 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                <input id="report-search" type="search" class="form-input pl-9" placeholder="Cari uraian, tanggal, atau pengguna...">
            </div>
            <form method="GET" class="flex flex-wrap gap-2" x-data="{ loading: false }" @submit="loading = true">
                <select name="month" class="form-input w-auto" aria-label="Bulan" @change="loading = true; $el.form.submit()">
                    <option value="">Semua bulan</option>
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" @selected(request('month') == $month)>{{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
                <select name="year" class="form-input w-auto" aria-label="Tahun" @change="loading = true; $el.form.submit()">
                    <option value="">Semua tahun</option>
                    @foreach(range(now()->year, now()->year - 5) as $year)<option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>@endforeach
                </select>
                <span x-cloak x-show="loading" class="inline-flex items-center gap-2 px-2 text-xs font-medium text-teal-700">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"/></svg>
                    Memuat
                </span>
                @if(request()->hasAny(['month','year']))<a href="{{ route('work-reports.index') }}" class="btn-secondary">Reset</a>@endif
            </form>
        </div>
        <div class="overflow-x-auto">
            <table id="reports-table" class="w-full">
                <thead><tr>
                    <th>Tanggal</th>
                    @if(auth()->user()->isAdmin())<th>Pengguna</th>@endif
                    <th>Jam</th><th>Aktivitas</th><th>Lokasi</th><th>File</th><th class="w-32 text-right">Aksi</th>
                </tr></thead>
                <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td data-order="{{ $report->input_date->format('Y-m-d') }}" class="whitespace-nowrap font-medium text-slate-800">{{ $report->input_date->translatedFormat('d M Y') }}</td>
                        @if(auth()->user()->isAdmin())<td class="whitespace-nowrap">{{ $report->user->name }}</td>@endif
                        <td class="whitespace-nowrap text-slate-500">{{ substr($report->start_time, 0, 5) }}–{{ substr($report->end_time, 0, 5) }}</td>
                        <td class="max-w-xs"><span class="line-clamp-2">{{ $report->work_activity }}</span></td>
                        <td><a class="text-teal-700 hover:underline" target="_blank" rel="noopener" href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}">Peta</a></td>
                        <td>{!! $report->attachment_path ? '<span class="badge bg-emerald-50 text-emerald-700">Ada</span>' : '<span class="text-slate-400">—</span>' !!}</td>
                        <td>
                            <div class="flex items-center justify-end gap-1 whitespace-nowrap">
                                <a
                                    href="{{ route('work-reports.show', $report) }}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-teal-700 transition hover:bg-teal-50"
                                    title="Lihat detail"
                                    aria-label="Lihat detail laporan"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg>
                                </a>
                                <a
                                    href="{{ route('work-reports.edit', $report) }}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100"
                                    title="Edit laporan"
                                    aria-label="Edit laporan"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.5 6.5 17.5 10.5M4 20l4.5-1 10-10a2.83 2.83 0 0 0-4-4l-10 10L4 20Z"/></svg>
                                </a>
                                <button
                                    type="button"
                                    @click="
                                        deleteUrl = @js(route('work-reports.destroy', $report));
                                        deleteDate = @js($report->input_date->translatedFormat('d F Y'));
                                        deleting = false;
                                        $dispatch('open-modal', 'confirm-report-deletion');
                                    "
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-rose-600 transition hover:bg-rose-50"
                                    title="Hapus laporan"
                                    aria-label="Hapus laporan"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M9 7V4h6v3M6.5 7l.7 13h9.6l.7-13M10 11v5M14 11v5"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex items-center gap-2 border-t border-slate-100 px-5 py-3 text-xs text-slate-500">
            Tampilkan
            <select id="page-length" class="rounded-lg border-slate-200 py-1 text-xs"><option>10</option><option>25</option><option>50</option></select>
            per halaman
        </div>
    </div>

    <x-modal name="confirm-report-deletion" maxWidth="md" focusable>
        <div class="p-6 sm:p-7">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4M12 17h.01M10.3 3.8 2.4 18a2 2 0 0 0 1.75 3h15.7a2 2 0 0 0 1.75-3L13.7 3.8a2 2 0 0 0-3.4 0Z"/></svg>
            </div>

            <h2 class="mt-5 text-lg font-bold text-slate-900">Hapus laporan?</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">
                Laporan tanggal <strong class="font-semibold text-slate-700" x-text="deleteDate"></strong> akan dihapus permanen beserta file lampirannya. Tindakan ini tidak dapat dibatalkan.
            </p>

            <form method="POST" :action="deleteUrl" @submit="deleting = true" class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                @csrf
                @method('DELETE')
                <button
                    type="button"
                    @click="$dispatch('close-modal', 'confirm-report-deletion')"
                    class="btn-secondary"
                    :disabled="deleting"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="deleting"
                >
                    <svg x-show="deleting" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"/></svg>
                    <span x-text="deleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                </button>
            </form>
        </div>
    </x-modal>
    </div>
</x-app-layout>
