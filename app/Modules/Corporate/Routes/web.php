<?php

use App\Modules\Corporate\Controllers\CorporateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CorporateController::class, 'index'])->name('corporate.index');
Route::get('/features', [CorporateController::class, 'features'])->name('corporate.features');
Route::get('/pricing', [CorporateController::class, 'pricing'])->name('corporate.pricing');
Route::get('/about', [CorporateController::class, 'about'])->name('corporate.about');
Route::get('/contact', [CorporateController::class, 'contact'])->name('corporate.contact');
Route::get('/maintenance', [CorporateController::class, 'maintenance'])->name('public.maintenance');
Route::get('/terms', [CorporateController::class, 'terms'])->name('corporate.terms');
Route::get('/privacy', [CorporateController::class, 'privacy'])->name('corporate.privacy');
