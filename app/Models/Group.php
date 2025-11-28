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
        'is_active'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->using(GroupUser::class); // Specify the pivot model
    }
}
