<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'honorific_title', 'academic_title', 'type', 'membership_date', 'nationality_id', 'passport_number', 'passport_expiry', 'email', 'phone', 'cv_text', 'photo_path', 'is_active',
    ];

    protected $casts = [
        'membership_date' => 'date',
        'passport_expiry' => 'date',
        'is_active' => 'boolean',
    ];

    public function nationality()
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function committees()
    {
        return $this->belongsToMany(Committee::class, 'committee_members')->withPivot('role');
    }
}
