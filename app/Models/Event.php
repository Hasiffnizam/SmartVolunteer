<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'event_date',
        'time_slot',
        'location',
        'cause_id',
        'poster_path',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Admin-created role tasks for this event
     * (Used for slots, role analysis, task performance)
     */
    public function roleTasks(): HasMany
    {
        return $this->hasMany(RoleTask::class);
    }

    /**
     * Volunteer registrations (raw registrations table)
     * Keep this for workflows like approval, status, etc.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Volunteers participating in this event
     * IMPORTANT for reporting & analytics
     *
     * Pivot fields:
     * - role_task_id
     * - attendance_status (present | absent | late)
     * - task_completion (0–100)
     */
    public function volunteers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'event_volunteers',
            'event_id',
            'user_id'
        )->withPivot([
            'role_task_id',
            'attendance_status',
            'task_completion',
        ])->withTimestamps();
    }

    /**
     * Event creator (admin)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Event cause/category
     */
    public function cause(): BelongsTo
    {
        return $this->belongsTo(Cause::class);
    }

    /**
     * Skills required by this event
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(
            Skill::class,
            'event_skill',
            'event_id',
            'skill_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Human-readable time slot label
     */
    public function timeSlotLabel(): string
    {
        return match ($this->time_slot) {
            'morning' => 'Morning (9:00 AM – 12:00 PM)',
            'evening' => 'Evening (2:00 PM – 6:00 PM)',
            'night'   => 'Night (8:00 PM – 11:00 PM)',
            default   => ucfirst((string) $this->time_slot),
        };
    }

    /**
     * Alias for backward compatibility
     * (event_tasks table was removed)
     */
    public function tasks(): HasMany
    {
        return $this->roleTasks();
    }
    
}
