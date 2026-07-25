<?php

use Illuminate\Support\Facades\Route;
use App\Modules\PublicOpinion\Livewire\Marketplace;
use App\Modules\PublicOpinion\Livewire\Academy;
use App\Modules\PublicOpinion\Livewire\PublicOpinionPolls;
use App\Modules\PublicOpinion\Livewire\PublicOpinionPollDetail;
use App\Modules\PublicOpinion\Livewire\PublicReportsGallery;
use App\Modules\PublicOpinion\Livewire\AdminPollCreator;
use App\Modules\PublicOpinion\Livewire\AdminPollManager;
use App\Modules\PublicOpinion\Livewire\AdminPayoutManager;
use App\Modules\PublicOpinion\Livewire\AdminPanelistManager;
use App\Modules\PublicOpinion\Livewire\AdminPoliticalPartyManager;
use App\Modules\PublicOpinion\Livewire\AdminPoliticianManager;
use App\Modules\PublicOpinion\Livewire\AdminLivePollManager;
use App\Modules\PublicOpinion\Livewire\AdminMediaGallery;

// Public routes
Route::get('/marketplace', Marketplace::class)->name('public.marketplace');
Route::get('/public-opinion', PublicOpinionPolls::class)->name('public.opinion');
Route::get('/public-opinion/{poll}', PublicOpinionPollDetail::class)->name('public.opinion.show');
Route::get('/public-reports', PublicReportsGallery::class)->name('public.reports');

// Auth routes for Panelists/Agents/Admins
Route::middleware(['auth'])->group(function () {
    Route::get('/academy', Academy::class)->name('panelist.academy');
});

// Admin Poll / Report Builder / Political Management routes
Route::middleware(['auth', 'role:Super Admin|Admin|Project Manager'])->group(function () {
    Route::get('/admin/polls', AdminPollManager::class)->name('admin.polls.index');
    Route::get('/admin/polls/create', AdminPollCreator::class)->name('admin.polls.create');
    Route::get('/admin/live-polls', AdminLivePollManager::class)->name('admin.live-polls.index');
    Route::get('/admin/media-gallery', AdminMediaGallery::class)->name('admin.media-gallery.index');
    Route::get('/admin/political-parties', AdminPoliticalPartyManager::class)->name('admin.parties.index');
    Route::get('/admin/politicians', AdminPoliticianManager::class)->name('admin.politicians.index');
    Route::get('/admin/payouts', AdminPayoutManager::class)->name('admin.payouts.index');
    Route::get('/admin/panelists', AdminPanelistManager::class)->name('admin.panelists.index');
});
