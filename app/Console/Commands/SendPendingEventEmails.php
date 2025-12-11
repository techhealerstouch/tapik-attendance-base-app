<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserInviteMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendPendingEventEmails extends Command
{
    protected $signature = 'emails:send-pending';
    protected $description = 'Send pending event invitation emails in batches';

    public function handle()
    {
        $startTime = time();
        $maxExecutionTime = 25; // Stay under 30 second limit
        
        // Get attendances that haven't been emailed yet (only where email_sent = false AND email_sent_at is NULL)
        $pendingAttendances = Attendance::where('email_sent', false)
            ->whereNull('email_sent_at') // This ensures we only get truly pending emails
            ->with(['event', 'user'])
            ->limit(50) // Process 50 at a time
            ->get();
        
        if ($pendingAttendances->isEmpty()) {
            $this->info('No pending emails to send.');
            Log::info('No pending event invitation emails.');
            return 0;
        }
        
        $emailsSent = 0;
        $emailsFailed = 0;
        
        foreach ($pendingAttendances as $attendance) {
            // Check timeout
            if ((time() - $startTime) > $maxExecutionTime) {
                $this->warn("Stopping due to time limit. Sent: {$emailsSent}");
                break;
            }
            
            $user = $attendance->user;
            $event = $attendance->event;
            
            if (!$user || !$event || !$user->email) {
                $attendance->email_sent = true;
                $attendance->email_sent_at = now();
                $attendance->save();
                continue;
            }
            
            try {
                $url = url('/ticket');
                $eventName = $event->title;
                $eventStart = Carbon::parse($event->start)->format('F j, Y g:i A');
                $eventEnd = $event->end;
                $eventAddress = $event->address;
                
                Mail::to($user->email)->send(new UserInviteMail(
                    $eventName,
                    $user->name,
                    $eventStart,
                    $eventEnd,
                    $eventAddress,
                    $url
                ));
                
                // Mark as sent
                $attendance->email_sent = true;
                $attendance->email_sent_at = now();
                $attendance->save();
                
                $emailsSent++;
                $this->info("Email sent to: {$user->email}");
                
                // Small delay
                usleep(100000); // 0.1 second
                
            } catch (\Throwable $th) {
                $emailsFailed++;
                Log::error("Failed to send email to {$user->email}: " . $th->getMessage());
                
                // Mark as failed but don't retry immediately
                $attendance->email_sent = true;
                $attendance->email_sent_at = now();
                $attendance->save();
            }
        }
        
        $this->info("Completed. Sent: {$emailsSent}, Failed: {$emailsFailed}");
        Log::info("Pending emails batch completed. Sent: {$emailsSent}, Failed: {$emailsFailed}");
        
        return 0;
    }
}