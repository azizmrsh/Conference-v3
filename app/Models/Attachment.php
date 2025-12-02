<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'attachable_type', 'attachable_id', 'collection', 'disk', 'path', 'filename', 'mime', 'size', 'uploaded_by', 'meta', 'created_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
        'size' => 'integer',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
