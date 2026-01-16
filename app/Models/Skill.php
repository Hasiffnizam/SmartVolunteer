<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;

class Skill extends Model
{
    protected $fillable = ['name', 'slug'];

    /**
     * Events that require this skill
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_skill');
    }
}
