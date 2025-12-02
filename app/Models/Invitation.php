<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id', 'member_id', 'status', 'role', 'invitation_sent_at', 'response_received_at', 'needs_visa', 'visa_issued', 'flight_booked', 'hotel_booked', 'bag_received', 'badge_printed', 'reminders_sent', 'emergency_contact', 'dietary_requirements', 'accessibility_needs', 'documents_received', 'notes', 'expected_arrival', 'expected_departure', 'preferred_communication',
    ];

    protected $casts = [
        'invitation_sent_at' => 'datetime',
        'response_received_at' => 'datetime',
        'needs_visa' => 'boolean',
        'visa_issued' => 'boolean',
        'flight_booked' => 'boolean',
        'hotel_booked' => 'boolean',
        'bag_received' => 'boolean',
        'badge_printed' => 'boolean',
        'expected_arrival' => 'datetime',
        'expected_departure' => 'datetime',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function travelBookings()
    {
        return $this->hasMany(TravelBooking::class);
    }

    public function papers()
    {
        return $this->hasMany(Paper::class);
    }
}
