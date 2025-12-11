<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        // Add other fillable fields as needed
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Many-to-Many relationship with Events
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_groups')
            ->withTimestamps();
    }

    /**
     * Get all users in this group
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users')
            ->withTimestamps();
    }

    /**
     * Get group users pivot records
     */
    public function groupUsers()
    {
        return $this->hasMany(GroupUser::class);
    }
}