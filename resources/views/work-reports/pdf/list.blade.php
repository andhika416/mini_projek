<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><title>Rekap Laporan Kerja</title>
<style>
body{font-family:DejaVu Sans,sans-serif;color:#263238;font-size:8px}h1{font-size:18px;color:#115e59;margin:0}.muted{color:#64748b;margin:3px 0 16px}table{width:100%;border-collapse:collapse}th{background:#0f766e;color:white;text-align:left;padding:7px}td{border:1px solid #dbe3e7;padding:6px;vertical-align:top}.nowrap{white-space:nowrap}.footer{position:fixed;bottom:0;font-size:7px;color:#94a3b8}
</style></head><body>
<h1>Rekap Laporan Kerja</h1><p class="muted">{{ $reports->count() }} laporan · Dicetak {{ now()->translatedFormat('d F Y H:i') }}</p>
<table><thead><tr><th>Tanggal</th><th>Pengguna</th><th>Jam</th><th>Rencana</th><th>Aktivitas</th><th>Hasil</th><th>GPS</th></tr></thead><tbody>
@forelse($reports as $report)<tr>
<td class="nowrap">{{ $report->input_date->format('d/m/Y') }}</td><td>{{ $report->user->name }}</td><td class="nowrap">{{ substr($report->start_time,0,5) }}–{{ substr($report->end_time,0,5) }}</td><td>{{ $report->work_plan }}</td><td>{{ $report->work_activity }}</td><td>{{ $report->work_result }}</td><td class="nowrap">{{ $report->latitude }},<br>{{ $report->longitude }}</td>
</tr>@empty<tr><td colspan="7" style="text-align:center">Tidak ada data laporan.</td></tr>@endforelse
</tbody></table><div class="footer">{{ config('app.name') }}</div>
</body></html>
