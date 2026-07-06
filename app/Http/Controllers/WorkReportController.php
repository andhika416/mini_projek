<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkReportRequest;
use App\Http\Requests\UpdateWorkReportRequest;
use App\Models\WorkReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WorkReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', WorkReport::class);

        $reports = $this->filteredQuery($request)->latest('input_date')->get();

        return view('work-reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', WorkReport::class);

        return view('work-reports.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkReportRequest $request)
    {
        $data = $request->validated();
        unset($data['attachment']);
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')
                ->store('work-reports/'.now()->format('Y/m'), config('filesystems.reports_disk'));
            $data['attachment_name'] = $request->file('attachment')->getClientOriginalName();
        }

        WorkReport::create($data);

        return redirect()->route('work-reports.index')->with('success', 'Laporan kerja berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkReport $workReport)
    {
        $this->authorize('view', $workReport);
        $workReport->load('user');

        return view('work-reports.show', compact('workReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkReport $workReport)
    {
        $this->authorize('update', $workReport);

        return view('work-reports.edit', compact('workReport'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkReportRequest $request, WorkReport $workReport)
    {
        $data = $request->validated();
        unset($data['attachment']);

        if ($request->hasFile('attachment')) {
            $this->deleteAttachment($workReport);
            $data['attachment_path'] = $request->file('attachment')
                ->store('work-reports/'.now()->format('Y/m'), config('filesystems.reports_disk'));
            $data['attachment_name'] = $request->file('attachment')->getClientOriginalName();
        }

        $workReport->update($data);

        return redirect()->route('work-reports.show', $workReport)->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkReport $workReport)
    {
        $this->authorize('delete', $workReport);
        $this->deleteAttachment($workReport);
        $workReport->delete();

        return redirect()->route('work-reports.index')->with('success', 'Laporan berhasil dihapus.');
    }

    public function pdf(WorkReport $workReport)
    {
        $this->authorize('view', $workReport);
        $workReport->load('user');

        return Pdf::loadView('work-reports.pdf.single', compact('workReport'))
            ->setPaper('a4')
            ->download('laporan-'.$workReport->input_date->format('Y-m-d').'.pdf');
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', WorkReport::class);
        $reports = $this->filteredQuery($request)->oldest('input_date')->get();

        return Pdf::loadView('work-reports.pdf.list', compact('reports'))
            ->setPaper('a4', 'landscape')
            ->download('laporan-kerja-'.now()->format('Y-m-d').'.pdf');
    }

    public function attachment(WorkReport $workReport)
    {
        $this->authorize('view', $workReport);
        abort_unless($workReport->attachment_path, 404);

        return Storage::disk(config('filesystems.reports_disk'))
            ->download($workReport->attachment_path, $workReport->attachment_name);
    }

    private function filteredQuery(Request $request)
    {
        $query = WorkReport::query()->with('user');

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return $query
            ->when($request->filled('month'), fn ($q) => $q->whereMonth('input_date', $request->integer('month')))
            ->when($request->filled('year'), fn ($q) => $q->whereYear('input_date', $request->integer('year')));
    }

    private function deleteAttachment(WorkReport $workReport): void
    {
        if ($workReport->attachment_path) {
            Storage::disk(config('filesystems.reports_disk'))->delete($workReport->attachment_path);
        }
    }
}
