<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketGuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'invoice_id',
        'ticket_no',
        'first_name',
        'last_name',
        'is_scanned'
    ];

     /**
     * Get the ticket associated with the TicketGuest.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the invoice associated with the TicketGuest.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
