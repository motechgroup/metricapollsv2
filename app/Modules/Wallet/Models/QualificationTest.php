<?php

namespace App\Modules\Wallet\Models;

use Illuminate\Database\Eloquent\Model;

class QualificationTest extends Model
{
    protected $fillable = [
        'title',
        'description',
        'questions',
        'reward_points',
        'level',
    ];

    protected $casts = [
        'questions' => 'array',
        'reward_points' => 'integer',
        'level' => 'string',
    ];
}
