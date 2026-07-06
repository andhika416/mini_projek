<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-medium text-teal-700">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Selamat datang, {{ auth()->user()->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">Ringkasan aktivitas laporan kerja Anda.</p>
        </div>
        <a href="{{ route('work-reports.create') }}" class="btn-primary">
            <span class="text-lg leading-none">+</span> Buat Laporan
        </a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 {{ auth()->user()->isAdmin() ? 'lg:grid-cols-3' : '' }}">
        <div class="card p-5">
            <p class="text-sm font-medium text-slate-500">Total laporan</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($totalReports) }}</p>
            <p class="mt-1 text-xs text-slate-400">Seluruh periode</p>
        </div>
        <div class="card p-5">
            <p class="text-sm font-medium text-slate-500">Laporan bulan ini</p>
            <p class="mt-3 text-3xl font-bold text-teal-700">{{ number_format($monthlyReports) }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ now()->translatedFormat('F Y') }}</p>
        </div>
        @if(auth()->user()->isAdmin())
            <div class="card p-5">
                <p class="text-sm font-medium text-slate-500">Pengguna terdaftar</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($totalUsers) }}</p>
                <a href="{{ route('admin.users.index') }}" class="mt-1 inline-block text-xs font-medium text-teal-700">Kelola pengguna →</a>
            </div>
        @endif
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="font-semibold text-slate-900">Laporan terbaru</h2>
                <p class="text-xs text-slate-500">Lima laporan terakhir</p>
            </div>
            <a href="{{ route('work-reports.index') }}" class="text-sm font-semibold text-teal-700">Lihat semua</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($recentReports as $report)
                <a href="{{ route('work-reports.show', $report) }}" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-slate-50">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-800">{{ Str::limit($report->work_activity, 70) }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $report->input_date->translatedFormat('d M Y') }} · {{ $report->start_time }}–{{ $report->end_time }} @if(auth()->user()->isAdmin()) · {{ $report->user->name }} @endif</p>
                    </div>
                    <span class="text-slate-400">›</span>
                </a>
            @empty
                <div class="px-5 py-10 text-center text-sm text-slate-500">Belum ada laporan kerja.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
