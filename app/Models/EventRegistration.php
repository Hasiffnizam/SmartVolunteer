<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
  protected $fillable = [
    'event_id',
    'role_task_id',
    'volunteer_id',
    'joined_at',
    'attendance_status',
    'checked_in_at',
    'check_in_method',
    'task_completion',
    'note',
  ];

  protected $casts = [
    'joined_at' => 'datetime',
  ];

  public function event(): BelongsTo
  {
    return $this->belongsTo(Event::class);
  }

  public function roleTask(): BelongsTo
  {
    return $this->belongsTo(RoleTask::class);
  }

  public function volunteer(): BelongsTo
  {
    return $this->belongsTo(User::class, 'volunteer_id');
  }
}
