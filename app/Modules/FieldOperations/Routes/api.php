<?php

use Illuminate\Support\Facades\Route;
use App\Modules\FieldOperations\Controllers\SyncController;

Route::post('/sync/responses', [SyncController::class, 'syncResponses'])->name('api.field.sync');
