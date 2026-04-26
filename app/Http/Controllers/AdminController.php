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
use Illuminate\Support\Facades\Auth; // Added for secure ID retrieval
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

            // 2. Recent Enrollments
            $recentEnrollments = Student::with('parent')->orderBy('created_at', 'desc')->take(5)->get();

            // 3. Alerts & Tasks (Right Column)
            $alerts = [];
            // Logic: Sync with UTC+8 to show events for the next 30 days
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
                    // Format for UTC+8 display consistency
                    'message' => $event->title . ' is on ' . Carbon::parse($event->start_date)->format('d M Y') . '.'
                ];
            }

            // 4. WEEKLY ATTENDANCE DATA (Static placeholders for Chart.js)
            $attendanceLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $attendanceData = [94, 91, 95, 89, 91];

        } catch (\Exception $e) {
            $totalStudents = 0; $totalClasses = 0; $pendingApprovals = 0;
            $recentEnrollments = []; $alerts = [];
            $attendanceLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $attendanceData = [0, 0, 0, 0, 0];
        }
        
        return view('admin.dashboard', compact(
            'totalStudents', 'totalClasses', 'pendingApprovals', 
            'recentEnrollments', 'alerts', 'attendanceLabels', 'attendanceData'    
        ));
    }

    // ==========================================
    // 1.0 USER ACCOUNTS (Teachers & Parents)
    // ==========================================
    public function users() {
        return view('admin.users', [
            'teachers' => Teacher::orderBy('full_name', 'asc')->get(), 
            'parents' => Guardian::orderBy('parent_name', 'asc')->get()
        ]);
    }
    
    public function storeUser(Request $request) {
        // Safety: Ensure emails are unique across both tables
        $request->validate([
            'email' => 'required|email|unique:teachers,email|unique:guardians,email',
            'name' => 'required|string|max:255',
            'type' => 'required|in:teacher,parent'
        ]);

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
                'join_date'    => $request->join_date ?? now()->toDateString(),
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

    public function deleteUser(Request $request, $id) {
        if($request->input('type') == 'teacher') {
            Teacher::findOrFail($id)->delete();
        } else {
            Guardian::findOrFail($id)->delete();
        }
        return back()->with('success', 'User account deleted successfully.');
    }

    // ==========================================
    // 3.0 & 4.0 STUDENT ENROLMENT
    // ==========================================
    public function enrolment() {
        return view('admin.enrolment', [
            'classesWithStudents' => Classroom::with(['students.parent'])->get(),
            'unassignedStudents'  => Student::whereNull('class_id')->with('parent')->get(),
            'parents'             => Guardian::all(),
            'classes'             => Classroom::all()
        ]);
    }
    
    public function storeStudent(Request $request) {
        // Validation: Ensure linked IDs exist to prevent DB errors
        $request->validate([
            'student_name' => 'required|string',
            'mykid'        => 'required|unique:students,mykid',
            'parent_id'    => 'required|exists:guardians,parent_id',
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

    // ==========================================
    // 8.0 FINANCE
    // ==========================================
    public function finance() {
        $totalIncome = Transaction::where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('type', 'expense')->sum('amount');
        
        return view('admin.finance', [
            'totalIncome'    => $totalIncome,
            'totalExpense'   => $totalExpense,
            'currentBalance' => $totalIncome - $totalExpense,
            'transactions'   => Transaction::orderBy('date', 'desc')->get()
        ]);
    }

    public function storeTransaction(Request $request) {
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => 'required'
        ]);

        Transaction::create($request->all());
        return back()->with('success', 'Transaction Recorded Successfully!');
    }

    // ==========================================
    // 10.0 SCHOOL EVENTS (CRUD) - SYNCED WITH PARENT CALENDAR
    // ==========================================
    public function events() {
        return view('admin.events', ['events' => Event::orderBy('start_date', 'asc')->get()]);
    }

    public function storeEvent(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'theme' => 'required|in:primary,danger' // primary=Activity, danger=Holiday
        ]);

        Event::create([
            'title'       => $request->title,
            'description' => $request->description,
            // Logic: Strip time for strict UTC+8 calendar tallying
            'start_date'  => Carbon::parse($request->start_date)->toDateString(),
            'end_date'    => $request->end_date ? Carbon::parse($request->end_date)->toDateString() : Carbon::parse($request->start_date)->toDateString(),
            'theme'       => $request->theme,
            'created_by'  => Auth::id() ?? 1
        ]);

        return back()->with('success', 'Event Posted Successfully!');
    }

    public function updateEvent(Request $request, $id) {
        $event = Event::findOrFail($id);
        $event->update([
            'title'       => $request->title,
            'description' => $request->description,
            'start_date'  => Carbon::parse($request->start_date)->toDateString(),
            'end_date'    => $request->end_date ? Carbon::parse($request->end_date)->toDateString() : $event->end_date,
            'theme'       => $request->theme,
        ]);
        return back()->with('success', 'Event Updated Successfully!');
    }

    public function deleteEvent($id) {
        Event::findOrFail($id)->delete();
        return back()->with('success', 'Event Deleted.');
    }
}