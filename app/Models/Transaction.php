<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id', 'tx_type', 'category', 'item_name', 'estimated_amount', 'actual_amount', 'incurred_at', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'estimated_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'incurred_at' => 'date',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
