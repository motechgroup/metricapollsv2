<?php

namespace App\Modules\PublicOpinion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PublicOpinion extends Model
{
    protected $table = 'public_opinions';

    protected $fillable = [
        'topic',
        'options',
        'status',
        'votes_count',
    ];

    protected $casts = [
        'options' => 'array',
        'votes_count' => 'integer',
    ];

    /**
     * Get the individual votes for this opinion poll.
     */
    public function votes()
    {
        return $this->hasMany(PublicOpinionVote::class);
    }
}

class PublicOpinionVote extends Model
{
    protected $table = 'public_opinion_votes';

    protected $fillable = [
        'public_opinion_id',
        'ip_address',
        'voted_option',
    ];

    public function publicOpinion()
    {
        return $this->belongsTo(PublicOpinion::class);
    }
}
