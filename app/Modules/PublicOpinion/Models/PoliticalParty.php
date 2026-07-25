<?php

namespace App\Modules\PublicOpinion\Models;

use Illuminate\Database\Eloquent\Model;

class PoliticalParty extends Model
{
    protected $table = 'political_parties';

    protected $fillable = [
        'name',
        'abbreviation',
        'logo_path',
        'party_color',
        'description',
    ];

    public function getLogoPathAttribute($value)
    {
        return (!empty($value) && trim($value) !== '') ? $value : '/images/favicon.png';
    }

    public function politicians()
    {
        return $this->hasMany(Politician::class);
    }
}
