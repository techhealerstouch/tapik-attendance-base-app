<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTable;
use App\Models\EventTableChair;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventTableController extends Controller
{
    public function index_list()
    {
        $events = Event::where('status', 1)
            ->orderBy('start', 'desc')
            ->get();

        return view('calendar.events-table.event-tables', compact('events'));
    }

    public function fetch(Request $request)
    {
        try {
            $eventId = $request->input('event_id');

            if (!$eventId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event ID is required'
                ], 400);
            }

            $tables = EventTable::where('event_id', $eventId)
                ->with(['chairs.user'])
                ->orderBy('order')
                ->orderBy('table_name')
                ->get();

            return response()->json([
                'success' => true,
                'tables' => $tables
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching tables: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching tables'
            ], 500);
        }
    }

    public function getEventAttendees(Request $request)
    {
        try {
            $eventId = $request->input('event_id');

            if (!$eventId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event ID is required'
                ], 400);
            }

            $attendees = Attendance::where('event_id', $eventId)
                ->with('user:id,name,email')
                ->get()
                ->map(function ($attendance) {
                    return [
                        'id' => $attendance->user_id,
                        'name' => $attendance->user->name ?? 'Unknown',
                        'email' => $attendance->user->email ?? '',
                        'status' => $attendance->status
                    ];
                })
                ->filter(function ($attendee) {
                    return $attendee['id'] !== null;
                })
                ->values();

            return response()->json([
                'success' => true,
                'attendees' => $attendees
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching attendees: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching attendees'
            ], 500);
        }
    }

    public function assignChair(Request $request)
    {
        try {
            $validated = $request->validate([
                'chair_id' => 'required|exists:event_table_chairs,id',
                'user_id' => 'nullable|exists:users,id'
            ]);

            $chair = EventTableChair::find($validated['chair_id']);
            
            // If user_id is null, we're unassigning the chair
            if ($validated['user_id'] === null) {
                $chair->user_id = null;
                $chair->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Chair unassigned successfully'
                ]);
            }

            // Check if user is already assigned to another chair in the same event
            $eventId = $chair->eventTable->event_id;
            $existingAssignment = EventTableChair::whereHas('eventTable', function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            })->where('user_id', $validated['user_id'])->first();

            if ($existingAssignment && $existingAssignment->id != $chair->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already assigned to another chair in this event'
                ], 422);
            }

            $chair->user_id = $validated['user_id'];
            $chair->save();

            return response()->json([
                'success' => true,
                'message' => 'Chair assigned successfully',
                'chair' => $chair->load('user')
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning chair: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error assigning chair: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeTables(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|exists:events,id',
                'tables' => 'required|array|min:1',
                'tables.*.table_name' => 'required|string|max:255',
                'tables.*.chair_count' => 'required|integer|min:1|max:50',
                'tables.*.manual_assignment' => 'nullable|boolean'
            ]);

            DB::beginTransaction();

            $order = EventTable::where('event_id', $validated['event_id'])->max('order') ?? 0;

            foreach ($validated['tables'] as $tableData) {
                $order++;
                
                $table = EventTable::create([
                    'event_id' => $validated['event_id'],
                    'table_name' => $tableData['table_name'],
                    'chair_count' => $tableData['chair_count'],
                    'manual_assignment' => ($tableData['manual_assignment'] ?? 0) == 1,
                    'order' => $order
                ]);

                // Create chairs for the table
                for ($i = 1; $i <= $tableData['chair_count']; $i++) {
                    EventTableChair::create([
                        'event_table_id' => $table->id,
                        'chair_number' => $i,
                        'user_id' => null
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tables created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating tables: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating tables: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteTable($id)
    {
        try {
            $table = EventTable::findOrFail($id);
            
            // Check if table has any assigned chairs
            if ($table->hasAssignedChairs()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete table with assigned chairs. Please unassign all chairs first.'
                ], 422);
            }

            $table->delete();

            return response()->json([
                'success' => true,
                'message' => 'Table deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting table: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting table'
            ], 500);
        }
    }

    public function updateTable(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'table_name' => 'required|string|max:255',
                'chair_count' => 'required|integer|min:1|max:50',
                'manual_assignment' => 'boolean'
            ]);

            DB::beginTransaction();

            $table = EventTable::findOrFail($id);
            $oldChairCount = $table->chair_count;
            $newChairCount = $validated['chair_count'];

            $table->table_name = $validated['table_name'];
            $table->chair_count = $newChairCount;
            $table->manual_assignment = $validated['manual_assignment'] ?? false;
            $table->save();

            // Adjust chairs if count changed
            if ($newChairCount > $oldChairCount) {
                // Add new chairs
                for ($i = $oldChairCount + 1; $i <= $newChairCount; $i++) {
                    EventTableChair::create([
                        'event_table_id' => $table->id,
                        'chair_number' => $i,
                        'user_id' => null
                    ]);
                }
            } elseif ($newChairCount < $oldChairCount) {
                // Remove excess chairs (starting from the highest chair number)
                EventTableChair::where('event_table_id', $table->id)
                    ->where('chair_number', '>', $newChairCount)
                    ->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Table updated successfully',
                'table' => $table->load('chairs.user')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating table: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating table: ' . $e->getMessage()
            ], 500);
        }
    }
}