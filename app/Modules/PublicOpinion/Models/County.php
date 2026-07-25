<?php

namespace App\Modules\PublicOpinion\Models;

use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    protected $table = 'counties';

    protected $fillable = [
        'region_id',
        'name',
        'code',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function constituencies()
    {
        return $this->hasMany(Constituency::class);
    }
}
