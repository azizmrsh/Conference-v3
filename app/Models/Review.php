<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_id', 'reviewer_user_id', 'review_type', 'review_round', 'assigned_at', 'due_date', 'started_at', 'completed_at', 'recommendation', 'content_quality', 'methodology_score', 'language_quality', 'formatting_score', 'overall_score', 'general_comments', 'annotated_file_path', 'status', 'confidential', 'created_by',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'confidential' => 'boolean',
        'content_quality' => 'integer',
        'methodology_score' => 'integer',
        'language_quality' => 'integer',
        'formatting_score' => 'integer',
        'overall_score' => 'integer',
    ];

    public function paper()
    {
        return $this->belongsTo(Paper::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
