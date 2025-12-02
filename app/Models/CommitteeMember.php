<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommitteeMember extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'committee_id',
        'member_id',
        'role',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
