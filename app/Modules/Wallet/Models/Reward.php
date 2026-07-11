<?php

namespace App\Modules\Wallet\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'name',
        'description',
        'points_cost',
        'type',
        'stock',
    ];

    protected $casts = [
        'points_cost' => 'integer',
        'stock' => 'integer',
    ];
}
