<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Correspondence extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'conference_id',
        'member_id',
        'created_by',
        'direction',
        'category',
        'workflow_group',
        'ref_number',
        'correspondence_date',
        'recipient_entity',
        'sender_entity',
        'subject',
        'content',
        'header',
        'file_path',
        'response_received',
        'response_date',
        'status',
        'priority',
        'requires_follow_up',
        'follow_up_at',
        'last_of_type',
        'notes',
    ];

    protected $casts = [
        'header' => 'array',
        'correspondence_date' => 'date',
        'response_date' => 'date',
        'follow_up_at' => 'datetime',
        'response_received' => 'boolean',
        'requires_follow_up' => 'boolean',
        'last_of_type' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Correspondence $correspondence) {
            // Auto-update last_of_type when creating new correspondence
            if ($correspondence->category) {
                static::where('category', $correspondence->category)
                    ->update(['last_of_type' => false]);
                $correspondence->last_of_type = true;
            }
        });
    }

    // Relationships
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeLastOfType($query, string $category)
    {
        return $query->where('category', $category)
            ->where('last_of_type', true);
    }

    public function scopePendingFollowUp($query)
    {
        return $query->where('requires_follow_up', true)
            ->where('follow_up_at', '<=', now())
            ->whereNotIn('status', ['replied', 'archived']);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByWorkflowGroup($query, string $workflowGroup)
    {
        return $query->where('workflow_group', $workflowGroup);
    }

    // Helper methods
    public static function getLastContentForCategory(string $category): ?array
    {
        $last = static::where('category', $category)
            ->where('last_of_type', true)
            ->first();

        if (!$last) {
            return null;
        }

        return [
            'content' => $last->content,
            'header' => $last->header,
            'sender_entity' => $last->sender_entity,
        ];
    }

    public function generateRefNumber(): string
    {
        $prefix = match ($this->category) {
            'invitation' => 'INV',
            'member_consultation' => 'MC',
            'research' => 'RES',
            'attendance' => 'ATT',
            'logistics' => 'LOG',
            'finance' => 'FIN',
            'royal_court' => 'RC',
            'diplomatic' => 'DIP',
            'security' => 'SEC',
            'press' => 'PRS',
            'membership' => 'MBR',
            'thanks' => 'THX',
            default => 'GEN',
        };

        $year = now()->year;
        $count = static::whereYear('created_at', $year)
            ->where('category', $this->category)
            ->count() + 1;

        return sprintf('%s-%d-%04d', $prefix, $year, $count);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->useDisk('public');

        $this->addMediaCollection('pdf')
            ->singleFile()
            ->useDisk('public');
    }
}
