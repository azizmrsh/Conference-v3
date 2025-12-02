<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgesKit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'conference_id', 'item_type', 'description', 'quantity', 'cost_per_item', 'created_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'cost_per_item' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
