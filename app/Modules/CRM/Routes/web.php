<?php

use Illuminate\Support\Facades\Route;
use App\Modules\CRM\Livewire\ClientManagement;

Route::middleware(['auth', 'role:Super Admin|Admin'])->group(function () {
    Route::get('/admin/crm', ClientManagement::class)->name('admin.crm');
});
