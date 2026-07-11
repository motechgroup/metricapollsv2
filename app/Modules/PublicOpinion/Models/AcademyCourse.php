<?php

namespace App\Modules\PublicOpinion\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyCourse extends Model
{
    protected $fillable = [
        'title',
        'description',
        'lessons',
        'points_award',
    ];

    protected $casts = [
        'lessons' => 'array',
        'points_award' => 'integer',
    ];
}
