<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><title>Laporan Kerja</title>
<style>
body{font-family:DejaVu Sans,sans-serif;color:#263238;font-size:11px;line-height:1.55}h1{font-size:20px;margin:0;color:#115e59}.muted{color:#64748b}.header{border-bottom:2px solid #0f766e;padding-bottom:14px;margin-bottom:20px}.meta{width:100%;border-collapse:collapse;margin-bottom:22px}.meta td{width:25%;padding:10px;border:1px solid #dbe3e7;vertical-align:top}.label{font-size:8px;text-transform:uppercase;color:#64748b;margin-bottom:4px}.section{margin-bottom:16px}.section h2{font-size:11px;color:#115e59;margin:0 0 5px}.footer{position:fixed;bottom:0;border-top:1px solid #ddd;width:100%;padding-top:7px;font-size:8px;color:#94a3b8}
</style></head><body>
<div class="header"><h1>Laporan Kerja Bulanan</h1><div class="muted">Dokumen laporan aktivitas kerja</div></div>
<table class="meta"><tr>
<td><div class="label">Tanggal Input</div><strong>{{ $workReport->input_date->translatedFormat('d F Y') }}</strong></td>
<td><div class="label">User Input</div><strong>{{ $workReport->user->name }}</strong></td>
<td><div class="label">Jam Kerja</div><strong>{{ substr($workReport->start_time,0,5) }} – {{ substr($workReport->end_time,0,5) }}</strong></td>
<td><div class="label">Lokasi GPS</div><strong>{{ $workReport->latitude }}, {{ $workReport->longitude }}</strong></td>
</tr></table>
<div class="section"><h2>URAIAN RENCANA KERJA</h2><div>{!! nl2br(e($workReport->work_plan)) !!}</div></div>
<div class="section"><h2>URAIAN AKTIVITAS KERJA</h2><div>{!! nl2br(e($workReport->work_activity)) !!}</div></div>
<div class="section"><h2>URAIAN HASIL KERJA</h2><div>{!! nl2br(e($workReport->work_result)) !!}</div></div>
<div class="section"><h2>FILE KERJA</h2><div>{{ $workReport->attachment_name ?? 'Tidak ada lampiran' }}</div></div>
<div class="footer">Dicetak pada {{ now()->translatedFormat('d F Y H:i') }} · {{ config('app.name') }}</div>
</body></html>
