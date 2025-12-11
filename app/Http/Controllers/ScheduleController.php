<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Ticket;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteMail;
use App\Mail\UserInviteMail;
use App\Models\Attendance;
use App\Models\FoodService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class ScheduleController extends Controller
{
    public function index()
    {
        return view('calendar.event-calendar');
    }

    public function index_list()
    {
        $groups = Group::all();
        $events = Event::with('groups')->get(); // Eager load groups
        return view('calendar.datatable.index', compact('events'));
    }

    public function create_event()
    {
        $groups = Group::where('is_active', 1)->get();
        $users = User::where('activate_status', 'activated')->get();
        $foodServices = FoodService::orderBy('order')->get();
        
        return view('calendar.add', compact('groups', 'users', 'foodServices'));
    }

    public function create(Request $request)
    {
        $item = new Event();
        $item->title = $request->title;
        $item->address = $request->address;
        $item->start = $request->start . ' ' . $request->start_time;
        $item->end = $request->end . ' ' . $request->end_time;
        $item->description = $request->description;
        $item->color = $request->color;
        $item->save();

        return redirect('/event-calendar');
    }

    private function sendNotificationMail($userIds, $event, $sendMailCheck, $groupIds = [])
    {
        try {
            // Remove duplicates from userIds
            $userIds = array_unique($userIds);
            
            // Batch insert attendances first
            $attendanceData = [];
            
            // Fetch all users in batch
            $users = User::whereIn('id', $userIds)->get()->keyBy('id');
            
            // Fetch group users for the selected groups
            $groupUsers = [];
            if (!empty($groupIds)) {
                $groupUsers = GroupUser::whereIn('user_id', $userIds)
                    ->whereIn('group_id', $groupIds)
                    ->get()
                    ->groupBy('user_id');
            }
            
            foreach ($userIds as $userId) {
                $user = $users->get($userId);
                
                if (!$user) {
                    Log::warning("User {$userId} not found, skipping.");
                    continue;
                }
                
                // Check if attendance already exists for this user and event
                $existingAttendance = Attendance::where('event_id', $event->id)
                    ->where('user_id', $userId)
                    ->first();
                
                if ($existingAttendance) {
                    Log::info("Attendance already exists for user {$userId} in event {$event->id}, skipping.");
                    continue;
                }
                
                // Get the first group_id for this user (if they're in multiple groups, pick one)
                $groupId = null;
                if (isset($groupUsers[$userId]) && $groupUsers[$userId]->isNotEmpty()) {
                    $groupId = $groupUsers[$userId]->first()->group_id;
                }
                
                // Prepare attendance data for batch insert
                $attendanceData[] = [
                    'user_id' => $user->id,
                    'group_id' => $groupId,
                    'event_id' => $event->id,
                    'event_name' => $event->title,
                    'time_in' => null,
                    'status' => 'Pending',
                    'email_sent' => ($sendMailCheck == 1) ? false : true,
                    'email_sent_at' => ($sendMailCheck == 1) ? null : now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Batch insert all attendances at once
            if (!empty($attendanceData)) {
                Attendance::insert($attendanceData);
                Log::info('Batch attendance created successfully for ' . count($attendanceData) . ' users');
                
                if ($sendMailCheck == 1) {
                    Log::info('Emails will be sent via cron job. Check logs for progress.');
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error in sendNotificationMail: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function store(Request $request)
    {
        $sendMailCheck = $request->send_mail ?? 0;
        Log::info('Store method - send_mail value:', ['value' => $sendMailCheck]);
        
        // Create a new Event
        $item = new Event();
        $item->title = $request->title;
        $item->address = $request->address;
        $item->start = $request->start;
        $item->end = $request->end;
        $item->description = $request->description;
        $item->color = '#052884';
        $item->save();

        // Attach Groups to Event using the pivot table
        if ($request->has('group_ids') && is_array($request->group_ids)) {
            $groupIds = array_filter($request->group_ids, function($id) {
                return !empty($id);
            });
            
            if (!empty($groupIds)) {
                $item->groups()->attach($groupIds);
                Log::info('Attached groups to event:', ['event_id' => $item->id, 'group_ids' => $groupIds]);
            }
        }

        // Attach Food Services to Event - batch insert
        if ($request->has('food_service_ids') && is_array($request->food_service_ids)) {
            $foodServiceData = [];
            foreach ($request->food_service_ids as $foodServiceId) {
                $quantity = $request->input("food_service_quantities.{$foodServiceId}");
                
                $foodServiceData[] = [
                    'event_id' => $item->id,
                    'food_service_id' => $foodServiceId,
                    'quantity' => $quantity ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            if (!empty($foodServiceData)) {
                DB::table('event_food_services')->insert($foodServiceData);
            }
        }

        // Fetch users from ALL selected groups
        $groupIds = $request->input('group_ids', []);
        $existingUserIds = [];
        
        if (!empty($groupIds)) {
            $existingUserIds = GroupUser::whereIn('group_id', $groupIds)
                ->pluck('user_id')
                ->unique()
                ->toArray();
        }

        // Fetch userIds from the request
        $newUserIds = $request->input('userId', []);

        // Combine and remove duplicates
        $allUserIds = array_unique(array_merge($existingUserIds, $newUserIds));
        
        // IMPORTANT: For large groups, limit email sending or split into batches
        if (count($allUserIds) > 200) {
            Log::warning("Large group detected (" . count($allUserIds) . " users). Consider splitting email sending.");
        }
        
        Log::info('User IDs summary:', [
            'from_groups' => count($existingUserIds),
            'manually_added' => count($newUserIds),
            'total_unique' => count($allUserIds)
        ]);
        
        $event = Event::where('id', $item->id)->first();
        
        if ($allUserIds) {
            $this->sendNotificationMail($allUserIds, $event, $sendMailCheck, $groupIds);
        }

        return redirect()->route('events.index_list')->with('success', 'Event added successfully. Check logs for email sending status.');
    }

    public function edit($id)
    {
        $event = Event::with(['foodServices', 'groups'])->findOrFail($id);
        $groups = Group::all();
        $users = User::all();
        
        // Get all food services
        $foodServices = FoodService::orderBy('order')->get();
        
        // Get selected food service IDs
        $selectedFoodServices = $event->foodServices->pluck('id')->toArray();
        
        // Get food service quantities
        $foodServiceQuantities = [];
        foreach ($event->foodServices as $service) {
            $foodServiceQuantities[$service->id] = $service->pivot->quantity;
        }
        
        // Get selected group IDs from the relationship
        $group_ids = $event->groups->pluck('id')->toArray();
        
        Log::info('Edit event - Group IDs:', ['event_id' => $id, 'group_ids' => $group_ids]);
        
        // Get user_ids from Attendance table
        $attendanceUserIds = Attendance::where('event_id', $id)->pluck('user_id')->toArray();
        
        // Get user IDs that belong to the selected groups
        $groupUserIds = [];
        if (!empty($group_ids)) {
            $groupUserIds = GroupUser::whereIn('group_id', $group_ids)
                ->pluck('user_id')
                ->unique()
                ->toArray();
            
            Log::info('Users from selected groups:', ['count' => count($groupUserIds)]);
        }
        
        // Only show users in "Additional Members" who are NOT part of the selected groups
        $user_ids = array_diff($attendanceUserIds, $groupUserIds);
        
        Log::info('Final user_ids for Additional Members:', ['count' => count($user_ids)]);
        
        return view('calendar.event-list.edit', compact(
            'event', 
            'groups', 
            'users', 
            'foodServices',
            'selectedFoodServices',
            'foodServiceQuantities',
            'group_ids', 
            'user_ids'
        ));
    }

    public function update_event(Request $request, $id)
    {
        Log::info('Update event:', ['event_id' => $id, 'request_data' => $request->all()]);
        $sendMailCheck = $request->send_mail ?? 0;

        // Find the event by ID
        $event = Event::findOrFail($id);

        // Update the event attributes
        $event->title = $request->title;
        $event->address = $request->address;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->description = $request->description;
        $event->status = $request->status;
        $event->save();

        // Sync Groups with Event using the pivot table
        if ($request->has('group_ids') && is_array($request->group_ids)) {
            $groupIds = array_filter($request->group_ids, function($id) {
                return !empty($id);
            });
            
            $event->groups()->sync($groupIds);
            Log::info('Synced groups for event:', ['event_id' => $id, 'group_ids' => $groupIds]);
        } else {
            // No groups selected, detach all
            $event->groups()->detach();
            Log::info('Detached all groups from event:', ['event_id' => $id]);
        }

        // Update Food Services - batch operation
        DB::table('event_food_services')->where('event_id', $id)->delete();
        
        if ($request->has('food_service_ids') && is_array($request->food_service_ids)) {
            $foodServiceData = [];
            foreach ($request->food_service_ids as $foodServiceId) {
                $quantity = $request->input("food_service_quantities.{$foodServiceId}");
                
                $foodServiceData[] = [
                    'event_id' => $id,
                    'food_service_id' => $foodServiceId,
                    'quantity' => $quantity ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            if (!empty($foodServiceData)) {
                DB::table('event_food_services')->insert($foodServiceData);
            }
        }

        // Check if the status is 0, and update related tickets
        if ($request->status == 0) {
            Ticket::where('event_id', $id)->update(['status' => 'Inactive']);
        }

        if ($request->status == 1) {
            Ticket::where('event_id', $id)->update(['status' => 'Active']);
        }

        // Fetch users from ALL selected groups
        $groupIds = $request->input('group_ids', []);
        $existingUserIdsFromGroups = [];
        
        if (!empty($groupIds)) {
            $existingUserIdsFromGroups = GroupUser::whereIn('group_id', $groupIds)
                ->pluck('user_id')
                ->unique()
                ->toArray();
        }

        // Fetch userIds from the request (manually added users)
        $newUserIds = $request->input('userId', []);

        // Combine both group users and manually selected users, remove duplicates
        $allNewUserIds = array_unique(array_merge($existingUserIdsFromGroups, $newUserIds));

        // Fetch existing user IDs from Attendance for the event
        $existingAttendanceUserIds = Attendance::where('event_id', $id)->pluck('user_id')->toArray();

        // Find user IDs to be removed
        $removedUserIds = array_diff($existingAttendanceUserIds, $allNewUserIds);

        // Find new user IDs to be added (only those not already in attendance)
        $addedUserIds = array_diff($allNewUserIds, $existingAttendanceUserIds);

        Log::info('Update summary:', [
            'existing_attendance' => count($existingAttendanceUserIds),
            'new_total' => count($allNewUserIds),
            'to_remove' => count($removedUserIds),
            'to_add' => count($addedUserIds)
        ]);

        // Remove the user IDs from the Attendance table - batch operation
        if (!empty($removedUserIds)) {
            Attendance::where('event_id', $id)
                ->whereIn('user_id', $removedUserIds)
                ->delete();
            Log::info("Removed " . count($removedUserIds) . " users from event {$id}");
        }

        // Send notification email for new user IDs added
        if (!empty($addedUserIds)) {
            if (count($addedUserIds) > 200) {
                Log::warning("Large update detected (" . count($addedUserIds) . " new users).");
            }
            $this->sendNotificationMail($addedUserIds, $event, $sendMailCheck, $groupIds);
            Log::info("Added " . count($addedUserIds) . " new users to event {$id}");
        }

        return redirect()->route('events.index_list')->with('success', 'Event updated successfully. Check logs for email sending status.');
    }

    public function getEvents()
    {
        $schedules = Event::with('groups')->get(); // Eager load groups
        return response()->json($schedules);
    }

    public function deleteEvent($id)
    {
        $schedule = Event::findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }

    public function update(Request $request, $id)
    {
        $schedule = Event::findOrFail($id);

        $schedule->update([
            'start' => $request->input('start_date'),
            'end' => $request->input('end_date'),
        ]);

        Log::info($request->input('start_date'));
        Log::info($request->input('end_date'));

        return response()->json(['message' => 'Event moved successfully']);
    }

    public function resize(Request $request, $id)
    {
        $schedule = Event::findOrFail($id);

        $newEndDate = Carbon::parse($request->input('end_date'))->setTimezone('UTC');
        $schedule->update(['end' => $newEndDate]);

        return response()->json(['message' => 'Event resized successfully.']);
    }

    public function search(Request $request)
    {
        $searchKeywords = $request->input('title');

        $matchingEvents = Event::where('title', 'like', '%' . $searchKeywords . '%')->get();

        return response()->json($matchingEvents);
    }
}