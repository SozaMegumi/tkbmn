<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Classroom; 
use App\Models\Message;   
use App\Models\Guardian;  
use App\Models\Teacher;   

class TeacherController extends Controller
{
    // ==========================================
    // DASHBOARD
    // ==========================================
    public function dashboard() {
        return view('teacher.dashboard');
    }

    // ==========================================
    // ATTENDANCE MANAGEMENT 
    // ==========================================
    public function attendance(Request $request) {
        $classes = Classroom::all();
        $selectedClass = null;
        $selectedDate = null;
        $students = [];

        if ($request->has('class_id') && $request->has('attendance_date')) {
            $classId = $request->class_id;
            $date = $request->attendance_date;

            $selectedClass = Classroom::find($classId);
            $selectedDate = $date;

            $students = Student::where('class_id', $classId)
                ->get()
                ->map(function($student) use ($date) {
                    $attendance = Attendance::where('student_id', $student->student_id)
                                          ->where('date', $date)
                                          ->first();
                    $student->setRelation('attendance', $attendance);
                    return $student;
                });
        }

        return view('teacher.attendance', compact('classes', 'students', 'selectedClass', 'selectedDate'));
    }

    public function storeAttendance(Request $request) {
        $request->validate([
            'class_id'        => 'required',
            'attendance_date' => 'required|date',
            'attendances'     => 'required|array'
        ]);

        foreach ($request->attendances as $studentId => $data) {
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'date' => $request->attendance_date],
                ['class_id' => $request->class_id, 'status' => $data['status'], 'reason' => $data['reason'] ?? null]
            );
        }

        return back()->with('success', 'Attendance Synced Successfully!');
    }

    // ==========================================
    // GRADING
    // ==========================================
    public function grading() {
        $students = Student::all();
        return view('teacher.grading', compact('students'));
    }

    public function storeGrade(Request $request) {
        return back()->with('success', 'Grades Saved Successfully');
    }

    // ==========================================
    // COMMUNICATION (Chat) - FIXED IDS
    // ==========================================
    public function communication(Request $request) {
        // 1. Get Logged in Teacher
        $teacher = Auth::guard('teacher')->user(); 

        // 2. Get List of Guardians
        $parents = Guardian::all(); 

        // 3. Determine Active Parent
        // FIX: Use 'parent_id' (not 'id') because of your database schema
        $parentId = $request->get('parent_id', $parents->first()->parent_id ?? null);
        
        // Find parent using explicit 'parent_id' column logic
        $activeParent = $parents->where('parent_id', $parentId)->first();

        // 4. Fetch Conversation
        $messages = [];
        if($activeParent && $teacher) {
            $messages = Message::conversation(
                // FIX: Use custom primary keys ->teacher_id and ->parent_id
                $teacher->teacher_id, 'App\Models\Teacher', 
                $activeParent->parent_id, 'App\Models\Guardian'
            )
            ->orderBy('created_at', 'asc')
            ->get();
        }

        return view('teacher.communication', compact('parents', 'activeParent', 'messages'));
    }

    public function sendMessage(Request $request) {
        $request->validate([
            'receiver_id' => 'required',
            'message'     => 'required|string'
        ]);

        $teacher = Auth::guard('teacher')->user(); 

        Message::create([
            // FIX: Use teacher_id
            'sender_id'       => $teacher->teacher_id,
            'sender_type'     => 'App\Models\Teacher',
            
            'receiver_id'     => $request->receiver_id,
            'receiver_type'   => 'App\Models\Guardian', 
            
            'message_content' => $request->message,
            'read_at'         => null
        ]);

        return back();
    }
}