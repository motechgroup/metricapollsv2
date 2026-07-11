<?php

namespace App\Modules\PublicOpinion\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPoll extends Model
{
    protected $table = 'admin_polls';

    protected $fillable = [
        'title',
        'category',
        'options',
        'is_public',
        'ai_report',
        'research_date',
        'release_date',
        'initial_downloads',
        'download_count',
        'sample_size',
        'region',
        'methodology',
    ];

    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean',
        'research_date' => 'date',
        'release_date' => 'date',
        'initial_downloads' => 'integer',
        'download_count' => 'integer',
        'sample_size' => 'integer',
    ];

    /**
     * Get the downloads tracked for this report.
     */
    public function downloads()
    {
        return $this->hasMany(ReportDownload::class);
    }
}

class ReportDownload extends Model
{
    protected $table = 'report_downloads';

    public $timestamps = false;

    protected $fillable = [
        'admin_poll_id',
        'email',
        'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    public function adminPoll()
    {
        return $this->belongsTo(AdminPoll::class);
    }
}
