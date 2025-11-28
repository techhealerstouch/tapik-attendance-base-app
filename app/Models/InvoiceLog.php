<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLog extends Model
{

    use HasFactory;

    protected $table = 'invoice_logs';

    protected $fillable = [
        'invoice_id',
        'status',
        'amount',
        'description',
        'logged_at',
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
