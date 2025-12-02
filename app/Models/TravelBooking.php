<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id', 'type', 'airline_id', 'flight_number', 'flight_date', 'flight_time', 'airport_from_id', 'airport_to_id', 'ticket_number', 'hotel_id', 'room_type', 'check_in', 'check_out', 'cost', 'ticket_path', 'delivery_method', 'delivery_confirmed_at', 'booking_status', 'created_by',
    ];

    protected $casts = [
        'flight_date' => 'date',
        'flight_time' => 'datetime:H:i:s',
        'check_in' => 'date',
        'check_out' => 'date',
        'cost' => 'decimal:2',
        'delivery_confirmed_at' => 'datetime',
    ];

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function airportFrom()
    {
        return $this->belongsTo(Airport::class, 'airport_from_id');
    }

    public function airportTo()
    {
        return $this->belongsTo(Airport::class, 'airport_to_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
