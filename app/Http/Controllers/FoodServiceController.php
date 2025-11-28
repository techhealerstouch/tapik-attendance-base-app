<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\FoodService;
use App\Models\FoodServiceClaim;
use App\Models\EventFoodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $events = Event::where('status', 'active')
            ->orWhere('start', '>=', now())
            ->orderBy('start')
            ->get();
        
        return view('food-services.claim-interface', compact('events'));
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

        // Find user by RFID or activate_code
        $user = User::where('rfid_no', $request->identifier)
            ->orWhere('activate_code', $request->identifier)
            ->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Find event with food services
        $event = Event::with(['foodServices' => function($query) {
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
    public function getEventReport($eventId)
    {
        $event = Event::with(['foodServices', 'claims.user', 'claims.foodService', 'claims.claimedBy'])
            ->findOrFail($eventId);

        $report = $event->foodServices->map(function ($service) use ($event) {
            $claims = FoodServiceClaim::where('event_id', $event->id)
                ->where('food_service_id', $service->id)
                ->with(['user', 'claimedBy'])
                ->orderBy('claimed_at')
                ->get();

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
                }),
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
                'total_claims' => $event->claims->count(),
                'unique_users' => $event->claims->unique('user_id')->count(),
            ],
        ]);
    }

    /**
     * Export report to CSV
     */
    public function exportReport($eventId)
    {
        $event = Event::with(['foodServices', 'claims.user', 'claims.foodService', 'claims.claimedBy'])
            ->findOrFail($eventId);

        $filename = 'food-service-report-' . $event->id . '-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($event) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Event', $event->title]);
            fputcsv($file, ['Date', $event->start->format('Y-m-d') . ' to ' . $event->end->format('Y-m-d')]);
            fputcsv($file, []);

            // Claims data
            fputcsv($file, ['Food Service', 'User Name', 'User Email', 'Claimed At', 'Claimed By', 'Method', 'Notes']);

            foreach ($event->claims as $claim) {
                fputcsv($file, [
                    $claim->foodService->name,
                    $claim->user->name,
                    $claim->user->email,
                    $claim->claimed_at->format('Y-m-d H:i:s'),
                    $claim->claimedBy?->name ?? 'System',
                    ucfirst($claim->claim_method),
                    $claim->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}