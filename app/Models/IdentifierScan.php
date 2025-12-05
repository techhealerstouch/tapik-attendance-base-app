<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentifierScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'reference_code',
        'scan_count',
        'first_scanned_at',
        'last_scanned_at',
    ];

    protected $casts = [
        'first_scanned_at' => 'datetime',
        'last_scanned_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Increment the scan count
     */
    public function incrementScan()
    {
        $this->increment('scan_count');
        $this->update(['last_scanned_at' => now()]);
    }

    /**
     * Get or create a scan record and increment it
     * Now consolidates all scans for a user in an event, regardless of reference code
     */
    public static function recordScan($eventId, $userId, $referenceCode)
    {
        // Find existing record by event_id and user_id only (ignore reference_code)
        $scan = static::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->first();

        if ($scan) {
            // Record already exists, increment the count
            $scan->incrementScan();
        } else {
            // Create new record
            $scan = static::create([
                'event_id' => $eventId,
                'user_id' => $userId,
                'reference_code' => $referenceCode,
                'scan_count' => 1,
                'first_scanned_at' => now(),
                'last_scanned_at' => now(),
            ]);
        }

        return $scan->fresh();
    }
}