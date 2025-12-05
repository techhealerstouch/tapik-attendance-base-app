<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\TicketGuest;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index()
    {
        // Get all event titles
        $eventTitles = Event::pluck('title');
        
        // Get all attendances with user relationship
        $attendances = Attendance::with('user')->get();
        
        // Get all activated users
        $users = User::where('activate_status', 'activated')->get();
    
        return view('calendar.attendance', [
            'eventTitles' => $eventTitles,
            'users' => $users,
            'attendances' => $attendances
        ]);
    }

    public function index_live()
    {
        $eventTitles = Event::select('id', 'title')->get();
    
        return view('tickets.admin.live-preview', [
            'events' => $eventTitles,
        ]);
    }

    public function get_live_user(Request $request, $eventId)
{
    try {
        // Get the limit from request, default to 10
        $limit = $request->input('limit', 10);
        
        // Validate limit to prevent abuse
        $limit = in_array($limit, [10, 100, 500]) ? $limit : 10;
        
        if ($eventId === 'all') {
            $attendances = Attendance::with('user', 'event')
                ->where('status', 'Present')
                ->orderBy('time_in', 'desc')
                ->limit($limit)
                ->get();

            $ticketGuests = TicketGuest::with('ticket.event')
                ->where('is_scanned', 1)
                ->orderBy('updated_at', 'desc')
                ->limit($limit)
                ->get();
                
            // Get total count for all events
            $totalMembers = Attendance::where('status', 'Present')->count();
        } else {
            $attendances = Attendance::with('user', 'event')
                ->where('status', 'Present')
                ->where('event_id', $eventId)
                ->orderBy('time_in', 'desc')
                ->limit($limit)
                ->get();

            $ticket = Ticket::where('event_id', $eventId)->first();

            if ($ticket) {
                $ticketGuests = TicketGuest::with('ticket.event')
                    ->where('is_scanned', 1)
                    ->where('ticket_id', $ticket->id)
                    ->orderBy('updated_at', 'desc')
                    ->limit($limit)
                    ->get();
            } else {
                $ticketGuests = collect(); // Empty collection
            }
            
            // Get total count for this specific event
            $totalMembers = Attendance::where('event_id', $eventId)
                ->where('status', 'Present')
                ->count();
        }

        return response()->json([
            'attendances' => $attendances,
            'ticketGuests' => $ticketGuests,
            'totalMembers' => $totalMembers,
            'limit' => $limit
        ]);

    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
}

    /**
     * Fetch attendance records for a specific event
     * Updated to accept both event title and event ID
     */
    public function fetch(Request $request)
    {
        $selectedEvent = $request->input('event');
        
        // Try to find by event title first (backward compatibility)
        $query = Attendance::with('user', 'event');
        
        // Check if it's numeric (event ID) or string (event title)
        if (is_numeric($selectedEvent)) {
            $query->where('event_id', $selectedEvent);
        } else {
            $query->where('event_name', $selectedEvent);
        }
        
        $attendances = $query->orderBy('time_in', 'desc')->get();
        
        // Add formatted data
        $attendances = $attendances->map(function ($attendance) {
            return [
                'id' => $attendance->id,
                'user' => $attendance->user ? [
                    'id' => $attendance->user->id,
                    'name' => $attendance->user->name,
                    'email' => $attendance->user->email,
                ] : null,
                'event_name' => $attendance->event_name,
                'event_id' => $attendance->event_id,
                'time_in' => $attendance->time_in,
                'rep_by' => $attendance->rep_by,
                'status' => $attendance->status,
            ];
        });
        
        return response()->json($attendances);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:attendances,id',
            'event' => 'required|string|max:255',
            'status' => 'required|string|in:Present,Absent,Pending',
        ]);

        $event = Event::where('title', $validated['event'])->first();

        if (!$event) {
            return redirect()->back()->with('error', 'Event not found.');
        }

        $attendance = Attendance::findOrFail($validated['id']);
        $attendance->event_id = $event->id;
        $attendance->event_name = $validated['event'];
        $attendance->time_in = now();
        $attendance->rep_by = $request->rep_by;
        $attendance->status = $validated['status'];
        $attendance->save();

        return redirect()->back()->with('success', 'Attendance updated successfully!');
    }

    public function store(Request $request)
    {
        // Find the user by ID
        $user = User::where('id', $request->userId)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Find the event by title
        $event = Event::where('title', $request->add_event)->first();
        if (!$event) {
            return redirect()->back()->with('error', 'Event not found.');
        }

        // Check if attendance already exists
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'Attendance record already exists for this user and event.');
        }

        // Create attendance record
        Attendance::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'event_name' => $request->add_event,
            'time_in' => Carbon::now(),
            'rep_by' => $request->add_rep_by ?? null,
            'status' => $request->add_status
        ]);

        return redirect()->back()->with('success', 'Attendance recorded successfully.');
    }

    public function attendance_input(Request $request)
    {
        $event = $request->event;
        
        $eventDetails = Event::where('title', $event)->first();

        if (!$eventDetails) {
            return redirect()->back()->with('error', 'Event not found.');
        }

        $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $eventDetails->start);
        $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $eventDetails->end);

        if ($startDate === false) {
            return redirect()->back()->with('error', 'Invalid event start date format.');
        }

        if ($endDate === false) {
            return redirect()->back()->with('error', 'Invalid event end date format.');
        }

        $formattedStart = $startDate->format('F d, Y');
        $formattedEnd = $endDate->format('F d, Y');

        $data = [
            'event' => $event,
            'event_id' => $eventDetails->id,
            'start' => $formattedStart,
            'end' => $formattedEnd,
            'address' => $eventDetails->address,
        ];

        return view('calendar.attendance-input', $data);
    }

    public function deleteAttendance($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attendance deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attendance: ' . $e->getMessage()
            ], 500);
        }
    }
}