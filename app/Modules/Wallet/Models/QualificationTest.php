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
    ];

    protected $casts = [
        'questions' => 'array',
        'reward_points' => 'integer',
    ];
}
