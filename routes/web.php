<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ParentController;

// --- LOGIN (Process 2.0) ---
Route::get('/', function () { 
    return view('auth.login'); 
})->name('login');

Route::post('/login', function (Request $request) {
    $creds = $request->validate(['email' => 'required', 'password' => 'required']);

    if (Auth::guard('admin')->attempt($creds)) {
        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }
    if (Auth::guard('teacher')->attempt($creds)) {
        $request->session()->regenerate();
        return redirect()->route('teacher.dashboard');
    }
    if (Auth::guard('parent')->attempt($creds)) {
        $request->session()->regenerate();
        return redirect()->route('parent.dashboard');
    }
    return back()->withErrors(['email' => 'Invalid credentials.']);
})->name('login.submit');

Route::post('/logout', function (Request $request) {
    Auth::guard('admin')->logout();
    Auth::guard('teacher')->logout();
    Auth::guard('parent')->logout();
    $request->session()->invalidate();
    return redirect('/');
})->name('logout');


// --- ADMIN ROUTES (Processes 1.0, 3.0, 4.0, 8.0, 9.0, 10.0) ---
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // --- Process 1.0: User Management ---
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/store', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/update/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // --- Process 3.0: Enrolment ---
    Route::get('/enrolment', [AdminController::class, 'enrolment'])->name('enrolment');
    Route::post('/enrolment/store', [AdminController::class, 'storeStudent'])->name('enrolment.store');
    Route::put('/enrolment/update/{id}', [AdminController::class, 'updateStudent'])->name('enrolment.update');
    Route::delete('/enrolment/delete/{id}', [AdminController::class, 'deleteStudent'])->name('enrolment.delete');

    // Process 5.0: Assessment Setup
    Route::get('/exams', [AdminController::class, 'exams'])->name('exams');
    Route::post('/exams/store', [AdminController::class, 'storeExam'])->name('exams.store');

    // --- 8.0 FINANCE ---
    Route::get('/finance', [AdminController::class, 'finance'])->name('finance');
    Route::post('/finance/store', [AdminController::class, 'storeTransaction'])->name('finance.store');
    Route::delete('/finance/delete/{id}', [AdminController::class, 'deleteTransaction'])->name('finance.delete');

    // Process 9.0: Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');

   // --- 10.0 EVENTS ---
    Route::get('/events', [AdminController::class, 'events'])->name('events');
    Route::post('/events/store', [AdminController::class, 'storeEvent'])->name('events.store');
    Route::put('/events/update/{id}', [AdminController::class, 'updateEvent'])->name('events.update');
    Route::delete('/events/delete/{id}', [AdminController::class, 'deleteEvent'])->name('events.delete');
});


// --- TEACHER ROUTES (Processes 5.0, 6.0, 7.0) ---
Route::middleware('auth:teacher')->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');

    // Process 6.0: Attendance
    Route::get('/attendance', [TeacherController::class, 'attendance'])->name('attendance');
    Route::post('/attendance/store', [TeacherController::class, 'storeAttendance'])->name('attendance.store');

    // Process 5.0: Grading
    Route::get('/grading', [TeacherController::class, 'grading'])->name('grading');
    Route::post('/grading/store', [TeacherController::class, 'storeGrade'])->name('grading.store');

    // Process 7.0: Chat
    Route::get('/communication', [TeacherController::class, 'communication'])->name('communication');
    // ** ADDED THIS LINE FOR TEACHER CHAT SEND **
    Route::post('/communication/send', [TeacherController::class, 'sendMessage'])->name('chat.send');
});


// --- PARENT PORTAL ROUTING ---
Route::middleware('auth:parent')->prefix('parent')->name('parent.')->group(function () {
    
    // 1. The Dashboard (Widgets)
    Route::get('/dashboard', [ParentController::class, 'dashboard'])->name('dashboard');
    
    // 2. Full Screen Chat
    Route::get('/chat', [ParentController::class, 'chat'])->name('communication'); // Kept name 'communication' for sidebar links
    // ** ADDED THIS LINE FOR PARENT CHAT SEND **
    Route::post('/chat/send', [ParentController::class, 'sendMessage'])->name('chat.send');

    // 3. Full Screen Payment/Finance
    Route::get('/payment', [ParentController::class, 'payment'])->name('payment');

    // 4. Full Screen Notices (New)
    Route::get('/notices', [ParentController::class, 'notices'])->name('notices');
});