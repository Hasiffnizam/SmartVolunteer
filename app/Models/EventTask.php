<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'slots_total',
        'slots_filled',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
