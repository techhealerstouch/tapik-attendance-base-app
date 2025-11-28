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


class ScheduleController extends Controller
{
    //
    public function index()
    {
        return view('calendar.event-calendar');
    }

    public function index_list()
    {
        $groups = Group::all();
        $events = Event::all();
        return view('calendar.datatable.index', compact('events'));
    }

public function create_event()
{
        $groups = Group::where('is_active', 1)->get();  // Fetch only active groups
        $users = User::where('activate_status', 'activated')->get();  // Fetch all users
    
    // ADD THIS LINE
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

    private function sendNotificationMail($userIds, $event, $sendMailCheck)
    {
        try {
            foreach ($userIds as $userId) {
                $user = User::where('id', $userId)->first();
                if (!$user) {
                    Log::warning("User {$userId} not found, skipping.");
                    continue;
                }
                // Check if the user has a group_id
                $groupUser = GroupUser::where('user_id', $userId)->first();
                $groupId = $groupUser ? $groupUser->group_id : null;
                $url = url('/ticket');
                $eventName = $event->title;
                $eventStart = Carbon::parse($event->start)->format('F j, Y g:i A');
                $eventEnd = $event->end;
                $eventAddress = $event->address;
                $name = $user->name;
                $toEmail = $user->email;

                if (!$toEmail) {
                    Log::warning("User {$user->id} has no email, skipping mail.");
                    continue;
                }

                try {
                    Attendance::create([
                        'user_id' => $user->id,
                        'group_id' => $groupId ?? null,
                        'event_id' => $event->id,
                        'event_name' => $event->title,
                        'time_in' => null,
                        'status' => 'Pending'
                    ]);
                    Log::info('Attendance created successfully');
                } catch (\Exception $e) {
                    Log::error('Error creating attendance: ' . $e->getMessage());
                    // Optionally continue or skip this user
                    continue;
                }

                if ($sendMailCheck == 1) {
                    // $redirectURL = url('/u/' . $user->id);
                    // //$publicURL = url('/' . $user->activate_code);
                    // ini_set('memory_limit', '256M');
                    // $argValues = [0, 0, 0, 0, 0, 0, 'diagonal'];
                    // list($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7) = $argValues;

                    // // Generate QR code
                    // if (extension_loaded('imagick')) {
                    //     $imgSrc = QrCode::format('png')
                    //         ->gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                    //         ->eye('circle')
                    //         ->style('round')
                    //         ->size(300)
                    //         ->generate($redirectURL);
                    //     $imgSrc = base64_encode($imgSrc);
                    //     $imgSrc = 'data:image/png;base64,' . $imgSrc;
                    // } else {
                    //     $imgSrc = QrCode::gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                    //         ->eye('circle')
                    //         ->style('round')
                    //         ->size(300)
                    //         ->generate($redirectURL);
                    //     $imgSrc = base64_encode($imgSrc);
                    //     $imgSrc = 'data:image/svg+xml;base64,' . $imgSrc;
                    // }

                    // $pdfData = [
                    //     'name' => $user->name,
                    //     'event_name' => $eventName,
                    //     'qr_code' => $imgSrc,
                    // ];

                    // $pdf = Pdf::loadView('calendar.pdf.qr-pdf', ['data' => $pdfData]);
                    // $filePath = storage_path('app/public/event_ticket_' . $user->id . '.pdf');
                    // $pdf->save($filePath); // Save each user's PDF separately

                    // Send mail with the correct attachment
                    try {
                        // Mail::to($toEmail)->send(new InviteMail(
                        //     $eventName,
                        //     $name,
                        //     $eventStart,
                        //     $eventEnd,
                        //     $eventAddress,
                        //     $url,
                        //     $filePath, // Attach correct QR PDF
                        //     "QR Code: " . $user->name
                        // ));

                        Log::info("Sending UserInviteMail with the following data:", [
                            'to_email' => $toEmail,
                            'event_name' => $eventName,
                            'recipient_name' => $name,
                            'event_start' => $eventStart,
                            'event_end' => $eventEnd,
                            'event_address' => $eventAddress,
                            'event_url' => $url
                        ]);

                        Mail::to($toEmail)->send(new UserInviteMail(
                            $eventName,
                            $name,
                            $eventStart,
                            $eventEnd,
                            $eventAddress,
                            $url
                        ));
                    } catch (\Throwable $th) {
                        Log::error('Error sending invite email to ' . $toEmail . ': ' . $th->getMessage());
                    }
                }
            }
        } catch (RequestException $e) {
            Log::error('Error creating attendance record: ' . $e->getMessage());
            Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            return ['error' => $e->getMessage()];
        }
    }



public function store(Request $request)
    {
        $sendMailCheck = $request->send_mail ?? 0;
        Log::info($sendMailCheck);
        
        // Create a new Event
        $item = new Event();
        $item->title = $request->title;
        $item->address = $request->address;
        $item->start = $request->start;
        $item->end = $request->end;
        $item->description = $request->description;
        $item->group_id = $request->group_id;
        $item->color = '#052884';
        $item->save();

        // Attach Food Services to Event
        if ($request->has('food_service_ids') && is_array($request->food_service_ids)) {
            foreach ($request->food_service_ids as $foodServiceId) {
                $quantity = $request->input("food_service_quantities.{$foodServiceId}");
                
                DB::table('event_food_services')->insert([
                    'event_id' => $item->id,
                    'food_service_id' => $foodServiceId,
                    'quantity' => $quantity ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Fetch the group_id and existing userIds for the group
        $groupId = $request->input('group_id');
        $existingUserIds = GroupUser::where('group_id', $groupId)->pluck('user_id')->toArray();

        // Fetch userIds from the request
        $newUserIds = $request->input('userId', []);

        // Filter out userIds already in the group
        $userIdsToAdd = array_diff($newUserIds, $existingUserIds);

        // Send notification to all users (existing and newly added)
        $allUserIds = array_merge($existingUserIds, $userIdsToAdd);
        $event = Event::where('id', $item->id)->first();
        if ($allUserIds) {
            $this->sendNotificationMail($allUserIds, $event, $sendMailCheck);
        }

        return redirect()->route('events.index_list')->with('success', 'Event added successfully with food services.');
    }

public function edit($id)
{
    $event = Event::with(['foodServices'])->findOrFail($id);
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
    
    // Get group_id
    $group_id = $event->group_id;
    
    // FIX: Get user_ids from Attendance table instead of $event->users
    $user_ids = Attendance::where('event_id', $id)->pluck('user_id')->toArray();
    
    return view('calendar.event-list.edit', compact(
        'event', 
        'groups', 
        'users', 
        'foodServices',
        'selectedFoodServices',
        'foodServiceQuantities',
        'group_id', 
        'user_ids'
    ));
}

    public function update_event(Request $request, $id)
    {
        Log::info($request->all());
        Log::info($id);
        $sendMailCheck = $request->send_mail ?? 0;
        Log::info($sendMailCheck);

        // Find the event by ID
        $event = Event::findOrFail($id);

        // Update the event attributes
        $event->title = $request->title;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->description = $request->description;
        $event->group_id = $request->group_id;
        $event->status = $request->status;
        $event->save();

        // Update Food Services
        // First, remove all existing food services for this event
        DB::table('event_food_services')->where('event_id', $id)->delete();
        
        // Then, add the new ones
        if ($request->has('food_service_ids') && is_array($request->food_service_ids)) {
            foreach ($request->food_service_ids as $foodServiceId) {
                $quantity = $request->input("food_service_quantities.{$foodServiceId}");
                
                DB::table('event_food_services')->insert([
                    'event_id' => $id,
                    'food_service_id' => $foodServiceId,
                    'quantity' => $quantity ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Check if the status is 0, and update related tickets
        if ($request->status == 0) {
            Ticket::where('event_id', $id)->update(['status' => 'Inactive']);
        }

        if ($request->status == 1) {
            Ticket::where('event_id', $id)->update(['status' => 'Active']);
        }

        // Fetch existing user IDs from Attendance for the event
        $user_ids = Attendance::where('event_id', $id)->pluck('user_id')->toArray();

        // Fetch new user IDs from the request
        $newUserIds = $request->input('userId', []);

        // Find user IDs to be removed
        $removedUserIds = array_diff($user_ids, $newUserIds);

        // Find new user IDs to be added
        $addedUserIds = array_diff($newUserIds, $user_ids);

        // Remove the user IDs from the Attendance table
        if (!empty($removedUserIds)) {
            Attendance::where('event_id', $id)
                ->whereIn('user_id', $removedUserIds)
                ->delete();
        }

        // Send notification email for new user IDs added
        if (!empty($addedUserIds)) {
            $this->sendNotificationMail($addedUserIds, $event, $sendMailCheck);
        }

        return redirect()->route('events.index_list')->with('success', 'Event updated successfully with food services.');
    }


    public function getEvents()
    {
        $schedules = Event::all();
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
