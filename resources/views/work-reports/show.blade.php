<x-app-layout>
    <x-slot name="title">Detail Laporan</x-slot>
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div><a href="{{ route('work-reports.index') }}" class="text-sm font-medium text-slate-500 hover:text-teal-700">← Kembali ke laporan</a><h1 class="mt-3 text-2xl font-bold text-slate-900">Detail Laporan Kerja</h1><p class="mt-1 text-sm text-slate-500">Dibuat {{ $workReport->created_at->diffForHumans() }}</p></div>
            <div class="flex gap-2"><a href="{{ route('work-reports.pdf', $workReport) }}" class="btn-secondary">Unduh PDF</a><a href="{{ route('work-reports.edit', $workReport) }}" class="btn-primary">Edit Laporan</a></div>
        </div>

        <div class="card overflow-hidden">
            <div class="grid gap-5 border-b border-slate-100 bg-slate-50/70 p-5 sm:grid-cols-2 lg:grid-cols-4">
                <div><p class="text-xs font-medium uppercase tracking-wide text-slate-400">Tanggal</p><p class="mt-1 text-sm font-semibold text-slate-800">{{ $workReport->input_date->translatedFormat('d F Y') }}</p></div>
                <div><p class="text-xs font-medium uppercase tracking-wide text-slate-400">User Input</p><p class="mt-1 text-sm font-semibold text-slate-800">{{ $workReport->user->name }}</p></div>
                <div><p class="text-xs font-medium uppercase tracking-wide text-slate-400">Jam Kerja</p><p class="mt-1 text-sm font-semibold text-slate-800">{{ substr($workReport->start_time, 0, 5) }} – {{ substr($workReport->end_time, 0, 5) }}</p></div>
                <div><p class="text-xs font-medium uppercase tracking-wide text-slate-400">Lokasi GPS</p><a target="_blank" rel="noopener" href="https://www.google.com/maps?q={{ $workReport->latitude }},{{ $workReport->longitude }}" class="mt-1 block text-sm font-semibold text-teal-700">{{ $workReport->latitude }}, {{ $workReport->longitude }}</a></div>
            </div>
            <div class="space-y-7 p-5 sm:p-7">
                @foreach(['work_plan' => 'Rencana Kerja', 'work_activity' => 'Aktivitas Kerja', 'work_result' => 'Hasil Kerja'] as $field => $label)
                    <section><h2 class="mb-2 text-sm font-bold text-slate-800">{{ $label }}</h2><p class="whitespace-pre-line text-sm leading-7 text-slate-600">{{ $workReport->{$field} }}</p></section>
                @endforeach
                <section class="rounded-xl border border-slate-200 p-4">
                    <h2 class="text-sm font-bold text-slate-800">File Kerja</h2>
                    @if($workReport->attachment_path)
                        <div class="mt-2 flex flex-wrap items-center justify-between gap-3"><p class="break-all text-sm text-slate-600">{{ $workReport->attachment_name }}</p><a href="{{ route('work-reports.attachment', $workReport) }}" class="text-sm font-semibold text-teal-700">Unduh file</a></div>
                    @else
                        <p class="mt-2 text-sm text-slate-400">Tidak ada file dilampirkan.</p>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
