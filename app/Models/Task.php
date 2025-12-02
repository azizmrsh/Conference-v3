<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id', 'category', 'title', 'description', 'assigned_to', 'department_responsible', 'start_date', 'due_date', 'priority', 'estimated_hours', 'status', 'completion_percentage', 'completed_at', 'prerequisites', 'deliverables', 'success_criteria', 'auto_reminder', 'reminder_schedule', 'notes', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'priority' => 'integer',
        'estimated_hours' => 'integer',
        'completion_percentage' => 'integer',
        'completed_at' => 'datetime',
        'auto_reminder' => 'boolean',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
