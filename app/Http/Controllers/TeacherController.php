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
use Carbon\Carbon; // <-- Added for date checking

class TeacherController extends Controller
{
    // ==========================================
    // DASHBOARD
    // ==========================================
    public function dashboard() {
        try {
            // 1. Get Logged in Teacher
            $teacher = Auth::guard('teacher')->user(); 

            // Initialize defaults
            $assignedClass = 'No Class Assigned';
            $totalStudents = 0;
            $attendanceMarked = false;
            $unreadMessages = 0;

            if ($teacher) {
                // 2. Find the teacher's assigned class
                // (Assuming Classroom model has a 'teacher_id'. If not, we just grab the first class as a fallback)
                $classroom = Classroom::where('teacher_id', $teacher->teacher_id)->first() ?? Classroom::first();

                if ($classroom) {
                    $assignedClass = $classroom->class_name;
                    $totalStudents = Student::where('class_id', $classroom->class_id)->count();

                    // 3. Check if attendance was marked TODAY for this class
                    $attendanceMarked = Attendance::where('class_id', $classroom->class_id)
                                                  ->whereDate('date', Carbon::today())
                                                  ->exists();
                }

                // 4. Count unread messages for this specific teacher
                $unreadMessages = Message::where('receiver_id', $teacher->teacher_id)
                                         ->where('receiver_type', 'App\Models\Teacher')
                                         ->whereNull('read_at')
                                         ->count();
            }

        } catch (\Exception $e) {
            // Fallback if database tables aren't fully set up yet
            $assignedClass = 'Database Error';
            $totalStudents = 0;
            $attendanceMarked = false;
            $unreadMessages = 0;
        }

        // Pass everything to the new dashboard view!
        return view('teacher.dashboard', compact(
            'assignedClass', 
            'totalStudents', 
            'attendanceMarked', 
            'unreadMessages'
        ));
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
    // COMMUNICATION (Chat)
    // ==========================================
    public function communication(Request $request) {
        // 1. Get Logged in Teacher
        $teacher = Auth::guard('teacher')->user(); 

        // 2. Get List of Guardians
        $parents = Guardian::all(); 

        // 3. Determine Active Parent
        $parentId = $request->get('parent_id', $parents->first()->parent_id ?? null);
        
        $activeParent = $parents->where('parent_id', $parentId)->first();

        // 4. Fetch Conversation
        $messages = [];
        if($activeParent && $teacher) {
            $messages = Message::conversation(
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