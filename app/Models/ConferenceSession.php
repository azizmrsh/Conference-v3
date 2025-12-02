<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConferenceSession extends Model
{
    use HasFactory;

    protected $table = 'conference_sessions';

    protected $fillable = [
        'conference_id',
        'topic_id',
        'session_title',
        'date',
        'start_time',
        'end_time',
        'hall_name',
        'chair_member_id',
        'session_order',
        'created_by',
    ];

    protected $casts = [
        'date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'session_order' => 'integer',
    ];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ConferenceTopic::class, 'topic_id');
    }

    public function chair(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'chair_member_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::saved(function (ConferenceSession $session) {
            $session->conference->updateSessionsCount();
        });

        static::deleted(function (ConferenceSession $session) {
            $session->conference->updateSessionsCount();
        });
    }
}
