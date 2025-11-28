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
        $eventTitles = Event::pluck('title');
        $attendances = Attendance::with('user')->get();
        $users = User::where('activate_status', 'activated')->get();  // Fetch all users
    
        return view('calendar.attendance', [
            'eventTitles' => $eventTitles,
            'users' => $users,
            'attendances' => $attendances
        ]);
    }

    public function index_live()
    {
        $eventTitles = Event::select('id', 'title')->get();
        // $attendances = Attendance::with('user')->get();
        // $users = User::where('activate_status', 'activated')->get();  // Fetch all users
    
        return view('tickets.admin.live-preview', [
            'events' => $eventTitles,
        ]);
    }

    public function get_live_user($eventId)
    {
        try {
            if ($eventId === 'all') {
                $attendances = Attendance::with('user', 'event')
                    ->where('status', 'Present')
                    ->orderBy('time_in', 'desc')
                    ->limit(6)
                    ->get();
    
                $ticketGuests = TicketGuest::with('ticket.event')
                    ->where('is_scanned', 1)
                    ->orderBy('updated_at', 'desc')
                    ->limit(6)
                    ->get();
            } else {
                $attendances = Attendance::with('user', 'event')
                    ->where('status', 'Present')
                    ->where('event_id', $eventId)
                    ->orderBy('time_in', 'desc')
                    ->limit(6)
                    ->get();
    
                $ticket = Ticket::where('event_id', $eventId)->first();
    
                if ($ticket) {
                    $ticketGuests = TicketGuest::with('ticket.event')
                        ->where('is_scanned', 1)
                        ->where('ticket_id', $ticket->id)
                        ->orderBy('updated_at', 'desc')
                        ->limit(6)
                        ->get();
                } else {
                    $ticketGuests = null;
                }
            }
    
            return response()->json(['attendances' => $attendances ?? null, 'ticketGuests' => $ticketGuests ?? null]);
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    

    public function fetch(Request $request)
    {
        $selectedEvent = $request->input('event');
        $attendances = Attendance::with('user')->where('event_name', $selectedEvent)->get();
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

        // Find the user by email
        $user = User::where('id', $request->userId)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Find the event by title
        $event = Event::where('title', $request->add_event)->first();
        if (!$event) {
            return redirect()->back()->with('error', 'Event not found.');
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
        // Handle the case where no event was found
        return redirect()->back()->with('error', 'Event not found.');
    }

    // Correctly reference the global DateTime class
    $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $eventDetails->start);
    $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $eventDetails->end);

    if ($startDate === false) {
        // Handle the case where date parsing failed
        return redirect()->back()->with('error', 'Invalid event date format.');
    }

    if ($endDate === false) {
        // Handle the case where date parsing failed
        return redirect()->back()->with('error', 'Invalid event date format.');
    }

    // Format the date to 'Month day, Year' format (e.g., May 20, 2024)
    $formattedStart = $startDate->format('F d, Y');
    $formattedEnd = $endDate->format('F d, Y');

    $data = [
        'event' => $event,
        'event_id' => $eventDetails->id,
        'start' => $formattedStart, // Use the formatted start date here
        'end' => $formattedEnd, // Use the formatted start date here
        'address' => $eventDetails->address,
    ];

    return view('calendar.attendance-input', $data);
}

    public function deleteAttendance($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return response()->json(['message' => 'Attendance deleted successfully']);
    }
    
}
