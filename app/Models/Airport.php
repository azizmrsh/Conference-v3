<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'iata_code', 'city', 'country_id', 'created_by',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function arrivals()
    {
        return $this->hasMany(TravelBooking::class, 'airport_to_id');
    }

    public function departures()
    {
        return $this->hasMany(TravelBooking::class, 'airport_from_id');
    }
}
