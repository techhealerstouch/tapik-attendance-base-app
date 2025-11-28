<?php

namespace App\Http\Controllers;

use App\Mail\PaymentConfirmation;
use Illuminate\Support\Str;
use App\Models\Ticket;
use App\Models\Invoice;
use App\Models\InvoiceLog;
use App\Models\User;
use App\Models\TicketGuest;
use App\Models\Attendance;
use App\Models\Attendee;
use App\Models\Event;
use GuzzleHttp\Client;
use App\Models\Group;
use App\Models\GroupUser;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentMail;
use App\Mail\InviteMail;
use App\Mail\TicketMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    /**
     * Display a listing of the tickets.
     */
    public function index()
    {
        //$tickets = Ticket::all();
        $users = User::all();
        $events = Event::all();
        $tickets = Ticket::with('event')->get();
        return view('tickets.index', ['users' => $users, 'events' => $events, 'tickets' => $tickets]);
    }

    public function view_invoice($id)
    {
        //$tickets = Ticket::all();
        $invoices = Invoice::with('ticket', 'user', 'ticket.event')->where('id', $id)->first();
        return view('tickets.invoice.view-invoice', ['invoices' => $invoices]);
    }
    public function filterTickets($eventId)
    {
        // Fetch tickets based on the selected event ID
        $tickets = Ticket::with('event')->where('event_id', $eventId)->get();

        // Return the filtered tickets as JSON
        return response()->json($tickets);
    }


    public function index_invoice()
    {
        //$tickets = Ticket::all();
        $users = User::all();
        $events = Event::all();
        $tickets = Ticket::all();
        $invoices = Invoice::with('ticket', 'user', 'ticket.event')->get();
        return view('tickets.invoice.index', ['users' => $users, 'tickets' => $tickets, 'events' => $events, 'invoices' => $invoices]);
    }

    public function index_invoice_logs()
    {
        $invoices = InvoiceLog::orderBy('created_at', 'desc')->get();
        return view('tickets.invoice.invoice-logs', ['invoices' => $invoices]);
    }

    public function success_ticket()
    {
        return view('tickets.success');
    }

    public function error_ticket()
    {
        return view('tickets.error');
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $users = User::all();
        $events = Event::all();
        $groups = Group::all();
        $tickets = Ticket::with('event')->get();
        return view('tickets.create-ticket', ['users' => $users, 'events' => $events, 'tickets' => $tickets, 'groups' => $groups]);
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function handleCallback(Request $request)
    {
        $xenditXCallbackToken = env('XENDIT_WEBHOOK_VERIFICATION_TOKEN');

        // Retrieve the 'x-callback-token' from the request headers
        $xIncomingCallbackTokenHeader = $request->header('X-CALLBACK-TOKEN');
        if ($xIncomingCallbackTokenHeader === $xenditXCallbackToken) {
            $arrRequestInput = $request->all();

            // Log the request data (optional)
            Log::info('Xendit Callback Received: ', $arrRequestInput);

            $externalId = $arrRequestInput['external_id'] ?? null;
            $inv = Invoice::where('invoice_no', $externalId)->first();  // Fetch the invoice model first
            $inv->update([  // Update the model after fetching it
                'paid_amount' => $arrRequestInput['paid_amount'] ?? null,
                'payment_channel' => $arrRequestInput['payment_channel'] ?? null,
                'payment_method' => $arrRequestInput['payment_method'] ?? null,
                'paid_at' => $arrRequestInput['paid_at'] ?? null,
                'status' => $arrRequestInput['status'] ?? null,
                'payment_id' => $arrRequestInput['payment_id'] ?? null
            ]);

            if ($arrRequestInput['status']  === 'EXPIRED') {
                InvoiceLog::create([
                    'invoice_id' => $inv->id,  // Accessing the ID from the fetched model
                    'status' => $arrRequestInput['status'],
                    'amount' => $inv->amount,
                    'description' => 'Invoice Expired! Invoice Number:' . $externalId . '  : Xendit',  // Correct string concatenation
                    'logged_at' => Carbon::now(),  // Using Carbon to set the current timestamp
                ]);

                return response()->json(['message' => 'Callback processed successfully'], 200);
            }

            InvoiceLog::create([
                'invoice_id' => $inv->id,  // Accessing the ID from the fetched model
                'status' => $arrRequestInput['status'],
                'amount' => $inv->amount,
                'description' => 'Invoice Paid! Invoice Number:' . $externalId . '  : Xendit',  // Correct string concatenation
                'logged_at' => Carbon::now(),  // Using Carbon to set the current timestamp
            ]);

            $customerEmail = $inv->email;
            $attendees = json_decode($inv->attendees, true);
            $quantity = $inv->quantity;  // Assuming 'quantity' is in the invoice model

            $argValues = config('advanced-config.qr_code_gradient') ?? [0, 0, 0, 0, 0, 0, 'diagonal'];
            list($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7) = $argValues;

            // $ticket_no = $this->createTicket($inv->id, $inv->ticket_id);
            // Generate the ticket PDF
            $tickets = Ticket::with('event')->where('id', $inv->ticket_id)->first();
            $start = Carbon::parse($tickets->event->start);
            $end = Carbon::parse($tickets->event->end);
            // Prepare ticket data
            $ticketData = [];
            Log::info($attendees);
            for ($i = 1; $i <= $inv->quantity; $i++) {
                $attendee = $attendees[$i - 1] ?? ['firstName' => 'Guest', 'lastName' => ''];
                $ticket_no = $this->createTicket($inv->id, $inv->ticket_id, $attendee['firstName'], $attendee['lastName']);  // Create unique ticket numbers
                // Use default if missing
                try {
                    if (extension_loaded('imagick')) {
                        $imgSrc = QrCode::format('png')
                            ->gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                            ->eye('circle')
                            ->style('round')
                            ->size(300)
                            ->generate($ticket_no);
                        $imgSrc = base64_encode($imgSrc);
                        $imgSrc = 'data:image/png;base64,' . $imgSrc;
                    } else {
                        $imgSrc = QrCode::gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                            ->eye('circle')
                            ->style('round')
                            ->size(300)
                            ->generate($ticket_no);
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
                    'ticket_no' => $ticket_no,
                    'qr_code' => $imgSrc, // Assuming QR code image is generated here
                    'attendee_name' => $attendee['firstName'] . ' ' . $attendee['lastName'],  // Include attendee name
                ];
                $ticketData[] = $data;  // Collect ticket data
            }

            if ($inv->user_id) {
                $inv = Invoice::where('invoice_no', $externalId)->first();
                $name = $inv->user->name;
                Mail::to($inv->user->email)->send(new PaymentConfirmation($name));
            } else {
                $name = $inv->first_name . ' ' . $inv->last_name;

                $pdf = Pdf::loadView('tickets.pdf.ticket', ['tickets' => $ticketData]);
                $filePath = storage_path('app/public/event_ticket_' . $inv->id . '.pdf');
                Log::info('File PATH: ' . $filePath);
                $fileName = "Ticket for " . $inv->first_name . "_" . $inv->last_name;
                $pdf->save($filePath);  // Save the PDF to a file
                Mail::to($customerEmail)->send(new TicketMail($name, $filePath, $fileName));
            }



            return response()->json(['message' => 'Callback processed successfully'], 200);
        } else {
            return response()->json(['message' => 'Forbidden'], 403);
        }
    }

    public function handleCallbackPaymongo(Request $request)
    {
        try {
            Log::info('PayMongo Webhook Received:', [$request->all()]);

            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return response()->json(['message' => 'Invalid payload'], 400);
            }

            // Extract payment values
            $status = $data['data']['attributes']['data']['attributes']['payments'][0]['attributes']['status'] ?? null;
            $payment_id = $data['data']['attributes']['data']['attributes']['payments'][0]['attributes']['id'] ?? null;
            $amount = $data['data']['attributes']['data']['attributes']['payments'][0]['attributes']['amount'] ?? null;
            $paidAtTimestamp = $data['data']['attributes']['data']['attributes']['payments'][0]['attributes']['paid_at'] ?? null;
            $paidAt = $paidAtTimestamp ? Carbon::createFromTimestamp($paidAtTimestamp) : null;
            $amountInPeso = $amount ? $amount / 100 : null;

            $id = $data['data']['attributes']['data']['id'] ?? null;
            $payment_method_used = $data['data']['attributes']['data']['attributes']['payment_method_used'] ?? null;

            // Find Invoice
            $inv = Invoice::where('xendit_id', $id)->first();

            if (!$inv) {
                Log::error("Invoice not found for xendit_id: " . $id);
                return response()->json(['message' => 'Invoice not found'], 404);
            }

            // Update the Invoice record
            $inv->update([
                'paid_amount' => $amountInPeso,
                'payment_method' => $payment_method_used,
                'paid_at' => $paidAt,
                'status' => $status,
                'payment_id' => $payment_id
            ]);

            // Log the invoice update
            InvoiceLog::create([
                'invoice_id' => $inv->id,
                'status' => $status,
                'amount' => $inv->amount,
                'description' => 'Invoice Paid! Invoice Number:' . $inv->invoice_no . ' : PayMongo',
                'logged_at' => Carbon::now(),
            ]);

            // Prepare data
            $customerEmail = $inv->email;
            $attendees = json_decode($inv->attendees, true);
            $quantity = $inv->quantity;

            $argValues = config('advanced-config.qr_code_gradient') ?? [0, 0, 0, 0, 0, 0, 'diagonal'];
            list($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7) = $argValues;

            // Fetch Ticket Details
            $tickets = Ticket::with('event')->where('id', $inv->ticket_id)->first();
            $start = Carbon::parse($tickets->event->start);
            $end = Carbon::parse($tickets->event->end);

            $ticketData = [];
            Log::info('send test');
            for ($i = 1; $i <= $quantity; $i++) {
                $attendee = $attendees[$i - 1] ?? ['firstName' => 'Guest', 'lastName' => ''];
                $ticket_no = $this->createTicket($inv->id, $inv->ticket_id, $attendee['firstName'], $attendee['lastName']);

                // QR code generation
                try {
                    if (extension_loaded('imagick')) {
                        $imgSrc = QrCode::format('png')
                            ->gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                            ->eye('circle')
                            ->style('round')
                            ->size(300)
                            ->generate($ticket_no);
                    } else {
                        $imgSrc = QrCode::gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                            ->eye('circle')
                            ->style('round')
                            ->size(300)
                            ->generate($ticket_no);
                    }

                    $imgSrc = 'data:image/png;base64,' . base64_encode($imgSrc);
                } catch (\Exception $e) {
                    Log::error("QR Code generation failed: " . $e->getMessage());
                    $imgSrc = url('/assets/linkstack/images/themes/no-preview.png');
                }

                $ticketData[] = [
                    'event_name' => $tickets->event->title,
                    'address' => $tickets->event->address,
                    'start_date' => $start->format('F d, Y'),
                    'end_date' => $end->format('F d, Y'),
                    'start_time' => $start->format('g:i A'),
                    'end_time' => $end->format('g:i A'),
                    'ticket_no' => $ticket_no,
                    'qr_code' => $imgSrc,
                    'attendee_name' => $attendee['firstName'] . ' ' . $attendee['lastName'],
                ];
            }
            Log::info('send test 1');
            // EMAILS
            if ($inv->user_id) {
                $name = $inv->user->name;
                Mail::to($inv->user->email)->send(new PaymentConfirmation($name));
            } else {
                $name = $inv->first_name . ' ' . $inv->last_name;

                $pdf = Pdf::loadView('tickets.pdf.ticket', ['tickets' => $ticketData]);
                $filePath = storage_path('app/public/event_ticket_' . $inv->id . '.pdf');
                $fileName = "Ticket for " . $inv->first_name . "_" . $inv->last_name;
                Log::info('customer email' . $customerEmail);
                $pdf->save($filePath);
                try {
                    Log::info('send mail test paymongo');
                    Mail::to($customerEmail)->send(new TicketMail($name, $filePath, $fileName));
                } catch (\Exception $e) {
                    Log::error("Mail sending failed: " . $e->getMessage());
                }
            }

            return response()->json(['message' => 'Callback processed successfully'], 200);
        } catch (\Throwable $e) {
            Log::error("PayMongo Callback Error: " . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Always respond with 200 so PayMongo does NOT reattempt endlessly
            return response()->json(['message' => 'Error processed'], 200);
        }
    }



    public function createTicket($invId, $ticketId, $first_name, $last_name)
    {
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
            'first_name' => $first_name,
            'last_name' => $last_name,
            'ticket_no' => $formattedTicketNo,
        ]);

        return $ticketGuest->ticket_no;
    }


    public function createOrderNumber()
    {
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

    public function expireInvoice($invoice_id)
    {
        $xendit_secret_key = env('XENDIT_SECRET_API_KEY'); // Store secret key in the .env file

        $client = new Client(['verify' => false]);

        try {
            $response = $client->post("https://api.xendit.co/invoices/{$invoice_id}/expire!", [
                'auth' => [$xendit_secret_key, ''], // Basic auth with API key
            ]);

            // Check if the response is successful
            if ($response->getStatusCode() === 200) {
                $responseData = json_decode($response->getBody(), true);
                $invoice = Invoice::where('xendit_invoice_no', $invoice_id)->first();
                if ($invoice) {
                    $invoice->status = $responseData['status'];
                    $invoice->save();
                }
                return response()->json([
                    'message' => 'Invoice expired successfully',
                    'data' => $responseData,
                ]);
            } else {
                return response()->json([
                    'message' => 'Failed to expire invoice',
                    'status' => $response->getStatusCode(),
                    'error' => json_decode($response->getBody(), true),
                ], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred while expiring the invoice',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Old Create Invoice with xendit payment
    // private function createInvoice($ticket, $userIds, $event) {
    //     $client = new Client(['verify' => false]);
    //     $apiSecretKey = env('XENDIT_SECRET_API_KEY');
    //     Log::info('TICKET' . $ticket);
    //     try {
    //         foreach ($userIds as $userId) {
    //         $user = User::where('id', $userId)->first();
    //         $response = $client->request('POST', 'https://api.xendit.co/v2/invoices', [
    //             'headers' => [
    //                 'Content-Type' => 'application/json',
    //             ],
    //             'auth' => [
    //                 $apiSecretKey,
    //                 '',
    //             ],
    //             'json' => [
    //                 'external_id' => $this->createOrderNumber(),
    //                 'amount' => $ticket->price,
    //                 'description' => 'Payment invoice for ' . $user->name . ' - Ticket Name: ' . $ticket->name,
    //                 'customer' => [
    //                     'email' => $user->email, // Payer email goes here
    //                 ],
    //                 'customer_notification_preference' => [
    //                     'invoice_paid' => ["email"]
    //                 ],
    //                 'invoice_duration' => 86400,
    //                 'success_redirect_url' => url('/ticket-success'),
    //                 'failure_redirect_url' => url('/ticket-error'),
    //                 'currency' => 'PHP'
    //             ]
    //         ]);

    //         $body = $response->getBody()->getContents();
    //         $data = json_decode($body, true);

    //             $inv = Invoice::create([
    //                 'user_id' => $userId,
    //                 'ticket_id' => $ticket->id,
    //                 'invoice_no' => $data['external_id'],
    //                 'xendit_id' => $data['user_id'], //xendit business ID / user_id
    //                 'xendit_invoice_no' => $data['id'], //xendit invoice ID
    //                 'description' => $data['description'],
    //                 'status' => $data['status'],
    //                 'amount' => $data['amount'],
    //                 'paid_amount' => null,
    //                 'currency' => $data['currency'],
    //                 'payer_email' => $user->email,
    //                 'payment_method' => null,
    //                 'bank_code' => null,
    //                 'payment_channel' => null,
    //                 'payment_destination' => null,
    //                 'expiry_date' => $data['expiry_date'],
    //                 'paid_at' => null,
    //                 'invoice_url' => $data['invoice_url']
    //             ]);

    //             $url = $data['invoice_url'];
    //             $amount = $data['amount'];
    //             $expiry_date = $data['expiry_date'];
    //             $invoice_no = $data['external_id'];
    //             $eventName = $event->title;
    //             $ticketName = $ticket->name;
    //             $name = $user->name;
    //             $toEmail = $user->email;
    //             InvoiceLog::create([
    //                 'invoice_id' => $inv->id,
    //                 'status' => $data['status'],
    //                 'amount' => $data['amount'],
    //                 'description' => 'Created invoice for ' . $user->name .  ' : Xendit',  // Correct string concatenation
    //                 'logged_at' => Carbon::now(),  // Using Carbon to set the current timestamp
    //             ]);
    //             Mail::to($toEmail)->send(new PaymentMail($url, $eventName, $ticketName, $name, $amount, $expiry_date, $invoice_no));
    //         }


    //     } catch (RequestException $e) {
    //         Log::error('Error creating invoice: ' . $e->getMessage());
    //         Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
    //         return ['error' => $e->getMessage()];
    //     }
    // }

    private function sendNotifiationMail($userIds, $event)
    {
        try {
            foreach ($userIds as $userId) {
                $user = User::where('id', $userId)->first();


                $url = url('/ticket');
                $eventName = $event->title;
                $eventStart = Carbon::parse($event->start)->format('F j, Y g:i A');
                $eventEnd = $event->end;
                $eventAddress = $event->address;

                $name = $user->name;
                $toEmail = $user->email;

                Attendance::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'event_name' => $event->title,
                    'time_in' => Carbon::now(),
                    'status' => 'Pending'
                ]);
                Mail::to($toEmail)->send(new InviteMail($eventName, $name, $eventStart, $eventEnd, $eventAddress, $url));
            }
        } catch (RequestException $e) {
            Log::error('Error creating invoice: ' . $e->getMessage());
            Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            return ['error' => $e->getMessage()];
        }
    }

    public function store(Request $request)
    {
        // Log::info($request->all());
        // //$userIds = $request->input('userId');
        // $groupId = $request->input('group_id');
        // $userIds = GroupUser::where('group_id', $groupId)->pluck('user_id');
        $ticket = Ticket::create([
            'ticket_code' => Str::random(10),
            'event_id' => $request->eventId,
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'description' => $request->description,
            'name' => $request->title,
            'price' => $request->price,
            'status' => 'Active'
        ]);
        // $event = Event::where('id', $request->eventId)->first();
        // if ($userIds) {
        //     $this->sendNotifiationMail($userIds, $event);
        // }

        //$tickets = Ticket::with('event', 'event.user')->where($ticket->id)->get();

        return redirect()->route('tickets.index')->with('success', 'Ticket: ' . $request->title . ' added successfully!');
    }



    /**
     * Display the specified ticket.
     */
    public function show($id)
    {
        $ticketId = $id;
        $users = User::all();
        $events = Event::all();
        return view('tickets.edit-ticket', ['users' => $users, 'events' => $events, 'ticketId' => $ticketId]);
    }

    /**
     * Show the form for editing the specified ticket.
     */
    public function edit($id)
    {
        $ticket = Ticket::find($id);
        Log::info($id);
        // Check if ticket exists
        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        return response()->json([
            'name' => $ticket->name,
            'description' => $ticket->description,
            'price' => $ticket->price,
            'start_date' => \Carbon\Carbon::parse($ticket->start_date)->format('Y-m-d'), // Converts to date only
            'end_date' => \Carbon\Carbon::parse($ticket->end_date)->format('Y-m-d'), // Converts to date only
            'eventId' => $ticket->event_id, // Adjust if your field name is different
        ]);
    }


    /**
     * Update the specified ticket in storage.
     */
    public function update(Request $request, $id)
    {
        // Update the ticket
        Log::info($id);
        $ticket = Ticket::findOrFail($id);
        $ticket->update([
            'name' => $request->input('title'),
            'description' => $request->input('description'),
            'start_date' => $request->input('startDate'),
            'end_date' => $request->input('endDate'),
            'event_id' => $request->input('eventId'), // Use the correct field name
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully.');
    }


    /**
     * Remove the specified ticket from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }


    private function createManualInvoiceXendit($ticket, $userIds, $event)
    {
        $client = new Client(['verify' => false]);
        $apiSecretKey = env('XENDIT_SECRET_API_KEY');
        Log::info('TICKET' . $ticket);
        try {
            $user = User::where('id', $userIds)->first();
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
                    'amount' => $ticket->price,
                    'description' => 'Payment invoice for ' . $user->name . ' - Ticket Name: ' . $ticket->name,
                    'invoice_duration' => 86400,
                    'success_redirect_url' => url('/ticket-success'),
                    'failure_redirect_url' => url('/ticket-error'),
                    'currency' => 'PHP'
                ]
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            $inv = Invoice::create([
                'user_id' => $userIds,
                'ticket_id' => $ticket->id,
                'invoice_no' => $data['external_id'],
                'xendit_id' => $data['user_id'], //xendit business ID / user_id
                'xendit_invoice_no' => $data['id'], //xendit invoice ID
                'description' => $data['description'],
                'status' => $data['status'],
                'amount' => $data['amount'],
                'paid_amount' => null,
                'currency' => $data['currency'],
                'payer_email' => null,
                'payment_method' => null,
                'bank_code' => null,
                'payment_channel' => null,
                'payment_destination' => null,
                'expiry_date' => $data['expiry_date'],
                'paid_at' => null,
                'invoice_url' => $data['invoice_url']
            ]);
            $url = $data['invoice_url'];
            $amount = $data['amount'];
            $expiry_date = $data['expiry_date'];
            $invoice_no = $data['external_id'];
            $eventName = $event->title;
            $ticketName = $ticket->name;
            $name = $user->name;
            $toEmail = $user->email;

            InvoiceLog::create([
                'invoice_id' => $inv->id,
                'status' => $data['status'],
                'amount' => $data['amount'],
                'description' => 'Created manual invoice for ' . $user->name .  ' : Xendit',  // Correct string concatenation
                'logged_at' => Carbon::now(),  // Using Carbon to set the current timestamp
            ]);
            Mail::to($toEmail)->send(new PaymentMail($url, $eventName, $ticketName, $name, $amount, $expiry_date, $invoice_no));
            return $data;
        } catch (RequestException $e) {
            Log::error('Error creating invoice: ' . $e->getMessage());
            Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            return ['error' => $e->getMessage()];
        }
    }

    private function createManualInvoiceCash($ticket, $userIds, $event)
    {
        try {
            $user = User::where('id', $userIds)->first();
            $invoice_no = $this->createOrderNumber();
            $inv = Invoice::create([
                'user_id' => $userIds,
                'ticket_id' => $ticket->id,
                'invoice_no' => $invoice_no,
                'xendit_id' => null, //xendit business ID / user_id
                'xendit_invoice_no' => null, //xendit invoice ID
                'description' => 'Cash Payment Option',
                'status' => 'PENDING',
                'amount' => $ticket->price,
                'paid_amount' => null,
                'currency' => 'PHP',
                'payer_email' => null,
                'payment_method' => 'CASH',
                'bank_code' => null,
                'payment_channel' => null,
                'payment_destination' => null,
                'expiry_date' => null,
                'paid_at' => null,
                'invoice_url' => null
            ]);

            InvoiceLog::create([
                'invoice_id' => $inv->id,
                'status' => $inv->status,
                'amount' => $inv->amount,
                'description' => 'Created manual invoice for ' . $user->name .  ' : Cash',  // Correct string concatenation
                'logged_at' => Carbon::now(),  // Using Carbon to set the current timestamp
            ]);
            // $url = $data['invoice_url'];
            // $amount = $data['amount'];
            // $expiry_date = $data['expiry_date'];
            // $invoice_no = $data['external_id'];
            // $eventName = $event->title;
            // $ticketName = $ticket->name;
            // $name = $user->name;
            // $toEmail = $user->email;
            // Mail::to($toEmail)->send(new PaymentMail($url, $eventName, $ticketName, $name, $amount, $expiry_date, $invoice_no));
            return $inv;
        } catch (RequestException $e) {
            Log::error('Error creating invoice: ' . $e->getMessage());
            Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            return ['error' => $e->getMessage()];
        }
    }

    public function store_invoice(Request $request)
    {
        Log::info($request->all());
        $userIds = $request->userId;
        $paymentOption = $request->input('paymentOption');
        $ticket = Ticket::where('id', $request->ticketId)->first();
        $event = Event::where('id', $ticket->event_id)->first();
        if ($paymentOption === "cash") {
            $this->createManualInvoiceCash($ticket, $userIds, $event);
        } elseif ($paymentOption === "xendit") {
            $this->createManualInvoiceXendit($ticket, $userIds, $event);
        } else {
            return redirect()->back()->with('error', 'No payment option selected');
        }

        return redirect()->back()->with('success', 'Invoice Created Successfully');
    }

    public function show_invoice($id)
    {
        $invoice = Invoice::with('ticket', 'user', 'ticket.event')->find($id);

        if ($invoice) {
            return response()->json($invoice);
        } else {
            return response()->json(['error' => 'Subscription not found.'], 404);
        }
    }

    public function update_invoice(Request $request, $id)
    {
        try {
            $invoice = Invoice::where('id', $id)->first();
            Log::info($invoice);
            $invoice->update([
                'paid_amount' => $request->input('paid_amount') ?? $invoice->amount,
                'status' => $request->input('status')
            ]);
            InvoiceLog::create([
                'invoice_id' => $id,
                'status' => $request->input('status'),
                'amount' => $request->input('paid_amount') ?? $invoice->amount,
                'description' => 'Update Invoice #: ' . $invoice->invoice_no .  ' : Bank Transfer',  // Correct string concatenation
                'logged_at' => Carbon::now(),  // Using Carbon to set the current timestamp
            ]);
            $attendees = json_decode($invoice->attendees, true);
            $argValues = config('advanced-config.qr_code_gradient') ?? [0, 0, 0, 0, 0, 0, 'diagonal'];
            list($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7) = $argValues;

            $tickets = Ticket::with('event')->where('id', $invoice->ticket_id)->first();
            $start = Carbon::parse($tickets->event->start);
            $end = Carbon::parse($tickets->event->end);
            // Prepare ticket data
            $ticketData = [];
            for ($i = 1; $i <= $invoice->quantity; $i++) {
                $attendee = $attendees[$i - 1] ?? ['firstName' => 'Guest', 'lastName' => ''];
                $ticket_no = $this->createTicket($invoice->id, $invoice->ticket_id, $attendee['firstName'], $attendee['lastName']);   // Create unique ticket numbers

                try {
                    if (extension_loaded('imagick')) {
                        $imgSrc = QrCode::format('png')
                            ->gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                            ->eye('circle')
                            ->style('round')
                            ->size(300)
                            ->generate($ticket_no);
                        $imgSrc = base64_encode($imgSrc);
                        $imgSrc = 'data:image/png;base64,' . $imgSrc;
                    } else {
                        $imgSrc = QrCode::gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                            ->eye('circle')
                            ->style('round')
                            ->size(300)
                            ->generate($ticket_no);
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
                    'ticket_no' => $ticket_no,
                    'qr_code' => $imgSrc,
                    'attendee_name' => $attendee['firstName'] . ' ' . $attendee['lastName']
                ];
                Log::info($data);
                $ticketData[] = $data;  // Collect ticket data
            }

            $name = $invoice->first_name . ' ' . $invoice->last_name;

            $pdf = Pdf::loadView('tickets.pdf.ticket', ['tickets' => $ticketData]);
            $filePath = storage_path('app/public/event_ticket_' . $invoice->id . '.pdf');
            Log::info('File PATH: ' . $filePath);
            $fileName = "Ticket for " . $invoice->first_name . "_" . $invoice->last_name;
            $pdf->save($filePath);  // Save the PDF to a file
            Mail::to($invoice->email)->send(new TicketMail($name, $filePath, $fileName));
            return redirect()->back()->with('success', 'Invoice updated successfuly!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong while updating record');
        }
    }
}
