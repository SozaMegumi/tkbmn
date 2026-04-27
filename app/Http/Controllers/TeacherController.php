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
use Carbon\Carbon;

class TeacherController extends Controller
{
    // ==========================================
    // DASHBOARD
    // ==========================================
    public function dashboard() {
        try {
            // 1. Get Logged in Teacher with strict guard check
            $teacher = Auth::guard('teacher')->user(); 

            if (!$teacher) {
                return redirect()->route('login');
            }

            // Initialize defaults
            $assignedClass = 'No Class Assigned';
            $totalStudents = 0;
            $attendanceMarked = false;
            $unreadMessages = 0;

            // 2. Find the teacher's specific assigned class
            $classroom = Classroom::where('teacher_id', $teacher->teacher_id)->first();

            if ($classroom) {
                $assignedClass = $classroom->class_name;
                $totalStudents = Student::where('class_id', $classroom->class_id)->count();

                // 3. Check if attendance was marked TODAY (UTC+8 Safe)
                $attendanceMarked = Attendance::where('class_id', $classroom->class_id)
                    ->whereDate('date', Carbon::today('Asia/Kuala_Lumpur'))
                    ->exists();
            }

            // 4. Count unread messages for this specific teacher
            $unreadMessages = Message::where('receiver_id', $teacher->teacher_id)
                ->where('receiver_type', 'App\Models\Teacher')
                ->whereNull('read_at')
                ->count();

        } catch (\Exception $e) {
            $assignedClass = 'System Error';
            $totalStudents = 0;
            $attendanceMarked = false;
            $unreadMessages = 0;
        }

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
        $teacher = Auth::guard('teacher')->user();
        if (!$teacher) return redirect()->route('login');

        $classes = Classroom::all();
        $selectedClass = null;
        // Default to today in UTC+8 string format
        $selectedDate = $request->get('attendance_date', Carbon::today('Asia/Kuala_Lumpur')->toDateString());
        $students = [];

        if ($request->has('class_id')) {
            $classId = $request->class_id;
            $selectedClass = Classroom::find($classId);

            $students = Student::where('class_id', $classId)
                ->get()
                ->map(function($student) use ($selectedDate) {
                    $attendance = Attendance::where('student_id', $student->student_id)
                        ->where('date', $selectedDate)
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
            // Logical Check-in/Out Tally
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'date' => $request->attendance_date],
                [
                    'class_id' => $request->class_id, 
                    'status'   => $data['status'], 
                    'reason'   => $data['reason'] ?? null
                ]
            );
        }

        return back()->with('success', 'Attendance records updated successfully!');
    }

    // ==========================================
    // GRADING
    // ==========================================
    public function grading() {
        $teacher = Auth::guard('teacher')->user();
        if (!$teacher) return redirect()->route('login');

        // Logic: Focus on the teacher's own students
        $classroom = Classroom::where('teacher_id', $teacher->teacher_id)->first();
        $students = $classroom ? Student::where('class_id', $classroom->class_id)->get() : collect();

        return view('teacher.grading', compact('students'));
    }

    public function storeGrade(Request $request) {
    // Validate according to DFD Process 5.0 requirements
    $request->validate([
        'student_id'    => 'required|exists:students,student_id',
        'subject_id'    => 'required|exists:subjects,subject_id',
        'academic_year' => 'required',
        'marks'         => 'required|numeric|min:0|max:100',
        'hafazan_surah' => 'nullable|string', // Added for kindergarten logic
    ]);

    // 1. Store Academic Marks (Process 5.0: Assessment Management)
    \App\Models\AssessmentResult::updateOrCreate(
        [
            'student_id' => $request->student_id, 
            'subject_id' => $request->subject_id,
            'academic_year_id' => $request->academic_year
        ],
        [
            'marks' => $request->marks,
            'teacher_id' => Auth::guard('teacher')->id()
        ]
    );

    // 2. Store Hafazan/Academic Record (Process 5.0: Record Tallying)
    if ($request->hafazan_surah) {
        \App\Models\HafazanRecord::create([
            'student_id' => $request->student_id,
            'surah_name' => $request->hafazan_surah,
            'status'     => 'Completed',
            'recorded_at' => now()
        ]);
    }

    return back()->with('success', 'Academic and Hafazan records updated successfully.');
}

    
    // ==========================================
    // COMMUNICATION (Chat)
    // ==========================================
    public function communication(Request $request) {
        $teacher = Auth::guard('teacher')->user(); 
        if (!$teacher) return redirect()->route('login');

        $parents = Guardian::orderBy('parent_name', 'asc')->get(); 
        $parentId = $request->get('parent_id', $parents->first()->parent_id ?? null);
        $activeParent = $parents->where('parent_id', $parentId)->first();

        $messages = [];
        if($activeParent) {
            // Mark incoming messages as read when viewing
            Message::where('sender_id', $activeParent->parent_id)
                ->where('receiver_id', $teacher->teacher_id)
                ->where('receiver_type', 'App\Models\Teacher')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

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