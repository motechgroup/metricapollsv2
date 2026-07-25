<?php

namespace App\Modules\PublicOpinion\Models;

use Illuminate\Database\Eloquent\Model;

class Politician extends Model
{
    protected $table = 'politicians';

    protected $fillable = [
        'name',
        'photo_path',
        'political_party_id',
        'level',
        'region_id',
        'county_id',
        'constituency_id',
        'position_title',
        'bio',
    ];

    public function getPhotoPathAttribute($value)
    {
        return (!empty($value) && trim($value) !== '') ? $value : '/images/favicon.png';
    }

    public function politicalParty()
    {
        return $this->belongsTo(PoliticalParty::class, 'political_party_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function county()
    {
        return $this->belongsTo(County::class);
    }

    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
    }
}
