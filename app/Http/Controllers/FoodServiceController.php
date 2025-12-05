<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\FoodService;
use App\Models\FoodServiceClaim;
use App\Models\EventFoodService;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class FoodServiceController extends Controller
{
    /**
     * Display list of all food services
     */
    public function index()
    {
        $foodServices = FoodService::orderBy('order')->get();
        return view('food-services.index', compact('foodServices'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('food-services.create');
    }

    /**
     * Store new food service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['order'] = $validated['order'] ?? FoodService::max('order') + 1;

        FoodService::create($validated);

        return redirect()->route('food-services.index')
            ->with('success', 'Food service created successfully');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $foodService = FoodService::findOrFail($id);
        return view('food-services.edit', compact('foodService'));
    }

    /**
     * Update food service
     */
    public function update(Request $request, $id)
    {
        $foodService = FoodService::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $foodService->update($validated);

        return redirect()->route('food-services.index')
            ->with('success', 'Food service updated successfully');
    }

    /**
     * Delete food service
     */
    public function destroy($id)
    {
        $foodService = FoodService::findOrFail($id);
        $foodService->delete();

        return redirect()->route('food-services.index')
            ->with('success', 'Food service deleted successfully');
    }

    /**
     * Show claiming interface
     */
    public function claimInterface()
    {
        $events = Event::orderBy('start', 'desc')->get();

        return view('food-services.claim-interface', compact('events'));
    }

    public function claimingPage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('food-service.claim-interface')
                ->with('error', 'Please select a valid event');
        }

        $event = Event::findOrFail($request->event_id);

        return view('food-services.claiming-page', compact('event'));
    }

    /**
     * Check if user has valid attendance for the event
     */
    private function hasValidAttendance($userId, $eventId)
    {
        $attendance = Attendance::where('user_id', $userId)
            ->where('event_id', $eventId)
            ->whereNotNull('time_in')
            ->first();

        // User must have checked in (time_in is not null)
        // Status should be 'present' or similar valid status
        return $attendance !== null && in_array(strtolower($attendance->status), ['present', 'checked in', 'attended']);
    }

    /**
     * Get user's food service status by scanning RFID/QR or manual input
     */
    public function getUserStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'event_id' => 'required|exists:events,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        if (filter_var($request->identifier, FILTER_VALIDATE_URL)) {
            // Extract the user ID from the URL (assuming the user ID is the last segment of the URL)
            $userId = basename($request->identifier);
            Log::info($userId);
            Log::info($request->identifier);

            // Find the user using the extracted user ID
            $user = User::where('activate_code', $userId)
                ->orWhere('id', $userId)
                ->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $attendance = Attendance::where('user_id', $user->id)
                ->where('event_id', $request->event_id)
                ->first();

            if (!$attendance) {
                return response()->json(['error' => 'You are not part of this event. Please contact admin or support.'], 404);
            }
        } else {
            // Find user by activate_code
            Log::info($request->identifier);
            $user = User::where('activate_code', $request->identifier)
                ->orWhere('id', $request->identifier)
                ->first();


            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
        }

        // Check if user is part of the event (registered)
        $attendanceRecord = \App\Models\Attendance::where('user_id', $user->id)
            ->where('event_id', $request->event_id)
            ->first();

        if (!$attendanceRecord) {
            return response()->json([
                'error' => 'User is not part of this event',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'event_registration_required' => true
            ], 403);
        }

        // Check if user has valid attendance
        $hasAttendance = $this->hasValidAttendance($user->id, $request->event_id);

        if (!$hasAttendance) {
            return response()->json([
                'error' => 'User must check in to the event first before claiming food services',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'attendance_required' => true
            ], 403);
        }

        // Record the scan (increment if exists, create if not)
        $scanRecord = \App\Models\IdentifierScan::recordScan(
            $request->event_id,
            $user->id,
            $request->identifier
        );

        // Find event with food services
        $event = Event::with(['foodServices' => function ($query) {
            $query->orderBy('order');
        }])->find($request->event_id);

        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        // Get claimed services for this user
        $claimedServices = FoodServiceClaim::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->pluck('food_service_id')
            ->toArray();

        // Build food services status
        $foodServicesStatus = $event->foodServices->map(function ($service) use ($claimedServices, $event, $user) {
            $isClaimed = in_array($service->id, $claimedServices);
            $eventFoodService = EventFoodService::where('event_id', $event->id)
                ->where('food_service_id', $service->id)
                ->first();

            $claimedCount = FoodServiceClaim::where('event_id', $event->id)
                ->where('food_service_id', $service->id)
                ->count();

            return [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'is_claimed' => $isClaimed,
                'can_claim' => !$isClaimed,
                'quantity' => $eventFoodService->quantity ?? null,
                'claimed_count' => $claimedCount,
                'remaining' => $eventFoodService->quantity ? max(0, $eventFoodService->quantity - $claimedCount) : null,
                'serving_start' => $eventFoodService->serving_start ?? $service->start_time,
                'serving_end' => $eventFoodService->serving_end ?? $service->end_time,
            ];
        });

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rfid_no' => $user->rfid_no,
                'activate_code' => $user->activate_code,
            ],
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start->format('Y-m-d H:i'),
                'end' => $event->end->format('Y-m-d H:i'),
            ],
            'food_services' => $foodServicesStatus,
            'has_attendance' => true,
            'is_registered' => true,
            'scan_info' => [
                'scan_count' => $scanRecord->scan_count,
                'first_scanned_at' => $scanRecord->first_scanned_at->format('Y-m-d H:i:s'),
                'last_scanned_at' => $scanRecord->last_scanned_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Claim a food service
     */
    public function claimService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'event_id' => 'required|exists:events,id',
            'food_service_id' => 'required|exists:food_services,id',
            'claim_method' => 'nullable|in:qr,nfc,manual',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        // CRITICAL: Check if user has valid attendance first
        if (!$this->hasValidAttendance($request->user_id, $request->event_id)) {
            return response()->json([
                'error' => 'User must check in to the event first before claiming food services',
                'attendance_required' => true
            ], 403);
        }

        // Verify food service is available for this event
        $eventFoodService = EventFoodService::where('event_id', $request->event_id)
            ->where('food_service_id', $request->food_service_id)
            ->first();

        if (!$eventFoodService) {
            return response()->json(['error' => 'Food service not available for this event'], 400);
        }

        // Check if already claimed
        $existingClaim = FoodServiceClaim::where('user_id', $request->user_id)
            ->where('event_id', $request->event_id)
            ->where('food_service_id', $request->food_service_id)
            ->first();

        if ($existingClaim) {
            return response()->json([
                'error' => 'Food service already claimed',
                'claimed_at' => $existingClaim->claimed_at->format('Y-m-d H:i:s'),
            ], 409);
        }

        // Check quantity limit if set
        if ($eventFoodService->quantity) {
            $claimedCount = FoodServiceClaim::where('event_id', $request->event_id)
                ->where('food_service_id', $request->food_service_id)
                ->count();

            if ($claimedCount >= $eventFoodService->quantity) {
                return response()->json(['error' => 'Food service limit reached'], 400);
            }
        }

        // Create claim
        $claim = FoodServiceClaim::create([
            'user_id' => $request->user_id,
            'event_id' => $request->event_id,
            'food_service_id' => $request->food_service_id,
            'claimed_at' => now(),
            'claimed_by' => auth()->id(),
            'claim_method' => $request->claim_method ?? 'manual',
            'notes' => $request->notes,
        ]);

        $foodService = FoodService::find($request->food_service_id);
        $user = User::find($request->user_id);

        return response()->json([
            'success' => true,
            'message' => 'Food service claimed successfully',
            'claim' => [
                'id' => $claim->id,
                'user' => $user->name,
                'food_service' => $foodService->name,
                'claimed_at' => $claim->claimed_at->format('Y-m-d H:i:s'),
            ],
        ], 201);
    }

    /**
     * Unclaim a food service
     */
    public function unclaimService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'event_id' => 'required|exists:events,id',
            'food_service_id' => 'required|exists:food_services,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $claim = FoodServiceClaim::where('user_id', $request->user_id)
            ->where('event_id', $request->event_id)
            ->where('food_service_id', $request->food_service_id)
            ->first();

        if (!$claim) {
            return response()->json(['error' => 'No claim found'], 404);
        }

        $foodService = FoodService::find($request->food_service_id);
        $claim->delete();

        return response()->json([
            'success' => true,
            'message' => 'Claim removed successfully',
            'food_service' => $foodService->name,
        ]);
    }

    /**
     * Show reports page
     */
    public function reports()
    {
        $events = Event::with('foodServices')
            ->orderBy('start', 'desc')
            ->get();

        return view('food-services.reports', compact('events'));
    }

    /**
     * Get event report data
     */
    /**
 * Get event report data
 */
public function getEventReport($eventId)
{
    $event = Event::with(['foodServices'])->findOrFail($eventId);

    // Only get claims that belong to THIS event
    $eventClaims = FoodServiceClaim::where('event_id', $event->id)
        ->with(['user', 'foodService', 'claimedBy'])
        ->get();

    $report = $event->foodServices->map(function ($service) use ($event, $eventClaims) {
        // Filter claims for this specific food service AND this event
        $claims = $eventClaims->where('food_service_id', $service->id);

        $eventFoodService = EventFoodService::where('event_id', $event->id)
            ->where('food_service_id', $service->id)
            ->first();

        return [
            'food_service_id' => $service->id,
            'food_service_name' => $service->name,
            'total_quantity' => $eventFoodService->quantity ?? 'Unlimited',
            'total_claimed' => $claims->count(),
            'remaining' => $eventFoodService->quantity ? max(0, $eventFoodService->quantity - $claims->count()) : 'N/A',
            'claims' => $claims->map(function ($claim) {
                return [
                    'id' => $claim->id,
                    'user_name' => $claim->user->name,
                    'user_email' => $claim->user->email,
                    'claimed_at' => $claim->claimed_at->format('Y-m-d H:i:s'),
                    'claimed_by' => $claim->claimedBy?->name ?? 'System',
                    'claim_method' => ucfirst($claim->claim_method),
                    'notes' => $claim->notes,
                ];
            })->values(), // Use values() to reset array keys
        ];
    });

    // Get scan statistics for this event
    $scanStats = \App\Models\IdentifierScan::where('event_id', $event->id)
        ->with('user')
        ->orderByDesc('scan_count')
        ->get()
        ->map(function ($scan) {
            return [
                'user_id' => $scan->user_id,
                'user_name' => $scan->user->name,
                'user_email' => $scan->user->email,
                'scan_count' => $scan->scan_count,
                'first_scanned_at' => $scan->first_scanned_at->format('Y-m-d H:i:s'),
                'last_scanned_at' => $scan->last_scanned_at->format('Y-m-d H:i:s'),
            ];
        });

    return response()->json([
        'event' => [
            'id' => $event->id,
            'title' => $event->title,
            'start' => $event->start->format('Y-m-d H:i'),
            'end' => $event->end->format('Y-m-d H:i'),
        ],
        'report' => $report,
        'summary' => [
            'total_services' => $event->foodServices->count(),
            'total_claims' => $eventClaims->count(),
            'unique_users' => $eventClaims->unique('user_id')->count(),
        ],
        'scan_stats' => $scanStats,
    ]);
}

    /**
     * Export comprehensive report to CSV
     */
    public function exportReport($eventId)
    {
        $event = Event::with(['foodServices', 'claims.user', 'claims.foodService', 'claims.claimedBy'])
            ->findOrFail($eventId);

        $filename = 'food-service-report-' . Str::slug($event->title) . '-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($event) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // HEADER SECTION
            fputcsv($file, ['FOOD SERVICE REPORT']);
            fputcsv($file, ['Event:', $event->title]);
            fputcsv($file, ['Date:', $event->start->format('F d, Y') . ' to ' . $event->end->format('F d, Y')]);
            fputcsv($file, ['Report Generated:', now()->format('F d, Y H:i:s')]);
            fputcsv($file, []);

            // SUMMARY SECTION
            $totalClaims = $event->claims->count();
            $uniqueUsers = $event->claims->unique('user_id')->count();
            $totalServices = $event->foodServices->count();

            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Food Services:', $totalServices]);
            fputcsv($file, ['Total Claims:', $totalClaims]);
            fputcsv($file, ['Unique Users:', $uniqueUsers]);
            fputcsv($file, ['Average Claims per Service:', $totalServices > 0 ? round($totalClaims / $totalServices, 2) : 0]);
            fputcsv($file, []);

            // SERVICE BREAKDOWN SECTION
            fputcsv($file, ['SERVICE BREAKDOWN']);
            fputcsv($file, ['Food Service', 'Total Quantity', 'Total Claimed', 'Remaining', 'Utilization %']);

            foreach ($event->foodServices as $service) {
                $eventFoodService = EventFoodService::where('event_id', $event->id)
                    ->where('food_service_id', $service->id)
                    ->first();

                $claimedCount = FoodServiceClaim::where('event_id', $event->id)
                    ->where('food_service_id', $service->id)
                    ->count();

                $quantity = $eventFoodService->quantity ?? 'Unlimited';
                $remaining = $eventFoodService->quantity ? max(0, $eventFoodService->quantity - $claimedCount) : 'N/A';
                $utilization = ($quantity !== 'Unlimited' && $quantity > 0) ? round(($claimedCount / $quantity) * 100, 1) . '%' : 'N/A';

                fputcsv($file, [
                    $service->name,
                    $quantity,
                    $claimedCount,
                    $remaining,
                    $utilization
                ]);
            }
            fputcsv($file, []);

            // DETAILED CLAIMS SECTION
            fputcsv($file, ['DETAILED CLAIMS']);
            fputcsv($file, ['Food Service', 'User Name', 'User Email', 'Claimed Date', 'Claimed Time', 'Claimed By', 'Method', 'Notes']);

            foreach ($event->claims()->with(['foodService', 'user', 'claimedBy'])->orderBy('claimed_at')->get() as $claim) {
                fputcsv($file, [
                    $claim->foodService->name,
                    $claim->user->name,
                    $claim->user->email,
                    $claim->claimed_at->format('Y-m-d'),
                    $claim->claimed_at->format('H:i:s'),
                    $claim->claimedBy?->name ?? 'System',
                    ucfirst($claim->claim_method),
                    $claim->notes ?? '',
                ]);
            }

            // USER PARTICIPATION SECTION
            fputcsv($file, []);
            fputcsv($file, ['USER PARTICIPATION']);
            fputcsv($file, ['User Name', 'Email', 'Total Claims', 'Services Claimed']);

            $userStats = $event->claims()
                ->with('user')
                ->select('user_id', DB::raw('count(*) as total_claims'))
                ->groupBy('user_id')
                ->orderByDesc('total_claims')
                ->get();

            foreach ($userStats as $stat) {
                $servicesClaimed = $event->claims()
                    ->where('user_id', $stat->user_id)
                    ->with('foodService')
                    ->get()
                    ->pluck('foodService.name')
                    ->implode(', ');

                fputcsv($file, [
                    $stat->user->name,
                    $stat->user->email,
                    $stat->total_claims,
                    $servicesClaimed
                ]);
            }

            // TIMELINE SECTION
            fputcsv($file, []);
            fputcsv($file, ['CLAIMS TIMELINE']);
            fputcsv($file, ['Date', 'Hour', 'Total Claims']);

            $timeline = $event->claims()
                ->select(
                    DB::raw('DATE(claimed_at) as claim_date'),
                    DB::raw('HOUR(claimed_at) as claim_hour'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('claim_date', 'claim_hour')
                ->orderBy('claim_date')
                ->orderBy('claim_hour')
                ->get();

            foreach ($timeline as $entry) {
                fputcsv($file, [
                    $entry->claim_date,
                    sprintf('%02d:00', $entry->claim_hour),
                    $entry->total
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
