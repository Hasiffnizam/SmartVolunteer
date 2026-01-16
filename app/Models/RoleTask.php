<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoleTask extends Model
{
  protected $fillable = [
    'event_id',
    'title',
    'description',
    'slots',
    'slots_taken',
  ];

  public function event(): BelongsTo
  {
    return $this->belongsTo(Event::class);
  }

  public function registrations(): HasMany
  {
    return $this->hasMany(EventRegistration::class);
  }

  // 🔥 Computed helpers
  public function getSlotsAvailableAttribute(): int
  {
    $total = (int) ($this->slots ?? 0);
    $taken = (int) ($this->slots_taken ?? 0);
    return max(0, $total - $taken);
  }

  public function getIsFullAttribute(): bool
  {
    return $this->slots_available <= 0;
  }
}
