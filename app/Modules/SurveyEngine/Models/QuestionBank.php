<?php

namespace App\Modules\SurveyEngine\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    protected $table = 'question_bank';

    protected $fillable = [
        'category',
        'type',
        'question_text',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];
}
