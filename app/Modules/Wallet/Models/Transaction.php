<?php

namespace App\Modules\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'points',
        'description',
        'reference',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'points' => 'integer',
    ];

    /**
     * Get the user owning this transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
