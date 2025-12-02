<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'session_number',
        'hijri_date',
        'gregorian_date',
        'sessions_count',
        'start_date',
        'end_date',
        'venue_name',
        'venue_address',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hijri_date' => 'datetime',
        'gregorian_date' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'sessions_count' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function sessions()
    {
        return $this->hasMany(ConferenceSession::class);
    }

    public function topics()
    {
        return $this->hasMany(ConferenceTopic::class);
    }

    public function updateSessionsCount(): void
    {
        $this->sessions_count = $this->sessions()->count();
        $this->saveQuietly();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function mediaCampaigns()
    {
        return $this->hasMany(MediaCampaign::class);
    }

    public function committees()
    {
        return $this->hasMany(Committee::class);
    }

    public function badgesKits()
    {
        return $this->hasMany(BadgesKit::class);
    }

    public function correspondences()
    {
        return $this->hasMany(Correspondence::class);
    }
}
