<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Attendance;
use App\Models\Invoice;
use App\Models\Event;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketGuest;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Illuminate\Support\Facades\Log;
class AttendanceForm extends Component
{
    public $rfid_no;
    public $eventTitles;
    public $event;

    protected $rules = [
        'rfid_no' => 'required|string', // Ensure RFID is at least 10 characters
    ];

    public function updatedRfidNo($value)
    {
        if (strlen($value) >= 8) {
            $this->submitAttendance();
        }
    }

    public function mount($event)
    {
        $this->event = $event;
    }

    // Add this method to handle the event_title update
    public function updatedEventTitle($value)
    {
        $this->rfid_no = ''; // Reset rfid_no when event_title changes
    }

    public function submitAttendance()
    {
        try {
            $this->validate();

            $events = Event::where('title', $this->event)->first();
            $user = User::where('rfid_no', $this->rfid_no)->first();

            if (!$user) {
                session()->flash('event', $events->title);
                return redirect()->route('attendance.error');
            }
    
            if (filter_var($this->rfid_no, FILTER_VALIDATE_URL)) {
                // Extract the user ID from the URL (
                $userCode = basename($this->rfid_no);
            
                // Find the user using the extracted user ID
                $userUrl = User::where('activate_code', $userCode)
                ->orWhere('id', $userCode)
                ->first();
            
                if (!$user) {
                    session()->flash('error', 'User not found. Please Contact admin/helpdesk.');
                    session()->flash('event', $events->title);
                    return redirect()->route('attendance.error');
                }
                $attendance = Attendance::where('user_id', $userUrl->id)
                ->where('event_id', $events->id)
                ->first();
                Log::info($attendance);
                if (!$attendance) {
                    session()->flash('error', 'You are not part of the event. Please contact the support or admin for inquiries.');
                    session()->flash('event', $events->title);
                    return redirect()->route('attendance.error');
                }
    
                if ($attendance && $attendance->status === 'Present') {
                    session()->flash('error', 'You have already submitted attendance for this event.');
                    session()->flash('event', $events->title);
                    return redirect()->route('attendance.error');
                }
                $attendance->time_in = now();
                $attendance->status = 'Present';
                $attendance->save();

                // $connector = new WindowsPrintConnector("XP-58C");
                // $printer = new Printer($connector);
                // $printer->text("Hello World\n");
                // $printer->cut();
                // $printer->close();
                
                session()->flash('event', $events->title);
                session()->flash('name', $userUrl->name);
                return redirect()->route('attendance.success');
            } else if (substr($this->rfid_no, 0, 3) === 'TCK') {
                // Handle the case where ticketNo is a regular ticket number
                $ticketGuest = TicketGuest::where('ticket_no', $this->rfid_no)->first();
                $ticketList = Ticket::where('id', $ticketGuest->ticket_id)->first();
    
    
                if ($ticketList->event_id !== (int) $events->id) {
                    session()->flash('error', 'Your ticket is not part of this event. Please contact admin/helpdesk for inquiries.');
                    session()->flash('event', $events->title);
                    return redirect()->route('attendance.error');
                }
    
    
                if ($ticketGuest) {
                    // Check if the ticket has already been scanned
                    if (!$ticketGuest->is_scanned) {
                        // Update the is_scanned field to true
                        $ticketGuest->is_scanned = 1;
                        $ticketGuest->save();
    
                        session()->flash('event', $events->title);
                        return redirect()->route('attendance.success');
                    } else {
                        session()->flash('error', 'You are already checked in.');
                        session()->flash('event', $events->title);
                        return redirect()->route('attendance.error');
                    }
                }
    
                session()->flash('error', 'Ticket not found.');
                session()->flash('event', $events->title);
                return redirect()->route('attendance.error');
            } else {
                if ($user && $events) {
                    $existingAttendance = Attendance::where('user_id', $user->id)
                                                    ->where('event_id', $events->id)
                                                    ->first();
                    if (!$existingAttendance) {
                        session()->flash('error', 'You are not part of the event. Please contact the support or admin for inquiries.');
                        session()->flash('event', $events->title);
                        return redirect()->route('attendance.error');
                    }
        
                    if ($existingAttendance->event_name !== $this->event) {
                        session()->flash('error', 'You are not part of the event. Please contact the support or admin for inquiries.');
                        session()->flash('event', $events->title);
                        return redirect()->route('attendance.error');
                    }
        
                    if ($existingAttendance->status === 'Present') {
                        session()->flash('error', 'You have already submitted attendance for this event.');
                        session()->flash('event', $events->title);
                        return redirect()->route('attendance.error');
                    } else {
                        $existingAttendance->time_in = now();
                        $existingAttendance->status = 'Present';
                        $existingAttendance->save();
                        
                        session()->flash('event', $events->title);
                        session()->flash('name', $user->name);
                        return redirect()->route('attendance.success');
                    }
                } else {
                    session()->flash('error', 'Invalid RFID or Event.');
                    session()->flash('event', $events->title);
                    return redirect()->route('attendance.error');
                }
        
                $this->reset('rfid_no');
            }
    
            
        } catch (\Throwable $th) {
            Log::error('Error occurred: ' . $th->getMessage(), [
                'exception' => $th,
                'event_title' => $events->title,
                'rfid_no' => $this->rfid_no, // Include other relevant data if needed
            ]);
            session()->flash('error', 'Something went wrong. Please try scanning again or refresh the page.');
            session()->flash('event', $events->title);
            return redirect()->route('attendance.error');
            $this->reset('rfid_no');
        }

       
    }

    public function render()
    {
        return view('livewire.attendance-form');
    }
}