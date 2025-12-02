<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id', 'conference_session_id', 'invitation_id', 'member_id', 'badge_code', 'method', 'check_in_at', 'check_out_at', 'notes', 'created_by',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function session()
    {
        return $this->belongsTo(ConferenceSession::class, 'conference_session_id');
    }

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

