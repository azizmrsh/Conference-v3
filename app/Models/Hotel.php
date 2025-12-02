<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'contact_person', 'phone', 'email', 'rating', 'notes', 'created_by',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function travelBookings()
    {
        return $this->hasMany(TravelBooking::class);
    }
}
