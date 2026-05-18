<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\LanguageController;

Route::get('/lang/{locale}', [LanguageController::class, 'swap'])->name('lang.swap');

// --- LOGIN & AUTHENTICATION ---
Route::get('/', function () { 
    return view('auth.login'); 
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// --- FORGOT PASSWORD & OTP ---
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.forgot');
Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.sendCode');
Route::get('/verify-code', [AuthController::class, 'showVerifyCode'])->name('password.verify.page');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

// --- LOGOUT ---
Route::post('/logout', function (Request $request) {
    Auth::guard('admin')->logout();
    Auth::guard('teacher')->logout();
    Auth::guard('parent')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');


// --- ADMIN ROUTES ---
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance', [AdminController::class, 'attendanceSummary'])->name('attendance.summary');

    // Process 1.0: User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/store', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/update/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // Process 3.0: Enrolment
    Route::get('/enrolment', [AdminController::class, 'enrolment'])->name('enrolment');
    Route::post('/enrolment/store', [AdminController::class, 'storeStudent'])->name('enrolment.store');
    Route::put('/enrolment/update/{id}', [AdminController::class, 'updateStudent'])->name('enrolment.update');
    Route::delete('/enrolment/delete/{id}', [AdminController::class, 'deleteStudent'])->name('enrolment.delete');
    
    // ADDED BACK: Route for Assigning Teachers to Classes in the old-style UI
    Route::put('/enrolment/assign-teacher/{id}', [AdminController::class, 'updateClassTeacher'])->name('enrolment.assign-teacher');

    // 5.0 ASSESSMENT SETUP
    Route::get('/exams', [AdminController::class, 'exams'])->name('exams');
    Route::post('/exams/store', [AdminController::class, 'storeExam'])->name('exams.store');
    Route::put('/exams/{id}/update', [AdminController::class, 'updateExam'])->name('exams.update');
    Route::delete('/exams/{id}/delete', [AdminController::class, 'deleteExam'])->name('exams.delete');
    
    // 8.0 FINANCE
    Route::get('/finance', [AdminController::class, 'finance'])->name('finance');
    Route::post('/finance/generate-bills', [AdminController::class, 'generateMonthlyBills'])->name('finance.generate-bills');
    Route::post('/finance/store', [AdminController::class, 'storeTransaction'])->name('finance.store');
    Route::delete('/finance/delete/{id}', [AdminController::class, 'deleteTransaction'])->name('finance.delete');
    Route::post('/finance/approve/{id}', [AdminController::class, 'approvePayment'])->name('finance.approve');
    Route::post('/finance/reject/{id}', [AdminController::class, 'rejectPayment'])->name('finance.reject');
    Route::get('/finance/chart-data', [AdminController::class, 'getCashFlowData'])->name('finance.chart-data');

    // 9.0 REPORTS
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/takwim', [AdminController::class, 'reportTakwim'])->name('takwim');
        Route::post('/takwim/generate', [AdminController::class, 'generateTakwim'])->name('takwim.generate');
        Route::get('/takwim/print/{id}', [AdminController::class, 'printTakwim'])->name('takwim.print');
        Route::delete('/takwim/delete/{id}', [AdminController::class, 'deleteTakwim'])->name('takwim.delete');

        Route::get('/unjuran', [AdminController::class, 'reportUnjuran'])->name('unjuran');
        Route::post('/unjuran/generate', [AdminController::class, 'generateUnjuran'])->name('unjuran.generate');
        Route::get('/unjuran/print/{id}', [AdminController::class, 'printUnjuran'])->name('unjuran.print');
        Route::delete('/unjuran/delete/{id}', [AdminController::class, 'deleteUnjuran'])->name('unjuran.delete');

        Route::get('/berkelompok', [AdminController::class, 'reportRumusan'])->name('berkelompok');
        Route::post('/berkelompok/generate', [AdminController::class, 'generateRumusan'])->name('berkelompok.generate');
        Route::get('/berkelompok/print/{id}', [AdminController::class, 'printRumusan'])->name('berkelompok.print');
        Route::delete('/berkelompok/delete/{id}', [AdminController::class, 'deleteRumusan'])->name('berkelompok.delete');

        Route::get('/prestasi', [AdminController::class, 'reportPrestasi'])->name('prestasi');
        Route::post('/prestasi/generate', [AdminController::class, 'generatePrestasi'])->name('prestasi.generate');
        Route::get('/prestasi/print/{id}', [AdminController::class, 'printPrestasi'])->name('prestasi.print');
        Route::delete('/prestasi/delete/{id}', [AdminController::class, 'deletePrestasi'])->name('prestasi.delete');
    });

    // 10.0 EVENTS
    Route::get('/events', [AdminController::class, 'events'])->name('events');
    Route::post('/events/store', [AdminController::class, 'storeEvent'])->name('events.store');
    Route::put('/events/update/{id}', [AdminController::class, 'updateEvent'])->name('events.update');
    Route::delete('/events/delete/{id}', [AdminController::class, 'deleteEvent'])->name('events.delete');
});


// --- TEACHER ROUTES ---
Route::middleware('auth:teacher')->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/daily-logs', [TeacherController::class, 'dailyLogs'])->name('daily-logs');
    Route::post('/daily-logs/store', [TeacherController::class, 'storeDailyLogs'])->name('daily-logs.store');

    Route::get('/attendance', [TeacherController::class, 'attendance'])->name('attendance');
    Route::post('/attendance/store', [TeacherController::class, 'storeAttendance'])->name('attendance.store');
    Route::get('/attendance/print', [TeacherController::class, 'printAttendance'])->name('attendance.print');

    Route::get('/grading', [TeacherController::class, 'grading'])->name('grading');
    Route::post('/grading/store', [TeacherController::class, 'storeGrade'])->name('grading.store');

    Route::get('/report-cards', [TeacherController::class, 'reportCards'])->name('report-cards');
    Route::get('/report-cards/print/{assessment_id}', [TeacherController::class, 'printReportCards'])->name('report-cards.print');

    // Communication Routes
    Route::get('/communication', [TeacherController::class, 'communication'])->name('communication');
    Route::post('/communication/send', [TeacherController::class, 'sendMessage'])->name('chat.send');
    
    // Hafazan Routes
    Route::get('/hafazan', [TeacherController::class, 'hafazan'])->name('hafazan'); 
   Route::post('/hafazan/bulk-store', [TeacherController::class, 'storeBulkHafazan'])->name('hafazan.bulk_store');
   Route::delete('/hafazan/delete/{id}', [TeacherController::class, 'deleteHafazan'])->name('hafazan.delete');

    // Events 
    Route::get('/events', [TeacherController::class, 'events'])->name('events');
});


// --- PARENT PORTAL ROUTES ---
Route::middleware('auth:parent')->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [ParentController::class, 'dashboard'])->name('dashboard');
    Route::get('/daily-logs', [ParentController::class, 'dailyLogs'])->name('daily-logs');
    Route::get('/report-cards', [ParentController::class, 'reportCards'])->name('report-cards');
    Route::get('/report-cards/download/{student_id}/{assessment_id}', [ParentController::class, 'downloadReportCard'])->name('report-cards.download');

    // Fixed: Named this consistently for the sendMessage logic
    Route::get('/chat', [ParentController::class, 'chat'])->name('communication'); 
    Route::post('/chat/send', [ParentController::class, 'sendMessage'])->name('chat.send');

 // ==========================================
    // PAYMENT & STRIPE ROUTES
    // ==========================================
    // Change name from 'parent.payment' to just 'payment'
    Route::get('/payment', [App\Http\Controllers\ParentController::class, 'payment'])->name('payment'); 
    Route::post('/payment/upload', [App\Http\Controllers\ParentController::class, 'uploadReceipt'])->name('payment.upload');
    Route::post('/payment/stripe', [App\Http\Controllers\ParentController::class, 'createPayment'])->name('payment.pay');
    Route::get('/payment/success', [App\Http\Controllers\ParentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/cancel', [App\Http\Controllers\ParentController::class, 'paymentCancel'])->name('payment.cancel');
   
    Route::get('/notices', [ParentController::class, 'notices'])->name('notices');
    Route::get('/events', [ParentController::class, 'events'])->name('events');
});