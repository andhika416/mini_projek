@if($errors->any())
    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Periksa kembali kolom yang ditandai di bawah.</div>
@endif

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label class="form-label" for="input_date">Tanggal Input</label>
        <input class="form-input" id="input_date" name="input_date" type="date" max="{{ now()->format('Y-m-d') }}" value="{{ old('input_date', isset($workReport) ? $workReport->input_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
        @error('input_date')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="form-label">User Input</label>
        <input class="form-input bg-slate-50" value="{{ auth()->user()->name }}" disabled>
    </div>
    <div>
        <label class="form-label" for="start_time">Jam Awal</label>
        <input class="form-input" id="start_time" name="start_time" type="time" value="{{ old('start_time', isset($workReport) ? substr($workReport->start_time, 0, 5) : '08:00') }}" required>
        @error('start_time')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="form-label" for="end_time">Jam Akhir</label>
        <input class="form-input" id="end_time" name="end_time" type="time" value="{{ old('end_time', isset($workReport) ? substr($workReport->end_time, 0, 5) : '16:00') }}" required>
        @error('end_time')<p class="form-error">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <div><p class="text-sm font-semibold text-slate-800">Lokasi GPS</p><p id="gps-status" class="text-xs text-slate-500">Masukkan koordinat atau ambil posisi perangkat.</p></div>
        <button type="button" id="get-location" class="btn-secondary py-2">Ambil Lokasi Saat Ini</button>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="form-label" for="latitude">Latitude</label><input class="form-input" id="latitude" name="latitude" type="number" step="0.0000001" value="{{ old('latitude', $workReport->latitude ?? '') }}" placeholder="-6.2000000" required>@error('latitude')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div><label class="form-label" for="longitude">Longitude</label><input class="form-input" id="longitude" name="longitude" type="number" step="0.0000001" value="{{ old('longitude', $workReport->longitude ?? '') }}" placeholder="106.8166667" required>@error('longitude')<p class="form-error">{{ $message }}</p>@enderror</div>
    </div>
    <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div id="location-map" class="h-72 w-full sm:h-80" aria-label="Peta pemilihan lokasi"></div>
        <div class="border-t border-slate-100 px-4 py-3">
            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Perkiraan alamat</p>
            <p id="location-address" class="mt-1 text-sm leading-6 text-slate-600">Pilih titik pada peta atau ambil lokasi perangkat.</p>
            <p class="mt-1 text-xs text-slate-400">Klik peta atau geser titik untuk menyesuaikan lokasi.</p>
        </div>
    </div>
</div>

@foreach(['work_plan' => 'Uraian Rencana Kerja', 'work_activity' => 'Uraian Aktivitas Kerja', 'work_result' => 'Uraian Hasil Kerja'] as $field => $label)
    <div class="mt-5">
        <label class="form-label" for="{{ $field }}">{{ $label }}</label>
        <textarea class="form-input min-h-28" id="{{ $field }}" name="{{ $field }}" rows="4" maxlength="5000" required placeholder="Tuliskan {{ strtolower($label) }} secara jelas...">{{ old($field, $workReport->{$field} ?? '') }}</textarea>
        @error($field)<p class="form-error">{{ $message }}</p>@enderror
    </div>
@endforeach

<div class="mt-5">
    <label class="form-label" for="attachment">Upload File Kerja <span class="font-normal text-slate-400">(opsional)</span></label>
    <input class="form-input file:mr-4 file:rounded-lg file:border-0 file:bg-teal-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-teal-700" id="attachment" name="attachment" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
    <p class="mt-1.5 text-xs text-slate-500">PDF, dokumen Office, gambar, atau ZIP. Maksimal 10 MB.</p>
    @if(isset($workReport) && $workReport->attachment_path)<p class="mt-2 text-xs text-teal-700">File saat ini: {{ $workReport->attachment_name }}. Unggah file baru untuk mengganti.</p>@endif
    @error('attachment')<p class="form-error">{{ $message }}</p>@enderror
</div>

<div class="mt-7 flex justify-end gap-3 border-t border-slate-100 pt-5">
    <a href="{{ isset($workReport) ? route('work-reports.show', $workReport) : route('work-reports.index') }}" class="btn-secondary">Batal</a>
    <button class="btn-primary">{{ isset($workReport) ? 'Simpan Perubahan' : 'Simpan Laporan' }}</button>
</div>
