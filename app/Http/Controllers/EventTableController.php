<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTable;
use App\Models\EventTableChair;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

            // Get all attendees for the event
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

            // Get all assigned user IDs in this event
            $assignedUserIds = EventTableChair::whereHas('eventTable', function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            })
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->toArray();

            return response()->json([
                'success' => true,
                'attendees' => $attendees,
                'assignedUserIds' => $assignedUserIds
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
            $eventId = $chair->eventTable->event_id;
            
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
            $existingAssignment = EventTableChair::whereHas('eventTable', function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            })
                ->where('user_id', $validated['user_id'])
                ->where('id', '!=', $chair->id)
                ->first();

            if ($existingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'This attendee is already assigned to another seat in this event. Please unassign them first.'
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

    public function exportTables(Request $request)
    {
        try {
            $eventId = $request->input('event_id');

            if (!$eventId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event ID is required'
                ], 400);
            }

            $event = Event::findOrFail($eventId);
            
            $tables = EventTable::where('event_id', $eventId)
                ->with(['chairs.user'])
                ->orderBy('order')
                ->orderBy('table_name')
                ->get();

            if ($tables->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tables found for this event'
                ], 404);
            }

            // Create new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator("Event Management System")
                ->setTitle("Table Assignments - " . $event->title)
                ->setSubject("Table Assignments");

            $currentRow = 1;

            // Process each table
            foreach ($tables as $table) {
                // Table Header (merged cells)
                $sheet->mergeCells("A{$currentRow}:B{$currentRow}");
                $sheet->setCellValue("A{$currentRow}", $table->table_name);
                
                // Style the table header
                $sheet->getStyle("A{$currentRow}:B{$currentRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F46E5']
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ]
                ]);
                $sheet->getRowDimension($currentRow)->setRowHeight(25);
                
                $currentRow++;

                // Column Headers
                $sheet->setCellValue("A{$currentRow}", 'Name');
                $sheet->setCellValue("B{$currentRow}", 'Status');
                
                // Style column headers
                $sheet->getStyle("A{$currentRow}:B{$currentRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '6366F1']
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    ]
                ]);
                
                $currentRow++;

                // Get assigned chairs only
                $assignedChairs = $table->chairs->filter(function($chair) {
                    return $chair->user_id !== null;
                })->sortBy('chair_number');

                if ($assignedChairs->count() > 0) {
                    foreach ($assignedChairs as $chair) {
                        $userName = $chair->user ? $chair->user->name : 'Unknown';
                        
                        $sheet->setCellValue("A{$currentRow}", $userName);
                        $sheet->setCellValue("B{$currentRow}", ''); // Leave status blank
                        
                        // Style data rows with alternating colors
                        $fillColor = ($currentRow % 2 == 0) ? 'F8FAFC' : 'FFFFFF';
                        $sheet->getStyle("A{$currentRow}:B{$currentRow}")->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $fillColor]
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                    'color' => ['rgb' => 'E2E8F0']
                                ]
                            ]
                        ]);
                        
                        $currentRow++;
                    }
                } else {
                    // No assigned chairs
                    $sheet->setCellValue("A{$currentRow}", 'No assignments');
                    $sheet->mergeCells("A{$currentRow}:B{$currentRow}");
                    $sheet->getStyle("A{$currentRow}")->applyFromArray([
                        'font' => ['italic' => true, 'color' => ['rgb' => '64748B']],
                        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
                    ]);
                    $currentRow++;
                }

                // Add spacing between tables
                $currentRow += 2;
            }

            // Auto-size columns
            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(20);

            // Generate filename
            $fileName = 'table_assignments_' . str_replace(' ', '_', $event->title) . '_' . date('Y-m-d_His') . '.xlsx';
            
            // Save to temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'export_');
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempFile);

            // Return file as download
            return response()->download($tempFile, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Error exporting tables: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting tables: ' . $e->getMessage()
            ], 500);
        }
    }
}