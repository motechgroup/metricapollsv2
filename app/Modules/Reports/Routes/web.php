<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Reports\Livewire\ReportsDashboard;

Route::middleware(['auth', 'role:Super Admin|Admin|Project Manager|Field Manager|Client'])->group(function () {
    Route::get('/admin/reports', ReportsDashboard::class)->name('admin.reports');
});
