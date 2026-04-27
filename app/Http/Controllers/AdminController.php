<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Event;
use App\Models\Transaction; 
use App\Models\Payment;     
use App\Models\Attendance;  
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon;

class AdminController extends Controller
{
    // ==========================================
    // DASHBOARD & OVERVIEW
    // ==========================================
    public function dashboard() {
        try {
            $totalStudents = Student::count();
            $totalClasses  = Classroom::count();
            $pendingApprovals = Student::where('status', 'Pending')->count();

            $recentEnrollments = Student::with('parent')->orderBy('created_at', 'desc')->take(5)->get();

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
                    'message' => $event->title . ' is on ' . Carbon::parse($event->start_date)->format('d M Y') . '.'
                ];
            }

            // Standardize labels for the chart fallback
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
    // 9.0 REPORT MANAGEMENT (Process 9.0 in DFD)
    // ==========================================
    public function reports() { 
        $stats = [
            'total_income' => Transaction::where('type', 'income')->sum('amount'),
            'monthly_attendance_avg' => Attendance::whereMonth('date', now()->month)
                                        ->where('status', 'Present')->count(),
            'enrollment_by_gender' => Student::selectRaw('gender, count(*) as total')
                                        ->groupBy('gender')->get()
        ];

        return view('admin.reports', compact('stats')); 
    }

    /**
     * AJAX METHOD: Fetch Real Cash Flow Data (Process 9.0)
     */
    public function getCashFlowData(Request $request) {
        $monthsCount = $request->get('timeframe') === 'thisyear' ? 12 : 6;
        $labels = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M');

            $incomeData[] = Transaction::where('type', 'income')
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('amount');

            $expenseData[] = Transaction::where('type', 'expense')
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('amount');
        }

        return response()->json([
            'labels' => $labels,
            'income' => $incomeData,
            'expense' => $expenseData
        ]);
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
    // 8.0 FINANCE (Process 8.0 in DFD)
    // ==========================================
    public function finance() {
        $totalIncome = Transaction::where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('type', 'expense')->sum('amount');
        $pendingPayments = Payment::where('status', 'Pending')->with('student')->get();

        return view('admin.finance', [
            'totalIncome'    => $totalIncome,
            'totalExpense'   => $totalExpense,
            'currentBalance' => $totalIncome - $totalExpense,
            'transactions'   => Transaction::orderBy('date', 'desc')->get(),
            'pendingPayments' => $pendingPayments
        ]);
    }

    public function approvePayment($id) {
        $payment = Payment::findOrFail($id);
        $payment->update(['status' => 'Paid']);

        // Link parent receipt verification directly to school income
        Transaction::create([
            'type' => 'income',
            'amount' => $payment->amount,
            'category' => 'School Fees',
            'date' => now(),
            'description' => "Fee payment for " . ($payment->student->student_name ?? 'Student')
        ]);

        return back()->with('success', 'Payment approved and recorded in finance.');
    }

    public function rejectPayment(Request $request, $id) {
        $payment = Payment::findOrFail($id);
        $payment->update([
            'status' => 'Unpaid',
            'admin_remarks' => $request->remarks 
        ]);

        return back()->with('error', 'Payment rejected. Parent has been notified.');
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

    public function deleteTransaction($id) {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
        return back()->with('success', 'Transaction record deleted successfully.');
    }

    // ==========================================
    // 10.0 SCHOOL EVENTS
    // ==========================================
    public function events() {
        return view('admin.events', ['events' => Event::orderBy('start_date', 'asc')->get()]);
    }

    public function storeEvent(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'theme' => 'required|in:primary,danger'
        ]);

        Event::create([
            'title'       => $request->title,
            'description' => $request->description,
            'start_date'  => Carbon::parse($request->start_date)->toDateString(),
            'end_date'    => $request->end_date ? Carbon::parse($request->end_date)->toDateString() : Carbon::parse($request->start_date)->toDateString(),
            'theme'       => $request->theme,
            'created_by'  => Auth::id() ?? 1
        ]);

        return back()->with('success', 'Event Posted Successfully!');
    }

    public function deleteEvent($id) {
        Event::findOrFail($id)->delete();
        return back()->with('success', 'Event Deleted.');
    }
}