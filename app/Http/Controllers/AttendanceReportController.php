<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use App\Models\IdentifierScan;
use App\Models\EventTableChair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceReportController extends Controller
{
    public function index()
    {
        // Get all events with their details
        $events = Event::select('id', 'title', 'start', 'end')->orderBy('start', 'desc')->get();
        
        return view('calendar.attendance-report', [
            'events' => $events
        ]);
    }

    public function fetch(Request $request)
    {
        $eventId = $request->input('event');
        
        if (!$eventId) {
            return response()->json(['error' => 'No event selected'], 400);
        }

        // Get event details
        $event = Event::find($eventId);
        
        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        // Auto-update pending to absent if event has ended
        $this->autoUpdatePendingToAbsent($eventId, $event->end);

        // Get attendance statistics
        $attendanceStats = Attendance::where('event_id', $eventId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get all attendances for the event
        $attendances = Attendance::with('user')
            ->where('event_id', $eventId)
            ->orderBy('time_in', 'desc')
            ->get();

        // Get seat assignments for the event
        $seatAssignments = EventTableChair::whereHas('eventTable', function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            })
            ->with(['user', 'eventTable'])
            ->whereNotNull('user_id')
            ->orderBy('event_table_id')
            ->orderBy('chair_number')
            ->get();

        // Get identifier scan statistics
        $scanStats = IdentifierScan::where('event_id', $eventId)
            ->select(
                DB::raw('COUNT(*) as total_users_scanned'),
                DB::raw('SUM(scan_count) as total_scans'),
                DB::raw('AVG(scan_count) as avg_scans_per_user'),
                DB::raw('MAX(scan_count) as max_scan_count')
            )
            ->first();

        // Get users with multiple scans
        $multipleScans = IdentifierScan::where('event_id', $eventId)
            ->where('scan_count', '>', 1)
            ->with('user')
            ->orderBy('scan_count', 'desc')
            ->get();

        // Calculate additional metrics
        $presentCount = $attendanceStats['Present'] ?? 0;
        $absentCount = $attendanceStats['Absent'] ?? 0;
        $pendingCount = $attendanceStats['Pending'] ?? 0;
        $totalRegistered = $presentCount + $absentCount + $pendingCount;
        
        $attendanceRate = $totalRegistered > 0 
            ? round(($presentCount / $totalRegistered) * 100, 2) 
            : 0;

        // Get time-based statistics
        $earlyArrivals = Attendance::where('event_id', $eventId)
            ->where('status', 'Present')
            ->where('time_in', '<', $event->start)
            ->count();

        $lateArrivals = Attendance::where('event_id', $eventId)
            ->where('status', 'Present')
            ->where('time_in', '>', $event->start)
            ->count();

        $onTimeArrivals = $presentCount - $earlyArrivals - $lateArrivals;

        // Format attendance data
        $formattedAttendances = $attendances->map(function ($attendance) use ($event) {
            $timeIn = $attendance->time_in ? new \DateTime($attendance->time_in) : null;
            $eventStart = new \DateTime($event->start);
            
            $arrivalStatus = 'N/A';
            $minutesDifference = null;
            
            if ($timeIn && $attendance->status === 'Present') {
                $diff = $eventStart->diff($timeIn);
                $minutesDifference = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
                
                if ($timeIn < $eventStart) {
                    $arrivalStatus = 'Early';
                    $minutesDifference = -$minutesDifference;
                } elseif ($timeIn > $eventStart) {
                    $arrivalStatus = 'Late';
                } else {
                    $arrivalStatus = 'On Time';
                    $minutesDifference = 0;
                }
            }

            return [
                'id' => $attendance->id,
                'user' => $attendance->user ? [
                    'id' => $attendance->user->id,
                    'name' => $attendance->user->name,
                    'email' => $attendance->user->email,
                ] : null,
                'event_name' => $attendance->event_name,
                'time_in' => $attendance->time_in,
                'rep_by' => $attendance->rep_by,
                'status' => $attendance->status,
                'arrival_status' => $arrivalStatus,
                'minutes_difference' => $minutesDifference,
            ];
        });

        // Format seat assignments
        $formattedSeatAssignments = $seatAssignments->map(function ($chair) {
            return [
                'user_name' => $chair->user->name ?? 'N/A',
                'user_email' => $chair->user->email ?? 'N/A',
                'table_name' => $chair->eventTable->table_name ?? 'N/A',
                'chair_number' => $chair->chair_number,
            ];
        });

        // Check if event has ended
        $eventHasEnded = Carbon::parse($event->end)->isPast();

        return response()->json([
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start,
                'end' => $event->end,
                'address' => $event->address,
                'has_ended' => $eventHasEnded,
            ],
            'summary' => [
                'total_registered' => $totalRegistered,
                'present' => $presentCount,
                'absent' => $absentCount,
                'pending' => $pendingCount,
                'attendance_rate' => $attendanceRate,
                'early_arrivals' => $earlyArrivals,
                'on_time_arrivals' => $onTimeArrivals,
                'late_arrivals' => $lateArrivals,
                'total_seats_assigned' => $seatAssignments->count(),
            ],
            'scan_stats' => [
                'total_users_scanned' => $scanStats->total_users_scanned ?? 0,
                'total_scans' => $scanStats->total_scans ?? 0,
                'avg_scans_per_user' => round($scanStats->avg_scans_per_user ?? 0, 2),
                'max_scan_count' => $scanStats->max_scan_count ?? 0,
                'users_with_multiple_scans' => $multipleScans->count(),
            ],
            'multiple_scans' => $multipleScans->map(function ($scan) {
                return [
                    'user_name' => $scan->user->name ?? 'Unknown',
                    'user_email' => $scan->user->email ?? 'N/A',
                    'scan_count' => $scan->scan_count,
                    'first_scan' => $scan->first_scanned_at,
                    'last_scan' => $scan->last_scanned_at,
                ];
            }),
            'attendances' => $formattedAttendances,
            'seat_assignments' => $formattedSeatAssignments,
        ]);
    }

    /**
     * Automatically update pending attendances to absent if event has ended
     */
    private function autoUpdatePendingToAbsent($eventId, $eventEnd)
    {
        $eventEndDate = Carbon::parse($eventEnd);
        $now = Carbon::now();

        // If event has ended, update all pending to absent
        if ($now->greaterThan($eventEndDate)) {
            $updatedCount = Attendance::where('event_id', $eventId)
                ->where('status', 'Pending')
                ->update([
                    'status' => 'Absent',
                    'updated_at' => now()
                ]);

            Log::info("Auto-updated {$updatedCount} pending attendances to absent for event ID: {$eventId}");
        }
    }

    public function export(Request $request)
    {
        $eventId = $request->input('event');
        
        if (!$eventId) {
            return response()->json(['error' => 'No event selected'], 400);
        }

        $event = Event::find($eventId);
        
        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        // Fetch the report data (this will also trigger auto-update)
        $reportData = $this->fetch($request);
        $data = json_decode($reportData->content(), true);

        return response()->json([
            'success' => true,
            'event' => $data['event'],
            'summary' => $data['summary'],
            'scan_stats' => $data['scan_stats'],
            'attendances' => $data['attendances'],
            'multiple_scans' => $data['multiple_scans'],
            'seat_assignments' => $data['seat_assignments'],
        ]);
    }
}