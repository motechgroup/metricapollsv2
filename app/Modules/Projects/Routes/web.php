<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Projects\Livewire\RequestReview;
use App\Modules\Projects\Livewire\ProjectList;

Route::middleware(['auth', 'role:Super Admin|Admin|Project Manager|Field Manager'])->group(function () {
    Route::get('/admin/research-requests', RequestReview::class)->name('admin.research-requests');
    Route::get('/admin/projects', ProjectList::class)->name('admin.projects');
});
