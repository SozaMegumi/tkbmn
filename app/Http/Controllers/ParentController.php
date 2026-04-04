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

class ParentController extends Controller
{
    // ==========================================
    // 1. DASHBOARD PAGE
    // ==========================================
    public function dashboard()
    {
        $parent = Auth::guard('parent')->user();
        
        // 1. Fetch Child Info
        $student = Student::where('parent_id', $parent->parent_id)->first();

        // Safety check
        if(!$student) {
            return view('parent.dashboard', [
                'parent' => $parent, 'student' => null, 'fees' => 0.00, 
                'attendance' => 0, 'notices' => [], 'teacher' => null, 'latestMsg' => null
            ]);
        }

        // 2. Calculate Outstanding Fees (Real DB Query)
        $fees = Payment::where('student_id', $student->student_id)
                       ->where('status', 'Unpaid')
                       ->sum('amount');

        // 3. Calculate Attendance % (Real DB Query)
        $totalDays = Attendance::where('student_id', $student->student_id)->count();
        $presentDays = Attendance::where('student_id', $student->student_id)
                                 ->where('status', 'Present') 
                                 ->count();
                                 
        $attendance = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        // 4. Get Class Teacher (Primary Logic)
        $teacher = null;
        if($student->class_id) {
            $classroom = Classroom::find($student->class_id);
            if($classroom && $classroom->teacher_id) {
                $teacher = Teacher::where('teacher_id', $classroom->teacher_id)->first();
            }
        }

        // 5. Fetch Latest 3 Announcements
        $notices = Event::latest()->take(3)->get();

        // 6. Mini Chat Preview (Smart Logic)
        // Try to find the latest message from ANY teacher
        $latestMsg = Message::where(function($q) use ($parent) {
            $q->where('sender_id', $parent->parent_id)->where('sender_type', 'App\Models\Guardian')
              ->where('receiver_type', 'App\Models\Teacher');
        })->orWhere(function($q) use ($parent) {
            $q->where('receiver_id', $parent->parent_id)->where('receiver_type', 'App\Models\Guardian')
              ->where('sender_type', 'App\Models\Teacher');
        })->latest()->first();

        // If we found a message but don't have a class teacher assigned yet,
        // use the teacher from the message so the dashboard isn't empty.
        if (!$teacher && $latestMsg) {
             $teacherId = ($latestMsg->sender_type == 'App\Models\Teacher') 
                        ? $latestMsg->sender_id 
                        : $latestMsg->receiver_id;
             $teacher = Teacher::where('teacher_id', $teacherId)->first();
        }

        return view('parent.dashboard', compact('parent', 'student', 'fees', 'attendance', 'notices', 'teacher', 'latestMsg'));
    }

    // ==========================================
    // 2. CHAT PAGE
    // ==========================================
    public function chat(Request $request) {
        $parent = Auth::guard('parent')->user();
        
        // A. Get List of Teachers
        $teachers = Teacher::all(); 

        // B. Determine Active Teacher
        $teacherId = $request->get('teacher_id', $teachers->first()->getKey() ?? null);
        $activeTeacher = $teachers->where('teacher_id', $teacherId)->first();

        // C. Fetch Conversation
        $messages = [];
        if($activeTeacher) {
            $messages = Message::conversation(
                $parent->getKey(), 'App\Models\Guardian',          
                $activeTeacher->getKey(), 'App\Models\Teacher'     
            )
            ->orderBy('created_at', 'asc')
            ->get();
        }

        return view('parent.chat', compact('teachers', 'activeTeacher', 'messages'));
    }

    // ==========================================
    // 3. SEND MESSAGE FUNCTION
    // ==========================================
    public function sendMessage(Request $request) {
        $request->validate([
            'receiver_id' => 'required',
            'message'     => 'required|string'
        ]);

        $parent = Auth::guard('parent')->user();

        // Create the message
        Message::create([
            'sender_id'       => $parent->getKey(), 
            'sender_type'     => 'App\Models\Guardian', 
            
            'receiver_id'     => $request->receiver_id,
            'receiver_type'   => 'App\Models\Teacher', 
            
            'message_content' => $request->message,
            'read_at'         => null
        ]);

        return back(); 
    }

    // ==========================================
    // 4. FULL NOTICES PAGE
    // ==========================================
    public function notices() {
        $notices = Event::orderBy('date', 'desc')->paginate(10);
        return view('parent.notices', compact('notices'));
    }

    // ==========================================
    // 5. PAYMENT PAGE (NOW CONNECTED TO DB)
    // ==========================================
    public function payment() {
        $parent = Auth::guard('parent')->user();
        
        // 1. Fetch Child
        $student = Student::where('parent_id', $parent->parent_id)->first();

        if(!$student) {
            return view('parent.payment', ['pendingInvoices' => [], 'paymentHistory' => []]);
        }

        // 2. Fetch Pending Invoices (Real DB Data)
        $pendingInvoices = Payment::where('student_id', $student->student_id)
                                  ->where('status', 'Unpaid')
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        // 3. Fetch Payment History (Real DB Data)
        $paymentHistory = Payment::where('student_id', $student->student_id)
                                 ->where('status', 'Paid')
                                 ->orderBy('payment_date', 'desc')
                                 ->get();

        return view('parent.payment', compact('pendingInvoices', 'paymentHistory'));
    }
}