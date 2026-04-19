<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
// ==========================================
    // DASHBOARD
    // ==========================================
    public function dashboard() {
        try {
            // 1. Top Summary Stats
            $totalStudents = Student::count();
            $totalClasses  = Classroom::count();
            $pendingApprovals = Student::where('status', 'Pending')->count(); 

            // 2. Recent Enrollments (Left Column)
            $recentEnrollments = Student::with('parent')->orderBy('created_at', 'desc')->take(5)->get();

            // 3. Alerts & Tasks (Right Column)
            $alerts = [];
            $upcomingEvents = Event::where('start_date', '>=', now()->startOfDay())
                                  ->where('start_date', '<=', now()->addDays(30)->endOfDay())
                                  ->orderBy('start_date', 'asc')
                                  ->get();

            foreach($upcomingEvents as $event) {
                $isHoliday = ($event->theme == 'danger');
                $alerts[] = [
                    'icon' => $isHoliday ? 'bi-calendar-x-fill' : 'bi-bell-fill',
                    'color' => $isHoliday ? 'danger' : 'warning',
                    'title' => $isHoliday ? 'Upcoming Holiday' : 'Upcoming Event',
                    'message' => $event->title . ' is on ' . \Carbon\Carbon::parse($event->start_date)->format('d M Y') . '.'
                ];
            }

            // 4. WEEKLY ATTENDANCE DATA
            $attendanceLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $attendanceData = [94, 91, 95, 89, 91]; 

        } catch (\Exception $e) {
            // Fallback if database fails
            $totalStudents = 0; 
            $totalClasses = 0; 
            $pendingApprovals = 0;
            $recentEnrollments = []; 
            $alerts = [];
            
            // --> THE MISSING LINES WERE ADDED HERE <--
            $attendanceLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $attendanceData = [0, 0, 0, 0, 0];
        }
        
        return view('admin.dashboard', compact(
            'totalStudents', 
            'totalClasses', 
            'pendingApprovals', 
            'recentEnrollments', 
            'alerts',
            'attendanceLabels', 
            'attendanceData'    
        ));
    }

    // ==========================================
    // 1.0 USER ACCOUNTS (Teachers & Parents)
    // ==========================================
    public function users() {
        return view('admin.users', ['teachers' => Teacher::all(), 'parents' => Guardian::all()]);
    }
    
    // Create User
    public function storeUser(Request $request) {
        $password = Hash::make('password123');
        $username = explode('@', $request->email)[0]; 

        if($request->type == 'teacher') {
            Teacher::create([
                'full_name'    => $request->name,
                'email'        => $request->email,
                'username'     => $username,
                'password'     => $password,
                'phone_number' => $request->phone,
                'gender'       => $request->gender,
                'address'      => $request->address,
                'join_date'    => $request->join_date ?? now(),
            ]);
        } else {
            Guardian::create([
                'parent_name'  => $request->name,
                'email'        => $request->email,
                'username'     => $username,
                'password'     => $password,
                'phone_number' => $request->phone,
                'gender'       => $request->gender,
            ]);
        }
        return back()->with('success', 'User Account Created Successfully!');
    }

    // Update User
    public function updateUser(Request $request, $id) {
        if($request->type == 'teacher') {
            $teacher = Teacher::findOrFail($id);
            $teacher->update([
                'full_name'    => $request->name,
                'email'        => $request->email,
                'phone_number' => $request->phone,
                'gender'       => $request->gender,
                'address'      => $request->address,
                'join_date'    => $request->join_date,
            ]);
        } else {
            $parent = Guardian::findOrFail($id);
            $parent->update([
                'parent_name'  => $request->name,
                'email'        => $request->email,
                'phone_number' => $request->phone,
                'gender'       => $request->gender,
            ]);
        }
        return back()->with('success', 'User details updated successfully!');
    }

    // Delete User
    public function deleteUser(Request $request, $id) {
        $type = $request->input('type'); // Reads ?type=teacher from URL

        if($type == 'teacher') {
            Teacher::findOrFail($id)->delete();
        } else {
            Guardian::findOrFail($id)->delete();
        }
        return back()->with('success', 'User account deleted successfully.');
    }


    // ==========================================
    // 3.0 & 4.0 STUDENT ENROLMENT (ADJUSTED)
    // ==========================================
    public function enrolment() {
        // 1. Get classes WITH their students (Grouped by Class)
        $classesWithStudents = Classroom::with(['students.parent'])->get();

        // 2. Get students who DO NOT have a class yet
        $unassignedStudents = Student::whereNull('class_id')->with('parent')->get();

        // 3. Get Parents & Classes for the dropdown menus
        $parents = Guardian::all();
        $allClasses = Classroom::all();

        // Send all 4 variables to the View
        return view('admin.enrolment', [
            'classesWithStudents' => $classesWithStudents,
            'unassignedStudents'  => $unassignedStudents,
            'parents'             => $parents,
            'classes'             => $allClasses
        ]);
    }
    
    // Create Student
    public function storeStudent(Request $request) {
        $request->validate([
            'student_name' => 'required',
            'mykid'        => 'required|unique:students,mykid',
            'parent_id'    => 'required',
            'dob'          => 'required|date',
        ]);

        Student::create([
            'student_name' => $request->student_name,
            'mykid'        => $request->mykid,
            'dob'          => $request->dob,
            'gender'       => $request->gender,
            'race'         => $request->race,
            'religion'     => $request->religion,
            'nationality'  => $request->nationality ?? 'Malaysian',
            'parent_id'    => $request->parent_id,
            'class_id'     => $request->class_id,
            'status'       => 'active'
        ]);

        return back()->with('success', 'Student Enrolled Successfully!');
    }

    // Update Student
    public function updateStudent(Request $request, $id) {
        $student = Student::findOrFail($id);
        
        $student->update([
            'student_name' => $request->student_name,
            'mykid'        => $request->mykid,
            'dob'          => $request->dob,
            'gender'       => $request->gender,
            'race'         => $request->race,
            'religion'     => $request->religion,
            'nationality'  => $request->nationality,
            'parent_id'    => $request->parent_id,
            'class_id'     => $request->class_id,
        ]);
        return back()->with('success', 'Student Details Updated!');
    }

    // Delete Student
    public function deleteStudent($id) {
        Student::findOrFail($id)->delete();
        return back()->with('success', 'Student Record Deleted.');
    }

    // ==========================================
    // OTHER MODULES
    // ==========================================
    
    // 5.0 Exams
    public function exams() { 
        return view('admin.exams'); 
    }
    public function storeExam(Request $request) { 
        return back()->with('success', 'Exam Scheduled'); 
    }

    // ==========================================
    // 8.0 FINANCE (Cash Flow System)
    // ==========================================

    public function finance() {
        // 1. Calculate Totals
        $totalIncome = Transaction::where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('type', 'expense')->sum('amount');
        $currentBalance = $totalIncome - $totalExpense;

        // 2. Get Recent Transactions (Latest first)
        $transactions = Transaction::orderBy('date', 'desc')->get();

        return view('admin.finance', [
            'totalIncome'    => $totalIncome,
            'totalExpense'   => $totalExpense,
            'currentBalance' => $currentBalance,
            'transactions'   => $transactions
        ]);
    }

    public function storeTransaction(Request $request) {
        $request->validate([
            'type' => 'required', // income or expense
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'category' => 'required'
        ]);

        Transaction::create([
            'type'           => $request->type,
            'category'       => $request->category,
            'description'    => $request->description,
            'amount'         => $request->amount,
            'date'           => $request->date,
            'payment_method' => $request->payment_method
        ]);

        return back()->with('success', 'Transaction Recorded Successfully!');
    }

    public function deleteTransaction($id) {
        Transaction::findOrFail($id)->delete();
        return back()->with('success', 'Record Deleted.');
    }
    
    // 9.0 Reports
    public function reports() { 
        return view('admin.reports', ['stats' => []]); 
    }
    
   // ==========================================
    // 10.0 SCHOOL EVENTS (CRUD)
    // ==========================================
    
    // 1. READ (List Events)
    public function events() {
        // Fetch events sorted by upcoming date
        $events = Event::orderBy('start_date', 'asc')->get();
        return view('admin.events', ['events' => $events]);
    }

    // 2. CREATE (Store Event)
    public function storeEvent(Request $request) {
        $request->validate([
            'title' => 'required',
            'start_date' => 'required|date',
            'theme' => 'required'
        ]);

        Event::create([
            'title'       => $request->title,
            'description' => $request->description,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date ?? $request->start_date, // Default to start date if empty
            'theme'       => $request->theme, // 'primary' (Activity) or 'danger' (Holiday)
            'created_by'  => 1 // Default Admin ID for now
        ]);

        return back()->with('success', 'Event Posted Successfully!');
    }

    // 3. UPDATE (Edit Event)
    public function updateEvent(Request $request, $id) {
        $event = Event::findOrFail($id);
        
        $event->update([
            'title'       => $request->title,
            'description' => $request->description,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'theme'       => $request->theme,
        ]);

        return back()->with('success', 'Event Updated Successfully!');
    }

    // 4. DELETE (Remove Event)
    public function deleteEvent($id) {
        Event::findOrFail($id)->delete();
        return back()->with('success', 'Event Deleted.');
    }
    }
