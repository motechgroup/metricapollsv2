<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Clients\Livewire\ClientRequests;
use App\Modules\Clients\Livewire\CreateRequest;
use App\Modules\Clients\Livewire\ClientProjects;

Route::middleware(['auth', 'role:Client'])->group(function () {
    Route::get('/client/requests', ClientRequests::class)->name('client.requests');
    Route::get('/client/requests/create', CreateRequest::class)->name('client.requests.create');
    Route::get('/client/projects', ClientProjects::class)->name('client.projects');
});
