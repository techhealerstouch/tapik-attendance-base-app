<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'address', 'start', 'end', 'status', 'group_id'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function foodServices()
    {
        return $this->belongsToMany(FoodService::class, 'event_food_services')
            ->withPivot('id', 'quantity', 'serving_start', 'serving_end')
            ->withTimestamps()
            ->orderBy('order');
    }

    public function claims()
    {
        return $this->hasMany(FoodServiceClaim::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->withPivot('registered_at', 'status')
            ->withTimestamps();
    }

    /**
     * Get food service status for a specific user
     * 
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getUserFoodServiceStatus($userId)
    {
        $services = $this->foodServices()->get();
        $claims = $this->claims()
            ->where('user_id', $userId)
            ->pluck('food_service_id')
            ->toArray();

        return $services->map(function ($service) use ($claims) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'is_claimed' => in_array($service->id, $claims),
                'quantity' => $service->pivot->quantity,
                'remaining' => $service->pivot->remaining_quantity,
                'serving_start' => $service->pivot->serving_start,
                'serving_end' => $service->pivot->serving_end,
            ];
        });
    }
}