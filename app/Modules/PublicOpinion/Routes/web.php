<?php

use Illuminate\Support\Facades\Route;
use App\Modules\PublicOpinion\Livewire\Marketplace;
use App\Modules\PublicOpinion\Livewire\Academy;
use App\Modules\PublicOpinion\Livewire\PublicOpinionPolls;
use App\Modules\PublicOpinion\Livewire\PublicReportsGallery;
use App\Modules\PublicOpinion\Livewire\AdminPollCreator;
use App\Modules\PublicOpinion\Livewire\AdminPollManager;

// Public routes
Route::get('/marketplace', Marketplace::class)->name('public.marketplace');
Route::get('/public-opinion', PublicOpinionPolls::class)->name('public.opinion');
Route::get('/public-reports', PublicReportsGallery::class)->name('public.reports');

// Auth routes for Panelists/Agents/Admins
Route::middleware(['auth'])->group(function () {
    Route::get('/academy', Academy::class)->name('panelist.academy');
});

// Admin Poll / Report Builder routes
Route::middleware(['auth', 'role:Super Admin|Admin|Project Manager'])->group(function () {
    Route::get('/admin/polls', AdminPollManager::class)->name('admin.polls.index');
    Route::get('/admin/polls/create', AdminPollCreator::class)->name('admin.polls.create');
});
