<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFoodService extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'food_service_id', 'quantity', 'serving_start', 'serving_end'
    ];

    protected $casts = [
        'serving_start' => 'datetime:H:i',
        'serving_end' => 'datetime:H:i',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function foodService()
    {
        return $this->belongsTo(FoodService::class);
    }

    public function claims()
    {
        return $this->hasMany(FoodServiceClaim::class, 'food_service_id', 'food_service_id')
            ->where('event_id', $this->event_id);
    }

    public function getClaimedCountAttribute()
    {
        return $this->claims()->count();
    }

    public function getRemainingQuantityAttribute()
    {
        if (!$this->quantity) return null;
        return max(0, $this->quantity - $this->claimed_count);
    }
}