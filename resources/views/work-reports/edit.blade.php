<x-app-layout>
    <x-slot name="title">Edit Laporan</x-slot>
    <div class="mx-auto max-w-4xl">
        <div class="mb-6"><a href="{{ route('work-reports.show', $workReport) }}" class="text-sm font-medium text-slate-500 hover:text-teal-700">← Kembali ke detail</a><h1 class="mt-3 text-2xl font-bold text-slate-900">Edit Laporan Kerja</h1><p class="mt-1 text-sm text-slate-500">Perbarui data laporan tanggal {{ $workReport->input_date->translatedFormat('d F Y') }}.</p></div>
        <form method="POST" action="{{ route('work-reports.update', $workReport) }}" enctype="multipart/form-data" class="card p-5 sm:p-7">@csrf @method('PUT') @include('work-reports._form')</form>
    </div>
</x-app-layout>
