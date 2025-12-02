<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'iso_code', 'visa_required_for_jordan', 'created_by',
    ];

    protected $casts = [
        'visa_required_for_jordan' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function airports()
    {
        return $this->hasMany(Airport::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'nationality_id');
    }
}
