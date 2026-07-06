<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <div class="flex flex-wrap items-center gap-x-2 text-sm font-medium text-teal-700">
                <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                <span class="text-teal-300">•</span>
                <span
                    x-data="liveClock(@js(config('app.timezone')))"
                    x-text="time + ' WIB'"
                    class="tabular-nums"
                    aria-live="off"
                >{{ now()->format('H:i:s') }} WIB</span>
            </div>
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
            <p x-data="counter({{ $totalReports }})" x-text="formatter.format(current)" class="mt-3 text-3xl font-bold tabular-nums text-slate-900">0</p>
            <p class="mt-1 text-xs text-slate-400">Seluruh periode</p>
        </div>
        <div class="card p-5">
            <p class="text-sm font-medium text-slate-500">Laporan bulan ini</p>
            <p x-data="counter({{ $monthlyReports }})" x-text="formatter.format(current)" class="mt-3 text-3xl font-bold tabular-nums text-teal-700">0</p>
            <p class="mt-1 text-xs text-slate-400">{{ now()->translatedFormat('F Y') }}</p>
        </div>
        @if(auth()->user()->isAdmin())
            <div class="card p-5">
                <p class="text-sm font-medium text-slate-500">Pengguna terdaftar</p>
                <p x-data="counter({{ $totalUsers }})" x-text="formatter.format(current)" class="mt-3 text-3xl font-bold tabular-nums text-slate-900">0</p>
                <a href="{{ route('admin.users.index') }}" class="mt-1 inline-block text-xs font-medium text-teal-700">Kelola pengguna →</a>
            </div>
        @endif
    </div>

    <div id="report-chart-section" class="card mt-6 scroll-mt-6 p-5 sm:p-6">
        <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
            <div>
                <h2 class="font-semibold text-slate-900">Tren laporan kerja</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $reportChartDescription }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <form action="{{ route('dashboard') }}#report-chart-section" method="GET" class="flex flex-1 items-center gap-2 sm:flex-none" x-data="{ loading: false }">
                    <div class="relative min-w-0 flex-1 sm:w-40 sm:flex-none">
                        <select name="chart_month" class="filter-select h-10 text-xs" aria-label="Filter bulan grafik" @change="loading = true; $el.form.submit()">
                            <option value="">Semua bulan</option>
                            @foreach(range(1, 12) as $month)
                                <option value="{{ $month }}" @selected($selectedChartMonth == $month)>{{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 10 5 5 5-5"/></svg>
                    </div>
                    <div class="relative min-w-0 flex-1 sm:w-32 sm:flex-none">
                        <select name="chart_year" class="filter-select h-10 text-xs" aria-label="Filter tahun grafik" @change="loading = true; $el.form.submit()">
                            @foreach(range(now()->year, now()->year - 5) as $year)
                                <option value="{{ $year }}" @selected($selectedChartYear == $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 10 5 5 5-5"/></svg>
                    </div>
                </form>
                <div class="inline-flex h-10 w-fit items-center gap-2 rounded-full bg-teal-50 px-3 text-xs font-semibold text-teal-700">
                    <span class="h-2 w-2 rounded-full bg-teal-500"></span>
                    {{ $reportChartValues->sum() }} laporan
                </div>
            </div>
        </div>
        <div class="relative mt-6 h-72 w-full sm:h-80">
            <canvas
                id="report-chart"
                data-labels='@json($reportChartLabels)'
                data-values='@json($reportChartValues)'
                aria-label="Grafik jumlah laporan kerja enam bulan terakhir"
                role="img"
            ></canvas>
        </div>
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
