<?php

use Illuminate\Support\Facades\Route;
use App\Modules\SurveyEngine\Livewire\SurveyDesigner;
use App\Modules\SurveyEngine\Livewire\SurveyRenderer;

Route::middleware(['auth', 'role:Super Admin|Admin|Project Manager|Field Manager'])->group(function () {
    Route::get('/admin/projects/{projectId}/survey/design', SurveyDesigner::class)->name('admin.projects.survey-design');
    Route::get('/admin/surveys', \App\Modules\SurveyEngine\Livewire\SurveyManager::class)->name('admin.surveys');
});

// Response renderer (accessible by panelists or guests to submit responses)
Route::get('/surveys/{surveyId}/respond', SurveyRenderer::class)->name('surveys.respond');
