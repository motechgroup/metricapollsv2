<?php

namespace App\Modules\PublicOpinion\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regions';

    protected $fillable = [
        'name',
        'code',
    ];

    public function counties()
    {
        return $this->hasMany(County::class);
    }
}
