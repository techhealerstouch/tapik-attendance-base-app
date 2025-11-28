<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodServiceClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'event_id', 'food_service_id', 'claimed_at', 
        'claimed_by', 'claim_method', 'notes'
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function foodService()
    {
        return $this->belongsTo(FoodService::class);
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }
}