<?php

namespace App\Modules\PublicOpinion\Models;

use Illuminate\Database\Eloquent\Model;

class PublicOpinionComment extends Model
{
    protected $table = 'public_opinion_comments';

    protected $fillable = [
        'public_opinion_id',
        'author_name',
        'comment_text',
        'likes',
    ];

    public function publicOpinion()
    {
        return $this->belongsTo(PublicOpinion::class);
    }
}
