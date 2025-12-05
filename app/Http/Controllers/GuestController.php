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
use GuzzleHttp\Client;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BankTransferMail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GuestController extends Controller
{
    public function index() {
        $tickets = Ticket::with('event')->where('status', 'Active')->get();
         return view('tickets.guest.index', ['tickets' => $tickets]);
    }

    public function guest_tickets()
    {
        $events = Event::all();
        $ticket_list = Ticket::with(['event'])->get();
        $tickets = TicketGuest::with(['invoice', 'ticket', 'ticket.event'])->get();
        Log::info($tickets);
        return view('tickets.admin.index', ['tickets' => $tickets, 'events' => $events, 'ticket_list' => $ticket_list]);
    }

    

    public function filterGuestTickets($eventId)
    {
        // Fetch tickets where the event ID matches in the related 'ticket' model
        $tickets = TicketGuest::with(['invoice', 'ticket.event'])
                              ->whereHas('ticket', function ($query) use ($eventId) {
                                  $query->where('event_id', $eventId);
                              })
                              ->orderBy('created_at', 'DESC')
                              ->get();
    
        // Return the filtered tickets as JSON
        return response()->json($tickets);
    }


    public function order($id) {
        $tickets = Ticket::with('event')->where('id', $id)->first();
        return view('tickets.guest.order', ['tickets' => $tickets]);
    }
    public function createOrderNumber() {
        $lastTicket = Invoice::orderBy('invoice_no', 'desc')->first();

        // Check if there are any existing tickets, if not, start from 1
        if ($lastTicket) {
            // Extract the numeric part from the last ticket_no and increment it
            $lastTicketNo = intval(str_replace('ORD', '', $lastTicket->invoice_no));
            $newTicketNo = $lastTicketNo + 1;
        } else {
            // Start from 1 if no tickets exist
            $newTicketNo = 1;
        }
        
        // Format the new ticket number with leading zeros (up to 10 digits)
        $formattedTicketNo = 'ORD' . str_pad($newTicketNo, 10, '0', STR_PAD_LEFT);

        return $formattedTicketNo;
    }
    private function createInvoice($total_price, $ticket_id, $quantity, $email, $firstName, $lastName, $all_names_and_emails) {
        $client = new Client(['verify' => false]);
        $ticket = Ticket::with('event')->where('id', $ticket_id)->first();
        $ticketTotal = $total_price;
        $apiSecretKey = env('XENDIT_SECRET_API_KEY');
        try {
            $response = $client->request('POST', 'https://api.xendit.co/v2/invoices', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth' => [
                    $apiSecretKey,
                    '',
                ],
                'json' => [
                    'external_id' => $this->createOrderNumber(),
                    'amount' =>  $ticketTotal ,
                    'description' => 'Payment invoice for ' . $firstName . ' ' . $lastName .' - Ticket Name: ' . $ticket->name,
                    'customer' => [
                        'given_names' => $firstName . ' ' . $lastName, 
                        'surname' => $lastName, 
                        'email' => $email, 
                    ],
                    'customer_notification_preference' => [
                        'invoice_paid' => ["email"]
                    ],
                    'invoice_duration' => 86400,
                    'success_redirect_url' => url('/ticket-success'),
                    'failure_redirect_url' => url('/ticket-error'),
                    'currency' => 'PHP'
                ]
            ]);
    
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

                $inv = Invoice::create([
                    'user_id' => null,
                    'ticket_id' => $ticket->id,
                    'invoice_no' => $data['external_id'],
                    'xendit_id' => $data['user_id'], //xendit business ID / user_id
                    'xendit_invoice_no' => $data['id'], //xendit invoice ID
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'amount' => $data['amount'],
                    'paid_amount' => null,
                    'currency' => $data['currency'],
                    'payer_email' => $email,
                    'payment_method' => null,
                    'bank_code' => null,
                    'payment_channel' => null,
                    'payment_destination' => null,
                    'expiry_date' => $data['expiry_date'],
                    'paid_at' => null,
                    'invoice_url' => $data['invoice_url'],
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'quantity' => $quantity,
                    'attendees' => json_encode($all_names_and_emails)
                ]);

                $url = $data['invoice_url'];
                $amount = $data['amount'];
                $expiry_date = $data['expiry_date'];
                $invoice_no = $data['external_id'];
                $eventName = $ticket->event->title;
                $ticketName = $ticket->name;
                $name = $firstName . ' ' . $lastName;
                $toEmail = $email;
                InvoiceLog::create([
                    'invoice_id' => $inv->id,
                    'status' => $data['status'],
                    'amount' => $data['amount'],
                    'description' => 'Created guest invoice for ' . $name .  ' : Xendit',  // Correct string concatenation
                    'logged_at' => Carbon::now(),  // Using Carbon to set the current timestamp
                ]);
                //Mail::to($toEmail)->send(new PaymentMail($url, $eventName, $ticketName, $name, $amount, $expiry_date, $invoice_no));

            return $url;
    
        } catch (RequestException $e) {
            Log::error('Error creating invoice: ' . $e->getMessage());
            Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            return ['error' => $e->getMessage()];
        }
    }
    public function xendit(Request $request) {
        Log::info('TEST request: ' . json_encode($request->all()));
        // Extract values from the request
        $firstName = $request->firstName[0]; // Get the first firstName
        $lastName = $request->lastName[0]; // Get the first lastName
        $email = $request->email[0]; // Get the first email
        $quantity = $request->quantity;
        $total_price = $request->total_price;
        $ticket_id = $request->ticket_id;
    
        // Save all the first names, last names, and emails in an array for later use
        $all_names_and_emails = [];
        for ($i = 0; $i < count($request->firstName); $i++) {
            $all_names_and_emails[] = [
                'firstName' => $request->firstName[$i],
                'lastName' => $request->lastName[$i],
                'email' => $request->email[$i]
            ];
        }
    
        // Use json_encode for logging the array
        Log::info('All Names and Emails: ' . json_encode($all_names_and_emails));
        $url = $this->createInvoice($total_price, $ticket_id, $quantity, $email, $firstName, $lastName, $all_names_and_emails);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Xendit invoice created successfully',
            'url' => $url
        ]);
    }

    private function createInvoicePaymongo($total_price, $ticket_id, $quantity, $email, $firstName, $lastName, $all_names_and_emails) {
        $client = new Client(['verify' => false]);
        $ticket = Ticket::with('event')->where('id', $ticket_id)->first();
        $quantity = (int) $quantity;
        $orig_price = $ticket->price;
        $total_amount = $orig_price * $quantity;
        $ticketTotal = $orig_price * 100;
        

        $apiSecretKey = env('PAYMONGO_SECRET_API_KEY');

        // Encode the API secret key in Base64 format as required for Basic Auth
        $encodedApiKey = base64_encode($apiSecretKey . ':');

        try {
            $response = $client->request('POST', 'https://api.paymongo.com/v1/checkout_sessions', [
                'body' => json_encode([
                    'data' => [
                        'attributes' => [
                            'billing' => [
                                'name' =>$firstName . ' ' . $lastName,
                                'email' => $email,
                            ],
                            'send_email_receipt' => true,
                            'show_description' => true,
                            'show_line_items' => true,
                            'success_url' => url('/ticket-success'),
                            'payment_method_types' => [
                                'billease', 'card', 'dob', 'dob_ubp', 'brankas_bdo',
                                'brankas_landbank', 'brankas_metrobank', 'gcash',
                                'paymaya', 'qrph'
                            ],
                            'line_items' => [
                                [
                                    'currency' => 'PHP',
                                    'amount' => $ticketTotal,
                                    'name' => $ticket->name,
                                    'quantity' => $quantity,
                                    'description' => 'Pass',
                                ],
                            ],
                            'reference_number' => $this->createOrderNumber(),
                            'description' => 'Order payment for ' . $firstName . ' ' . $lastName .' - Ticket Name: ' . $ticket->name,
                        ],
                    ],
                ]),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . $encodedApiKey,
                ],
            ]);
            
            
    
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            Log::info('DATA: ' . json_encode($data));


                $inv = Invoice::create([
                    'user_id' => null,
                    'ticket_id' => $ticket->id,
                    'invoice_no' => $data['data']['attributes']['reference_number'],
                    'xendit_id' => $data['data']['id'], //xendit business ID / user_id
                    'xendit_invoice_no' => $data['data']['id'], //xendit invoice ID
                    'description' => $data['data']['attributes']['description'],
                    'status' => $data['data']['attributes']['status'],
                    'amount' => $total_amount,
                    'paid_amount' => null,
                    'currency' => $data['data']['attributes']['line_items'][0]['currency'],
                    'payer_email' => $email,
                    'payment_method' => null,
                    'bank_code' => null,
                    'payment_channel' => null,
                    'payment_destination' => null,
                    'expiry_date' => null,
                    'paid_at' => null,
                    'invoice_url' => $data['data']['attributes']['checkout_url'],
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'quantity' => $quantity,
                    'attendees' => json_encode($all_names_and_emails)
                ]);

                $url = $data['data']['attributes']['checkout_url'];
                //  $amount = $data['amount'];
                //  $expiry_date = $data['expiry_date'];
                //  $invoice_no = $data['external_id'];
                //  $eventName = $ticket->event->title;
                //  $ticketName = $ticket->name;
                $name = $firstName . ' ' . $lastName;
                //  $toEmail = $email;
                InvoiceLog::create([
                    'invoice_id' => $inv->id,
                    'status' => $data['data']['attributes']['status'],
                    'amount' => $total_amount,
                    'description' => 'Created guest invoice for ' . $name .  ' : Paymongo',  // Correct string concatenation
                    'logged_at' => Carbon::now(),  // Using Carbon to set the current timestamp
                ]);
                //Mail::to($toEmail)->send(new PaymentMail($url, $eventName, $ticketName, $name, $amount, $expiry_date, $invoice_no));

            return $url;
    
        } catch (RequestException $e) {
            Log::error('Error creating invoice: ' . $e->getMessage());
            Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            return ['error' => $e->getMessage()];
        }
    }
    public function paymongo(Request $request) {
        Log::info('TEST request: ' . json_encode($request->all()));
        // Extract values from the request
        $firstName = $request->firstName[0]; // Get the first firstName
        $lastName = $request->lastName[0]; // Get the first lastName
        $email = $request->email[0]; // Get the first email
        $quantity = $request->quantity;
        $total_price = $request->total_price;
        $ticket_id = $request->ticket_id;
    
        // Save all the first names, last names, and emails in an array for later use
        $all_names_and_emails = [];
        for ($i = 0; $i < count($request->firstName); $i++) {
            $all_names_and_emails[] = [
                'firstName' => $request->firstName[$i],
                'lastName' => $request->lastName[$i],
                'email' => $request->email[$i]
            ];
        }
    
        // Use json_encode for logging the array
        Log::info('All Names and Emails: ' . json_encode($all_names_and_emails));
        $url = $this->createInvoicePaymongo($total_price, $ticket_id, $quantity, $email, $firstName, $lastName, $all_names_and_emails);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Xendit invoice created successfully',
            'url' => $url
        ]);
    }
    

    public function bank_transfer(Request $request)
    {
        Log::info($request);
        $firstName = $request->firstName[0]; // Get the first firstName
        $lastName = $request->lastName[0]; // Get the first lastName
        $email = $request->email[0]; 
        $quantity = $request->quantity;
        $total_price = $request->total_price;
        $ticket_id = $request->ticket_id;

        $all_names_and_emails = [];
        for ($i = 0; $i < count($request->firstName); $i++) {
            $all_names_and_emails[] = [
                'firstName' => $request->firstName[$i],
                'lastName' => $request->lastName[$i],
                'email' => $request->email[$i]
            ];
        }
        // Fetch the ticket and calculate total
       // $ticket = Ticket::with('event')->where('id', $ticket_id)->first();
        $ticketTotal = $total_price;
    
        // Create invoice
        $inv = Invoice::create([
            'user_id' => null,
            'ticket_id' => $ticket_id,
            'invoice_no' => $this->createOrderNumber(),
            'description' => 'Bank Transfer Payment',
            'status' => 'PENDING',
            'amount' => $ticketTotal,
            'currency' => 'PHP',
            'payer_email' => $email,
            'payment_method' => 'BANK_TRANSFER',
            'expiry_date' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'quantity' => $quantity,
            'attendees' => json_encode($all_names_and_emails)
        ]);
    
        // Define additional variables to be passed to the email template
        $data = [
            'name' => "{$firstName} {$lastName}",
            'amount_due' => $ticketTotal,
            'transaction_reference' => $inv->invoice_no,
            'due_date' => $inv->expiry_date,
            'bank_name' => env('BANK_NAME'),
            'account_name' => env('BANK_ACCOUNT_NAME'),
            'account_number' => env('BANK_ACCOUNT_NUMBER'),
        ];
        
    
        // Send the bank transfer mail
        Mail::to($email)->send(new BankTransferMail($data));
    
        $url = url("/ticket/order-details/{$inv->invoice_no}");
        
        return response()->json([
            'status' => 'success',
            'message' => 'Invoice created successfully',
            'url' => $url
        ]);
    }
    

    public function order_details($invoiceId) {

        Log::info($invoiceId);
        $invoice = Invoice::with('ticket.event')->where('invoice_no', $invoiceId)->first();
        return view('tickets.guest.order-details', ['invoice' => $invoice]);
    }

    public function guest_ticket_input(Request $request)
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
            'start' => $formattedStart, // Use the formatted start date here
            'end' => $formattedEnd, // Use the formatted start date here
            'address' => $eventDetails->address,
        ];
    
        return view('tickets.guest.attendance', $data);
    }
    
    
    // public function scanTicket(Request $request)
    // {
    //     $ticketNo = $request->input('ticket_no');
    //     $event_id = $request->input('event_no');
    //     Log::info($ticketNo);
        
    //     // Check if ticketNo is a URL
    //     if (filter_var($ticketNo, FILTER_VALIDATE_URL)) {
    //         // Extract the user ID from the URL (assuming the user ID is the last segment of the URL)
    //         $userId = basename($ticketNo);
    //         Log::info($userId);
        
    //         // Find the user using the extracted user ID
    //         $user = User::find($userId);
        
    //         if (!$user) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'User not found.',
    //             ]);
    //         }
        
    //         // Find the ticket for the event ID
    //         $ticket = Ticket::where('event_id', $event_id)->first();
        
    //         if (!$ticket) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Ticket not found for the specified event.',
    //             ]);
    //         }
        
    //         Log::info($ticket->id);
        
    //         // Find the invoice for the user and ticket
    //         $invoice = Invoice::with('user')->where('user_id', $userId)
    //         ->where('ticket_id', $ticket->id)
    //         ->orderBy('created_at', 'desc')
    //         ->first();
    //         Log::info($invoice);
        
    //         if (!$invoice) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'You are not part of this event. Please contact admin/helpdesk.',
    //             ]);
    //         }
        
    //         // Find the ticket guest
    //         $ticketGuest = TicketGuest::where('invoice_id', $invoice->id)->first();
    //         $events = Event::where('id', $event_id)->first();
        
    //         if ($ticketGuest && $ticketGuest->is_scanned === 1) {
    //             return response()->json([
    //                 'status' => 'already_scanned',
    //                 'message' => 'You are already checked in.',
    //             ]);
    //         }
        
    //     // Update the ticketGuest to mark it as scanned
    //     $ticket_no = $this->createTicket($invoice->id, $ticket->id, $invoice->user->name);
    //     Log::info($ticket_no);
    //     $ticketUser = TicketGuest::where('ticket_no', $ticket_no)->first();
    //     $ticketUser->is_scanned = 1;
    //     $ticketUser->save();
    //     Attendance::create([
    //         'user_id' => $userId,
    //         'event_id' => $event_id,
    //         'event_name' => $events->title,
    //         'time_in' => now(),
    //         'status' => 'Present',
    //     ]);
    //         // Reload invoice with user relationship
    //         $invoice = Invoice::with('user')->find($invoice->id);
            
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Thank you ' . $invoice->user->name . ', You are now checked in.',
    //         ]);
    //     }else {
    //         // Handle the case where ticketNo is a regular ticket number
    //         $ticketGuest = TicketGuest::where('ticket_no', $ticketNo)->first();

    //         if ($ticketGuest) {
    //             // Check if the ticket has already been scanned
    //             if (!$ticketGuest->is_scanned) {
    //                 // Update the is_scanned field to true
    //                 $ticketGuest->is_scanned = 1;
    //                 $ticketGuest->save();

    //                 return response()->json([
    //                     'status' => 'success',
    //                     'message' => 'Thank you ' . $ticketGuest->first_name . ' ' . $ticketGuest->last_name  .', You are now Checked in.',
    //                 ]);
    //             } else {
    //                 return response()->json([
    //                     'status' => 'already_scanned',
    //                     'message' => 'You are already checked in.'
    //                 ]);
    //             }
    //         }

    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Ticket not found.'
    //         ]);
    //     }
    // }

    public function scanTicket(Request $request)
    {
        try {
            $ticketNo = $request->input('ticket_no');
            $event_id = $request->input('event_no');
            Log::info($event_id);
            // Check if ticketNo is a URL
            if (filter_var($ticketNo, FILTER_VALIDATE_URL)) {
                // Extract the user ID from the URL (assuming the user ID is the last segment of the URL)
                $userId = basename($ticketNo);
                Log::info($userId);
                Log::info($ticketNo);
            
                // Find the user using the extracted user ID
                $user = User::find($userId);
            
                if (!$user) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User not found.',
                    ]);
                }
                
                $attendance = Attendance::where('user_id', $userId)
                ->where('event_id', $event_id)
                ->first();

                $userName = User::where('id', $userId)->first();
    
                if (!$attendance) {
                    
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are not part of this event. Please contact admin or support.',
                    ]);
                }
    
                if ($attendance && $attendance->status === 'Present') {
                    return response()->json([
                        'status' => 'already_scanned',
                        'message' => "{$userName->name}, You are already checked in.",
                    ]);
                }
                $attendance->time_in = now();
                $attendance->status = 'Present';
                $attendance->save();
                
                return response()->json([
                    'status' => 'success',
                    'message' => "Thank you {$userName->name}, You are now checked in.",
                ]);
            }else {
                // Handle the case where ticketNo is a regular ticket number
                $ticketGuest = TicketGuest::where('ticket_no', $ticketNo)->first();
                $ticketList = Ticket::where('id', $ticketGuest->ticket_id)->first();
                Log::info($ticketGuest);
                Log::info($ticketList);
    
                if ($ticketList->event_id !== (int) $event_id) {
                    Log::info('TEST');
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Your ticket is not part of this event. Please contact admin/helpdesk for inquiries.',
                    ]);
                }
    
    
                if ($ticketGuest) {
                    // Check if the ticket has already been scanned
                    if (!$ticketGuest->is_scanned) {
                        // Update the is_scanned field to true
                        $ticketGuest->is_scanned = 1;
                        $ticketGuest->save();
    
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Thank you ' . $ticketGuest->first_name . ' ' . $ticketGuest->last_name  .', You are now Checked in.',
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'already_scanned',
                            'message' => 'You are already checked in.'
                        ]);
                    }
                }
    
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket not found.'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try scanning again or refresh the page.'
            ]);
        }
       
    }

    public function createTicket($invId, $ticketId, $name) {
        $lastTicket = TicketGuest::orderBy('id', 'desc')->first();

        // Check if there are any existing tickets, if not, start from 1
        if ($lastTicket) {
            // Extract the numeric part from the last ticket_no and increment it
            $lastTicketNo = intval(str_replace('TCK', '', $lastTicket->ticket_no));
            $newTicketNo = $lastTicketNo + 1;
        } else {
            // Start from 1 if no tickets exist
            $newTicketNo = 1;
        }
        
        // Format the new ticket number with leading zeros (up to 10 digits)
        $formattedTicketNo = 'TCK' . str_pad($newTicketNo, 10, '0', STR_PAD_LEFT);
        
        $ticketGuest = TicketGuest::create([
            'invoice_id' => $invId,  // Accessing the ID from the fetched model
            'ticket_id' => $ticketId,
            'first_name' => $name,
            'last_name' => $name,
            'ticket_no' => $formattedTicketNo,
        ]);

        return $ticketGuest->ticket_no;
    }

    public function store(Request $request)
    {
        Log::info("Request: " . json_encode($request->all()));

        $last_name = $request->last_name;
        $first_name = $request->first_name;

        $this->createTicketManual($request->ticket, $first_name, $last_name);

        return redirect()->back()->with('success', 'Ticket created successfully!');
    }

    public function bulk_store(Request $request)
    {
        Log::info("Request: " . json_encode($request->all()));

        $pass_count = $request->passCount; //ex. 50
        $first_name = null;
        $last_name = null;
        $ticket = $request->ticket;

         // Loop through the pass count and run createTicketManual for each
        for ($i = 0; $i < $pass_count; $i++) {
            $this->createTicketManual($ticket, $first_name, $last_name);
        }

        return redirect()->back()->with('success', 'Ticket created successfully!');
    }

    public function update(Request $request, $id)
    {
        Log::info($request);
        $ticket = TicketGuest::findOrFail($id);
        $ticket->first_name = $request->input('first_name');
        $ticket->last_name = $request->input('last_name');
        $ticket->ticket_id = $request->input('ticket');
        $ticket->is_scanned = $request->input('status');
        $ticket->save();

        return redirect()->back()->with('success', 'Ticket updated successfully!');
    }

    public function createTicketManual($ticketId, $first_name, $last_name) {
        $lastTicket = TicketGuest::orderBy('id', 'desc')->first();

        // Check if there are any existing tickets, if not, start from 1
        if ($lastTicket) {
            // Extract the numeric part from the last ticket_no and increment it
            $lastTicketNo = intval(str_replace('TCK', '', $lastTicket->ticket_no));
            $newTicketNo = $lastTicketNo + 1;
        } else {
            // Start from 1 if no tickets exist
            $newTicketNo = 1;
        }
        
        // Format the new ticket number with leading zeros (up to 10 digits)
        $formattedTicketNo = 'TCK' . str_pad($newTicketNo, 10, '0', STR_PAD_LEFT);
        
        $ticketGuest = TicketGuest::create([ // Accessing the ID from the fetched model
            'ticket_id' => $ticketId,
            'first_name' => $first_name ?? null,
            'last_name' => $last_name ?? null,
            'ticket_no' => $formattedTicketNo,
        ]);

        return $ticketGuest->ticket_no;
    }

    public function exportPass($ticketId)
    {
        $ticketGuest = TicketGuest::where('ticket_no', $ticketId)->firstOrFail();
        $tickets = Ticket::with('event')->where('id', $ticketGuest->ticket_id)->first();
        $start = Carbon::parse($tickets->event->start);
        $end = Carbon::parse($tickets->event->end);
        // Prepare ticket data
        $ticketData = [];
        $argValues = config('advanced-config.qr_code_gradient') ?? [0, 0, 0, 0, 0, 0, 'diagonal'];
        list($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7) = $argValues;

        try {
            if (extension_loaded('imagick')) {
                $imgSrc = QrCode::format('png')
                    ->gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                    ->eye('circle')
                    ->style('round')
                    ->size(300)
                    ->generate($ticketGuest->ticket_no);
                $imgSrc = base64_encode($imgSrc);
                $imgSrc = 'data:image/png;base64,' . $imgSrc;
            } else {
                $imgSrc = QrCode::gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                    ->eye('circle')
                    ->style('round')
                    ->size(300)
                    ->generate($ticketGuest->ticket_no);
                $imgSrc = base64_encode($imgSrc);
                $imgSrc = 'data:image/png;base64,' . $imgSrc;
            }
        } catch (\Exception $e) {
            $imgSrc = url('/assets/linkstack/images/themes/no-preview.png');
            $imgType = NULL;
        }

        $data = [
            'event_name' => $tickets->event->title,
            'address' => $tickets->event->address,
            'start_date' => $start->format('F d, Y'),
            'end_date' => $end->format('F d, Y'),
            'start_time' => $start->format('g:i A'),
            'end_time' => $end->format('g:i A'),
            'ticket_no' => $ticketId,  // Fixed incorrect variable name
            'qr_code' => $imgSrc,
            'attendee_name' => $ticketGuest->first_name . ' ' . $ticketGuest->last_name,
        ];
    
        $pdf = Pdf::loadView('tickets.pdf.single-ticket', ['tickets' => [$data]]);
    
        // Return PDF as a download
        return $pdf->download($ticketGuest->ticket_no . '.pdf');
    }


    public function exportPassBulk($ticketId)
    {
        Log::info($ticketId);
        $ticketGuests = TicketGuest::where('ticket_id', $ticketId)->get();
        $tickets = Ticket::with('event')->where('id', $ticketId)->first();
        $start = Carbon::parse($tickets->event->start);
        $end = Carbon::parse($tickets->event->end);
        // Prepare ticket data
        $ticketData = [];
        $argValues = config('advanced-config.qr_code_gradient') ?? [0, 0, 0, 0, 0, 0, 'diagonal'];
        list($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7) = $argValues;
        Log::info($ticketGuests);
        foreach ($ticketGuests as $ticketGuest) {
            try {
                if (extension_loaded('imagick')) {
                    $imgSrc = QrCode::format('png')
                        ->gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                        ->eye('circle')
                        ->style('round')
                        ->size(300)
                        ->generate($ticketGuest->ticket_no);
                    $imgSrc = base64_encode($imgSrc);
                    $imgSrc = 'data:image/png;base64,' . $imgSrc;
                } else {
                    $imgSrc = QrCode::gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                        ->eye('circle')
                        ->style('round')
                        ->size(300)
                        ->generate($ticketGuest->ticket_no);
                    $imgSrc = base64_encode($imgSrc);
                    $imgSrc = 'data:image/png;base64,' . $imgSrc;
                }
            } catch (\Exception $e) {
                $imgSrc = url('/assets/linkstack/images/themes/no-preview.png');
                $imgType = NULL;
            }
    
            $data = [
                'event_name' => $tickets->event->title,
                'address' => $tickets->event->address,
                'start_date' => $start->format('F d, Y'),
                'end_date' => $end->format('F d, Y'),
                'start_time' => $start->format('g:i A'),
                'end_time' => $end->format('g:i A'),
                'ticket_no' => $ticketGuest->ticket_no, 
                'qr_code' => $imgSrc,
                'attendee_name' => $ticketGuest->first_name . ' ' . $ticketGuest->last_name,
            ];

            
    
            $ticketData[] = $data;
        }
        Log::info('Ticket data added:', $ticketData);
    
        $pdf = Pdf::loadView('tickets.pdf.bulk-ticket', ['tickets' => $ticketData]);
        
        // Return PDF as a download
        return $pdf->download($tickets->event->title . ' - ' . $tickets->name . '.pdf');
    }

}
