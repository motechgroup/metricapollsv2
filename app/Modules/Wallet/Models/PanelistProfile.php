<?php

namespace App\Modules\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PanelistProfile extends Model
{
    protected $table = 'panelists';

    protected $fillable = [
        'user_id',
        'gender',
        'date_of_birth',
        'education_level',
        'income_bracket',
        'location_region',
        'is_verified',
        'points_balance',
        'experience_points',
        'badge_level',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_verified' => 'boolean',
        'points_balance' => 'integer',
        'experience_points' => 'integer',
    ];

    /**
     * Get the user owning this panel profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
