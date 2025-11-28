<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_id',
        'invoice_no',
        'xendit_id',
        'description',
        'status',
        'amount',
        'paid_amount',
        'currency',
        'payer_email',
        'payment_method',
        'bank_code',
        'payment_channel',
        'payment_id',
        'payment_destination',
        'expiry_date',
        'paid_at',
        'xendit_invoice_no',
        'invoice_url',
        'quantity',
        'first_name',
        'last_name',
        'email',
        'attendees'
        
    ];

    // Relation with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation with Ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    public function ticketGuests()
    {
        return $this->hasMany(TicketGuest::class);
    }
    
}
