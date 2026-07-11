<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatewaySetting extends Model
{
    protected $fillable = [
        'name',
        'credentials',
    ];

    protected $casts = [
        'credentials' => 'array',
    ];
}
