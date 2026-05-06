@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <h4 class="fw-bold mb-3">Verify Code</h4>
        <p class="text-muted small">We sent a 6-digit code to <strong>{{ session('email') ?? old('email') }}</strong>.</p>

        @if(session('error'))
            <div class="alert alert-danger py-2">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}">
            @csrf
            <input type="hidden" name="email" value="{{ session('email') ?? old('email') }}">
            
            <div class="mb-3">
                <label class="form-label fw-bold text-primary">6-Digit Code</label>
                <input type="text" name="code" class="form-control form-control-lg text-center fw-bold tracking-widest" maxlength="6" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">New Password</label>
                <input type="password" name="new_password" class="form-control" required minlength="8">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" class="form-control" required minlength="8">
            </div>

            <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold">Reset Password</button>
        </form>
    </div>
</div>
@endsection