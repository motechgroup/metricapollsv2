<?php

namespace App\Modules\PublicOpinion\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MarketplaceReport extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'file_path',
        'author_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the author of the report.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
