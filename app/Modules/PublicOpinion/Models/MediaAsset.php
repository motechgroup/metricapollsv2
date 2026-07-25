<?php

namespace App\Modules\PublicOpinion\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'category',
        'file_size',
    ];
}
