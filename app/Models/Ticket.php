<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_code',
        'description',
        'start_date',
        'end_date',
        'name',
        'price',
        'status'
    ];

    // Define relationship with Event model
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function ticketGuests()
    {
        return $this->hasMany(TicketGuest::class);
    }
}
