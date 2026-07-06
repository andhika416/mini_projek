<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkReportController;
use App\Models\User;
use App\Models\WorkReport;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/dashboard', function () {
    $query = WorkReport::query();
    if (! auth()->user()->isAdmin()) {
        $query->where('user_id', auth()->id());
    }

    return view('dashboard', [
        'totalReports' => (clone $query)->count(),
        'monthlyReports' => (clone $query)->whereMonth('input_date', now()->month)->whereYear('input_date', now()->year)->count(),
        'recentReports' => $query->with('user')->latest('input_date')->limit(5)->get(),
        'totalUsers' => auth()->user()->isAdmin() ? User::count() : null,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('work-reports/export/pdf', [WorkReportController::class, 'export'])->name('work-reports.export');
    Route::get('work-reports/{work_report}/pdf', [WorkReportController::class, 'pdf'])->name('work-reports.pdf');
    Route::get('work-reports/{work_report}/attachment', [WorkReportController::class, 'attachment'])->name('work-reports.attachment');
    Route::resource('work-reports', WorkReportController::class);

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
