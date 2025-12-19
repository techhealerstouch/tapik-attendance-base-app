<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\TicketGuest;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Event;
use App\Models\EventTable;
use App\Models\EventTableChair;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
                // Get present attendances
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

                // Get total count of ALL attendance records (not just Present)
                $totalMembers = Attendance::count();
            } else {
                // Get present attendances for specific event
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

                // Get total count of ALL attendance records for this event (not just Present)
                $totalMembers = Attendance::where('event_id', $eventId)->count();
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

    public function scanProcess(Request $request)
    {
        try {
            $identifier = $request->input('identifier');
            $eventId = $request->input('event_id');
            $scanMode = $request->input('scan_mode', 'rfid'); // Get scan mode from request

            // Store scan mode in session
            session(['last_scan_mode' => $scanMode]);

            // Find event
            $event = Event::find($eventId);
            if (!$event) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Event not found.',
                    'redirect' => false
                ]);
            }

            // Check if it's a URL
            if (filter_var($identifier, FILTER_VALIDATE_URL)) {
                $userCode = basename($identifier);
                $user = User::where('activate_code', $userCode)
                    ->orWhere('id', $userCode)
                    ->first();

                if (!$user) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User not found. Please contact admin/helpdesk.',
                        'redirect' => true
                    ]);
                }

                $attendance = Attendance::where('user_id', $user->id)
                    ->where('event_id', $event->id)
                    ->first();

                if (!$attendance) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are not part of the event. Please contact support.',
                        'redirect' => true
                    ]);
                }

                if ($attendance->status === 'Present') {
                    $assignedChair = EventTableChair::whereHas('eventTable', function ($query) use ($event) {
                        $query->where('event_id', $event->id);
                    })->where('user_id', $user->id)->first();

                    $errorData = [
                        'status' => 'error',
                        'message' => 'You have already submitted attendance for this event.',
                        'redirect' => true
                    ];

                    if ($assignedChair) {
                        $assignedChair->load('eventTable');
                        $errorData['data'] = [
                            'has_seat' => true,
                            'table_name' => $assignedChair->eventTable->table_name,
                            'chair_number' => $assignedChair->chair_number
                        ];
                    }

                    return response()->json($errorData);
                }

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'user_id' => $user->id,
                        'attendance_id' => $attendance->id,
                        'representative' => $user->representative,
                        'user_name' => $user->name
                    ]
                ]);
            }
            // Check if it's a ticket
            elseif (substr($identifier, 0, 3) === 'TCK') {
                $ticketGuest = TicketGuest::where('ticket_no', $identifier)->first();

                if (!$ticketGuest) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Ticket not found.',
                        'redirect' => true
                    ]);
                }

                $ticketList = Ticket::where('id', $ticketGuest->ticket_id)->first();

                if ($ticketList->event_id !== (int) $event->id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Your ticket is not part of this event.',
                        'redirect' => true
                    ]);
                }

                if ($ticketGuest->is_scanned) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are already checked in.',
                        'redirect' => true
                    ]);
                }

                // Mark ticket as scanned
                $ticketGuest->is_scanned = 1;
                $ticketGuest->save();

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'type' => 'ticket',
                        'message' => 'Ticket scanned successfully!',
                        'redirect' => true
                    ]
                ]);
            }
            // Regular RFID
            else {
                $user = User::where('rfid_no', $identifier)->first();

                if (!$user) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User not found. Please contact admin/helpdesk.',
                        'redirect' => true
                    ]);
                }

                $attendance = Attendance::where('user_id', $user->id)
                    ->where('event_id', $event->id)
                    ->first();

                if (!$attendance) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are not part of the event. Please contact support.',
                        'redirect' => true
                    ]);
                }

                if ($attendance->status === 'Present') {
                    $assignedChair = EventTableChair::whereHas('eventTable', function ($query) use ($event) {
                        $query->where('event_id', $event->id);
                    })->where('user_id', $user->id)->first();

                    $errorData = [
                        'status' => 'error',
                        'message' => 'You have already submitted attendance for this event.',
                        'redirect' => true
                    ];

                    if ($assignedChair) {
                        $assignedChair->load('eventTable');
                        $errorData['data'] = [
                            'has_seat' => true,
                            'table_name' => $assignedChair->eventTable->table_name,
                            'chair_number' => $assignedChair->chair_number
                        ];
                    }

                    return response()->json($errorData);
                }

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'user_id' => $user->id,
                        'attendance_id' => $attendance->id,
                        'representative' => $user->representative,
                        'user_name' => $user->name
                    ]
                ]);
            }
        } catch (\Throwable $th) {
            Log::error('Error in scanProcess: ' . $th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again.',
                'redirect' => true
            ]);
        }
    }

    /**
     * Automatically assign an available chair to a user
     * This method uses pessimistic locking to prevent race conditions when multiple
     * users scan simultaneously. Each transaction locks the chair row before checking
     * availability, ensuring no two users can be assigned the same chair.
     */
    private function assignChair($eventId, $userId)
    {
        try {
            // Check if user already has a chair assigned for this event
            $existingChair = EventTableChair::whereHas('eventTable', function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            })->where('user_id', $userId)->first();

            if ($existingChair) {
                // User already has a chair, return it
                $existingChair->load('eventTable');
                return [
                    'success' => true,
                    'table_name' => $existingChair->eventTable->table_name,
                    'chair_number' => $existingChair->chair_number
                ];
            }

            // Find an available chair using a database transaction with row-level locking
            // Skip tables with manual_assignment = true
            $availableChair = DB::transaction(function () use ($eventId, $userId) {
                // Get all tables for this event where manual_assignment is FALSE, ordered by priority
                $tables = EventTable::where('event_id', $eventId)
                    ->where('manual_assignment', false) // SKIP MANUAL ASSIGNMENT TABLES
                    ->orderBy('order')
                    ->get();

                foreach ($tables as $table) {
                    // CRITICAL: lockForUpdate() creates a pessimistic lock
                    $chair = EventTableChair::where('event_table_id', $table->id)
                        ->whereNull('user_id')
                        ->orderBy('chair_number')
                        ->lockForUpdate()
                        ->first();

                    if ($chair) {
                        // At this point, we have an exclusive lock on this chair
                        $chair->user_id = $userId;
                        $chair->save();

                        // Return immediately after successful assignment
                        return [
                            'success' => true,
                            'table_name' => $table->table_name,
                            'chair_number' => $chair->chair_number
                        ];
                    }
                    // If no chair found in this table, continue to next table
                }

                // No available chairs found in any non-manual table
                return [
                    'success' => false,
                    'message' => 'No available seats at this time.'
                ];
            });

            return $availableChair;
        } catch (\Throwable $th) {
            Log::error('Error in assignChair: ' . $th->getMessage());
            return [
                'success' => false,
                'message' => 'Error assigning seat.'
            ];
        }
    }

    /**
     * Submit attendance with representative and automatic chair assignment
     */
    public function submitAttendance(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $attendanceId = $request->input('attendance_id');
            $representative = $request->input('representative');
            $updateUserRep = $request->input('update_user_rep', 0);
            $enableRepPrompt = $request->input('enable_rep_prompt', 0); // ADD THIS

            $attendance = Attendance::find($attendanceId);

            if (!$attendance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Attendance record not found.',
                    'redirect' => true
                ]);
            }

            $user = User::find($userId);
            $event = Event::find($attendance->event_id);
            $chairAssignment = $this->assignChair($attendance->event_id, $userId);

            $attendance->time_in = now();
            $attendance->status = 'Present';
            $attendance->rep_by = $representative;
            $attendance->save();

            if ($updateUserRep && $user) {
                $user->representative = $representative;
                $user->save();
            }

            $responseData = [
                'status' => 'success',
                'message' => 'Attendance marked successfully!',
                'redirect' => true,
                'data' => [
                    'name' => $user ? $user->name : null,
                    'event' => $event ? $event->title : $attendance->event_name,
                    'has_seat' => $chairAssignment['success'],
                    'enable_rep_prompt' => $enableRepPrompt // ADD THIS
                ]
            ];

            if ($chairAssignment['success']) {
                $responseData['data']['table_name'] = $chairAssignment['table_name'];
                $responseData['data']['chair_number'] = $chairAssignment['chair_number'];
            }

            return response()->json($responseData);
        } catch (\Throwable $th) {
            Log::error('Error in submitAttendance: ' . $th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error submitting attendance. Please try again.',
                'redirect' => true
            ]);
        }
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

        // Get last scan mode from session, default to 'rfid'
        $lastScanMode = session('last_scan_mode', 'rfid');

        $data = [
            'event' => $event,
            'event_id' => $eventDetails->id,
            'start' => $formattedStart,
            'end' => $formattedEnd,
            'address' => $eventDetails->address,
            'enable_rep_prompt' => $request->input('enable_rep_prompt', 0),
            'last_scan_mode' => $lastScanMode, // Pass to view
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

    public function showSuccess(Request $request)
    {
        $name = $request->input('name');
        $event = $request->input('event');
        $tableName = $request->input('table_name');
        $chairNumber = $request->input('chair_number');
        $hasSeat = $request->input('has_seat', false);
        $enableRepPrompt = $request->input('enable_rep_prompt', 0); // ADD THIS

        return view('calendar.attendance-success')->with([
            'name' => $name,
            'event' => $event,
            'table_name' => $tableName,
            'chair_number' => $chairNumber,
            'has_seat' => $hasSeat,
            'enable_rep_prompt' => $enableRepPrompt // ADD THIS
        ]);
    }

    // Update showError method
    public function showError(Request $request)
    {
        $error = $request->input('error');
        $event = $request->input('event');
        $tableName = $request->input('table_name');
        $chairNumber = $request->input('chair_number');
        $hasSeat = $request->input('has_seat', false);
        $enableRepPrompt = $request->input('enable_rep_prompt', 0); // ADD THIS

        return view('calendar.attendance-error')->with([
            'error' => $error,
            'event' => $event,
            'table_name' => $tableName,
            'chair_number' => $chairNumber,
            'has_seat' => $hasSeat,
            'enable_rep_prompt' => $enableRepPrompt // ADD THIS
        ]);
    }
}
