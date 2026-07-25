<?php

namespace App\Modules\PublicOpinion\Models;

use Illuminate\Database\Eloquent\Model;

class PublicOpinion extends Model
{
    protected $table = 'public_opinions';

    protected $fillable = [
        'topic',
        'target_level',
        'region_id',
        'county_id',
        'constituency_id',
        'position_title',
        'options',
        'candidates_data',
        'status',
        'allow_public_voting',
        'expires_at',
        'votes_count',
    ];

    protected $casts = [
        'options' => 'array',
        'candidates_data' => 'array',
        'allow_public_voting' => 'boolean',
        'expires_at' => 'datetime',
        'votes_count' => 'integer',
    ];

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

    /**
     * Get the comments for this opinion poll.
     */
    public function comments()
    {
        return $this->hasMany(PublicOpinionComment::class)->latest();
    }

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
