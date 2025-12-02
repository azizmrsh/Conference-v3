<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConferenceTopic extends Model
{
    protected $fillable = [
        'conference_id',
        'title',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ConferenceSession::class, 'topic_id')->orderBy('session_order');
    }
}
