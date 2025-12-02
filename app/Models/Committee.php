<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id', 'name', 'description', 'created_by',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'committee_members')->withPivot('role');
    }
}
