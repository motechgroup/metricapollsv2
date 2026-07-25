<?php

namespace App\Modules\PublicOpinion\Models;

use Illuminate\Database\Eloquent\Model;

class Constituency extends Model
{
    protected $table = 'constituencies';

    protected $fillable = [
        'county_id',
        'name',
    ];

    public function county()
    {
        return $this->belongsTo(County::class);
    }
}
