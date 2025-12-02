<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correspondence extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id', 'direction', 'category', 'ref_number', 'correspondence_date', 'recipient_entity', 'subject', 'content', 'file_path', 'response_received', 'response_date', 'status', 'priority', 'requires_follow_up', 'follow_up_date', 'notes', 'created_by',
    ];

    protected $casts = [
        'correspondence_date' => 'date',
        'response_received' => 'boolean',
        'response_date' => 'date',
        'requires_follow_up' => 'boolean',
        'follow_up_date' => 'date',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
