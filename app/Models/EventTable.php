<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTable extends Model
{
    protected $fillable = [
        'event_id',
        'table_name',
        'chair_count',
        'order',
        'manual_assignment' // Added
    ];

    protected $casts = [
        'manual_assignment' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function chairs()
    {
        return $this->hasMany(EventTableChair::class);
    }

    public function getAssignedChairsCountAttribute()
    {
        return $this->chairs()->whereNotNull('user_id')->count();
    }

    public function getAvailableChairsCountAttribute()
    {
        return $this->chair_count - $this->assigned_chairs_count;
    }

    /**
     * Check if table has any assigned chairs
     */
    public function hasAssignedChairs()
    {
        return $this->chairs()->whereNotNull('user_id')->exists();
    }
}