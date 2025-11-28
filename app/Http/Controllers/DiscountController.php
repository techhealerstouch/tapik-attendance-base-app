<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Models\Ticket;
use App\Models\Invoice;
use App\Models\InvoiceLog;
use App\Models\Attendance;
use App\Models\User;
use App\Models\TicketGuest;
use App\Models\Event;
use App\Models\Discount;
use GuzzleHttp\Client;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BankTransferMail;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::all();
        return view('discounts.index', compact('discounts'));
    }

    // Show the form to create a new discount
    public function create()
    {
        return view('discounts.create');
    }

    // Store a new discount
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:discounts,code',
            'name' => 'required',
            'amount' => 'required|numeric',
            'is_active' => 'boolean',
            'type' => 'required|in:percentage,fixed',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
        ]);

        Discount::create($request->all());

        return redirect()->route('discounts.index')->with('success', 'Discount created successfully.');
    }

    public function validateDiscount(Request $request)
    {
        $code = $request->input('code');
        $discount = Discount::where('code', $code)->first();

        if (!$discount || $discount->valid_until < Carbon::today()) {
            return response()->json(['message' => 'Invalid or expired discount code'], 400);
        }

        return response()->json([
            'message' => 'Discount code is valid',
            'amount' => $discount->amount
        ], 200);
    }

    // Show a specific discount
    public function show(Discount $discount)
    {
        return view('discounts.show', compact('discount'));
    }

    // Show the form to edit an existing discount
    public function edit(Discount $discount)
    {
        return view('discounts.edit', compact('discount'));
    }

    // Update an existing discount
    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'code' => 'required|unique:discounts,code,' . $discount->id,
            'name' => 'required',
            'amount' => 'required|numeric',
            'is_active' => 'boolean',
            'type' => 'required|in:percentage,fixed',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
        ]);

        $discount->update($request->all());

        return redirect()->route('discounts.index')->with('success', 'Discount updated successfully.');
    }

    // Delete a discount
    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('discounts.index')->with('success', 'Discount deleted successfully.');
    }

}
