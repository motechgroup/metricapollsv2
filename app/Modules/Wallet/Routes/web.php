<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Wallet\Livewire\PanelistDashboard;
use App\Modules\Wallet\Livewire\PanelistWallet;
use App\Modules\Wallet\Livewire\PanelistQualifications;

Route::middleware(['auth', 'role:Panelist|Super Admin|Admin'])->group(function () {
    Route::get('/panelist/dashboard', PanelistDashboard::class)->name('panelist.dashboard');
    Route::get('/panelist/wallet', PanelistWallet::class)->name('panelist.wallet');
    Route::get('/panelist/qualifications', PanelistQualifications::class)->name('panelist.qualifications');
});
