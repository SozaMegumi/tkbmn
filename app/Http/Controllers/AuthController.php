<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Teacher; 
use App\Models\Guardian;

class AuthController extends Controller
{
    // --- 1. SMART ID/EMAIL LOGIN LOGIC ---
    public function login(Request $request) {
        $request->validate([
            'login_id' => 'required',
            'password' => 'required'
        ]);

        // 1. Check if the user typed an email (contains an @ symbol)
        $is_email = filter_var($request->login_id, FILTER_VALIDATE_EMAIL);
        $login_type = $is_email ? 'email' : 'username';

        // 2. Build the standard credentials array for Teachers & Parents
        $credentials = [
            $login_type => $request->login_id,
            'password'  => $request->password
        ];

        // 3. TRY ADMIN: (Only check if they typed an actual email, because Admins don't have 'username' columns)
        if ($is_email) {
            if (Auth::guard('admin')->attempt(['email' => $request->login_id, 'password' => $request->password])) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
        }

        // 4. TRY TEACHER: (Using either Email or Username)
        if (Auth::guard('teacher')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('teacher.dashboard');
        }

        // 5. TRY PARENT: (Using either Email or Username)
        if (Auth::guard('parent')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('parent.dashboard');
        }

        // 6. If everything fails
        return back()->withErrors(['login_id' => 'Invalid ID, Username, or Password.']);
    }

    // --- 2. FORGOT PASSWORD (SHOW FORM) ---
    public function showForgotPassword() {
        return view('auth.forgot-password');
    }

    // --- 3. SEND 6-DIGIT OTP ---
    public function sendResetCode(Request $request) {
        $request->validate(['email' => 'required|email']);

        // Check if email exists in Teachers or Guardians
        $isTeacher = Teacher::where('email', $request->email)->exists();
        $isParent = Guardian::where('email', $request->email)->exists();

        if (!$isTeacher && !$isParent) {
            return back()->with('error', 'We could not find an account with that email.');
        }

        $code = rand(100000, 999999); // Generate 6 digit code

        // Save to DB
        DB::table('password_reset_otps')->insert([
            'email' => $request->email,
            'token' => $code,
            'created_at' => Carbon::now()
        ]);

        // Send Email
        Mail::raw("Your Tabika Kemas password reset code is: $code. It expires in 15 minutes.", function ($message) use ($request) {
            $message->to($request->email)->subject('Your Password Reset Code');
        });

        return redirect()->route('password.verify.page')->with('email', $request->email);
    }

    // --- 4. SHOW VERIFY OTP FORM ---
    public function showVerifyCode() {
        if(!session('email')) return redirect()->route('password.forgot');
        return view('auth.verify-otp')->with('email', session('email'));
    }

    // --- 5. VERIFY OTP & RESET PASSWORD ---
    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric',
            'new_password' => 'required|min:8|confirmed' 
        ]);

        $resetRequest = DB::table('password_reset_otps')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$resetRequest) {
            return back()->with('error', 'Invalid verification code!');
        }

        if (Carbon::parse($resetRequest->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_otps')->where('email', $request->email)->delete();
            return back()->with('error', 'Code has expired. Please request a new one.');
        }

        // Update Password
        $hashedPassword = Hash::make($request->new_password);
        Teacher::where('email', $request->email)->update(['password' => $hashedPassword]);
        Guardian::where('email', $request->email)->update(['password' => $hashedPassword]);

        // Delete used code
        DB::table('password_reset_otps')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password reset successfully! You can now log in.');
    }
}