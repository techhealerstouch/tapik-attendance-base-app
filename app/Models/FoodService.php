<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'start_time', 'end_time', 'is_active', 'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_food_services')
            ->withPivot('quantity', 'serving_start', 'serving_end')
            ->withTimestamps();
    }

    public function claims()
    {
        return $this->hasMany(FoodServiceClaim::class);
    }
}