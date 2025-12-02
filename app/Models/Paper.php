<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id', 'title', 'abstract', 'theme', 'keywords', 'file_word_path', 'file_pdf_path', 'final_print_path', 'status', 'word_count', 'submitted_at', 'accepted_at', 'reviewers_count', 'completed_reviews', 'review_deadline', 'review_summary', 'review_scores', 'requires_major_revision', 'requires_minor_revision', 'ready_for_print', 'sent_to_print_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'accepted_at' => 'datetime',
        'review_deadline' => 'date',
        'review_scores' => 'array',
        'requires_major_revision' => 'boolean',
        'requires_minor_revision' => 'boolean',
        'ready_for_print' => 'boolean',
        'sent_to_print_at' => 'datetime',
    ];

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
