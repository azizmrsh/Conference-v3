<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id', 'title', 'campaign_type', 'plan_text', 'target_outlets', 'press_kit_notes', 'press_conference_date', 'estimated_budget', 'actual_cost', 'status', 'manager_user_id',
    ];

    protected $casts = [
        'press_conference_date' => 'datetime',
        'estimated_budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function pressReleases()
    {
        return $this->hasMany(PressRelease::class);
    }
}
