<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Dashboard\Livewire\UserManagement;
use App\Modules\Dashboard\Livewire\RoleManagement;
use App\Modules\Dashboard\Livewire\SettingsManagement;

Route::middleware('auth')->group(function () {
    // Dynamic redirect based on role
    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard.index');

    // Admin Panel (Protected by role or permissions)
    Route::prefix('admin')->middleware(['role:Super Admin|Admin|Project Manager|Field Manager'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.index');
        
        // Livewire Management Panels
        Route::get('/users', UserManagement::class)->name('admin.users')->middleware('permission:manage users');
        Route::get('/roles', RoleManagement::class)->name('admin.roles')->middleware('permission:manage roles');
        Route::get('/settings', SettingsManagement::class)->name('admin.settings')->middleware('permission:manage settings');
        Route::get('/finances', \App\Modules\Dashboard\Livewire\FinancesManagement::class)->name('admin.finances');
    });

    // Fallbacks for Panelist & Client portal stubs
    Route::get('/client', function () {
        return view('Dashboard::client-portal');
    })->name('client.index')->middleware('role:Client');

    Route::get('/panelist', function () {
        return view('Dashboard::panelist-portal');
    })->name('panelist.index')->middleware('role:Panelist');
});
