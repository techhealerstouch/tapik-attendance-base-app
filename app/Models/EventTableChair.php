<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTableChair extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_table_id',
        'user_id',
        'chair_number'
    ];

    public function eventTable()
    {
        return $this->belongsTo(EventTable::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsOccupiedAttribute()
    {
        return !is_null($this->user_id);
    }
}