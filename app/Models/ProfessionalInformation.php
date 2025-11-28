<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'company', 'location', 'email', 'mobile', 'role'
    ];

    // User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
