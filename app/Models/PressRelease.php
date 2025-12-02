<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PressRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_campaign_id', 'title', 'content', 'release_type', 'scheduled_release_time', 'actual_release_time', 'status', 'created_by',
    ];

    protected $casts = [
        'scheduled_release_time' => 'datetime',
        'actual_release_time' => 'datetime',
    ];

    public function mediaCampaign()
    {
        return $this->belongsTo(MediaCampaign::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
