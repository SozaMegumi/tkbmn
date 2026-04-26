<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Event;     
use App\Models\Student;   
use App\Models\Message;   
use App\Models\Teacher; 
use App\Models\Guardian; 
use App\Models\Payment;    
use App\Models\Attendance; 
use App\Models\Classroom;  
use Carbon\Carbon;

class ParentController extends Controller
{
    // ==========================================
    // 1. DASHBOARD PAGE
    // ==========================================
    public function dashboard()
    {
        $parent = Auth::guard('parent')->user();
        
        // Safety check to prevent portal leaking
        if (!$parent) {
            return redirect()->route('login');
        }

        // 1. Fetch Child Info
        $student = Student::where('parent_id', $parent->parent_id)->first();

        // Safety check if no student assigned
        if(!$student) {
            return view('parent.dashboard', [
                'parent' => $parent, 'student' => null, 'fees' => 0.00, 
                'attendance' => 0, 'notices' => [], 'teacher' => null, 'latestMsg' => null
            ]);
        }

        // 2. Calculate Outstanding Fees
        $fees = Payment::where('student_id', $student->student_id)
                       ->where('status', 'Unpaid')
                       ->sum('amount');

        // 3. Calculate Attendance %
        $totalDays = Attendance::where('student_id', $student->student_id)->count();
        $presentDays = Attendance::where('student_id', $student->student_id)
                                 ->where('status', 'Present') 
                                 ->count();
                                 
        $attendance = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        // 4. Get Class Teacher
        $teacher = null;
        if($student->class_id) {
            $classroom = Classroom::find($student->class_id);
            if($classroom && $classroom->teacher_id) {
                $teacher = Teacher::where('teacher_id', $classroom->teacher_id)->first();
            }
        }

        // 5. Fetch Latest 3 Announcements
        $notices = Event::latest()->take(3)->get();

        // 6. Mini Chat Preview Logic
        $latestMsg = Message::where(function($q) use ($parent) {
            $q->where('sender_id', $parent->parent_id)->where('sender_type', 'App\Models\Guardian')
              ->where('receiver_type', 'App\Models\Teacher');
        })->orWhere(function($q) use ($parent) {
            $q->where('receiver_id', $parent->parent_id)->where('receiver_type', 'App\Models\Guardian')
              ->where('sender_type', 'App\Models\Teacher');
        })->latest()->first();

        return view('parent.dashboard', compact('parent', 'student', 'fees', 'attendance', 'notices', 'teacher', 'latestMsg'));
    }

    // ==========================================
    // 2. CHAT PAGE
    // ==========================================
    public function chat(Request $request) {
        $parent = Auth::guard('parent')->user();
        $teachers = Teacher::all(); 
        $teacherId = $request->get('teacher_id', $teachers->first()->teacher_id ?? null);
        $activeTeacher = $teachers->where('teacher_id', $teacherId)->first();

        $messages = [];
        if($activeTeacher) {
            $messages = Message::conversation(
                $parent->parent_id, 'App\Models\Guardian',          
                $activeTeacher->teacher_id, 'App\Models\Teacher'     
            )
            ->orderBy('created_at', 'asc')
            ->get();
        }

        return view('parent.chat', compact('teachers', 'activeTeacher', 'messages'));
    }

    // ==========================================
    // 3. SEND MESSAGE
    // ==========================================
    public function sendMessage(Request $request) {
        $request->validate([
            'receiver_id' => 'required',
            'message'     => 'required|string'
        ]);

        $parent = Auth::guard('parent')->user();

        Message::create([
            'sender_id'       => $parent->parent_id, 
            'sender_type'     => 'App\Models\Guardian', 
            'receiver_id'     => $request->receiver_id,
            'receiver_type'   => 'App\Models\Teacher', 
            'message_content' => $request->message,
            'read_at'         => null
        ]);

        return back(); 
    }

    // ==========================================
    // 4. NOTICES PAGE
    // ==========================================
    public function notices() {
        $notices = Event::orderBy('start_date', 'desc')->paginate(10);
        return view('parent.notices', compact('notices'));
    }

    // ==========================================
    // 5. PAYMENT PAGE 
    // ==========================================
    public function payment() {
        $parent = Auth::guard('parent')->user();
        $student = Student::where('parent_id', $parent->parent_id)->first();

        if(!$student) {
            return view('parent.payment', ['pendingInvoices' => [], 'paymentHistory' => []]);
        }

        $pendingInvoices = Payment::where('student_id', $student->student_id)
                                  ->where('status', 'Unpaid')
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        $paymentHistory = Payment::where('student_id', $student->student_id)
                                 ->where('status', 'Paid')
                                 ->orderBy('created_at', 'desc') 
                                 ->get();

        return view('parent.payment', compact('pendingInvoices', 'paymentHistory'));
    }

    // ==========================================
    // 6. UPLOAD RECEIPT
    // ==========================================
    public function uploadReceipt(Request $request) {
        $request->validate([
            'amount' => 'required|numeric',
        ]);
        
        return back()->with('success', 'Receipt uploaded successfully! Waiting for Admin approval.');
    }

    // ==========================================
    // 7. EVENTS CALENDAR (FIXED FOR UTC+8 TALLY)
    // ==========================================
    public function events() {
        // Fetch and normalize dates to prevent UTC+8 timezone shifts in JS
        $upcomingEvents = Event::where('start_date', '>=', now()->startOfDay())
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function($event) {
                return [
                    'title' => $event->title,
                    'description' => $event->description,
                    // Force simple string date so JS doesn't shift it
                    'date_only' => Carbon::parse($event->start_date)->toDateString(),
                    'theme' => $event->theme,
                    'start_date_raw' => $event->start_date 
                ];
            });

        $allNotices = Event::latest()->paginate(5);

        return view('parent.events', compact('upcomingEvents', 'allNotices'));
    }
}