<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EventGroup extends Pivot
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_groups';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'group_id',
    ];

    /**
     * Get the event that owns this pivot.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the group that owns this pivot.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}