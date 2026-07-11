<?php

use Illuminate\Support\Facades\Route;
use App\Modules\FieldOperations\Livewire\AgentAssignments;

Route::middleware(['auth', 'role:Field Agent|Super Admin|Admin'])->group(function () {
    Route::get('/agent/assignments', AgentAssignments::class)->name('agent.assignments');
});
