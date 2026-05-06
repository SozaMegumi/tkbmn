@extends('layouts.app') <!-- Change if your login uses a different layout -->

@section('content')
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <h4 class="fw-bold mb-3">Forgot Password</h4>
        <p class="text-muted small">Enter your email address and we will send you a 6-digit verification code.</p>

        @if(session('error'))
            <div class="alert alert-danger py-2">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('password.sendCode') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Send Code</button>
           <a href="{{ route('login') }}" class="btn btn-link w-100 mt-2 text-muted text-decoration-none">Back to Login</a>
        </form>
    </div>
</div>
@endsection