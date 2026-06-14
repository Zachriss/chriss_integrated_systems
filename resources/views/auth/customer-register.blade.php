@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
<section class="auth-page auth-login-page">
    <div class="auth-login-frame">
        <div class="auth-login-shell">
            <aside class="auth-login-brand-panel" style="background-image: url('{{ $system_settings && $system_settings->system_logo ? asset('storage/' . $system_settings->system_logo) : asset('img/logo.svg') }}'); background-size: 60% auto; background-position: 85% 90%; background-repeat: no-repeat; background-blend-mode: overlay;">
                <a href="{{ route('home') }}" class="auth-login-brand">
                    <img src="{{ $system_settings && $system_settings->system_logo ? asset('storage/' . $system_settings->system_logo) : asset('img/logo.svg') }}" alt="Logo" class="auth-login-brand-icon" style="width: 38px; height: 38px; border-radius: 8px; margin-right: 12px;">
                    <span>
                        <strong>{{ $system_settings->system_name ?? 'Chriss Integrated Systems' }}</strong>
                        <small>Customer Portal</small>
                    </span>
                </a>
                <div class="auth-login-copy">
                    <h1>Create your customer account</h1>
                </div>
                <div class="auth-login-guide">
                    <ul>
                        <li>Register to request services and track orders</li>
                        <li>View assigned products and service history</li>
                        <li>Fast checkout and request tracking</li>
                    </ul>
                </div>
                <div class="auth-login-watermark" aria-hidden="true">
                    <i class="bi bi-person-plus"></i>
                </div>
            </aside>

            <div class="auth-login-form-panel">
                <div class="auth-login-form-card">
                    <div class="auth-login-title-row">
                        <h2 class="auth-login-title">
                            <i class="bi bi-person-plus"></i>
                            <span>Create Account</span>
                        </h2>
                    </div>
                    <p class="auth-login-subtitle">Fill in your details to get started.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('customer.register.submit') }}" class="auth-form auth-login-form" id="registerForm">
                        @csrf
                        <div class="auth-input-group">
                            <label for="full_name">Full Name</label>
                            <div class="auth-input-wrap auth-login-input @error('full_name') is-invalid @enderror">
                                <input id="full_name" type="text" name="full_name" value="{{ old('full_name') }}" placeholder="John Doe" required>
                            </div>
                        </div>

                        <div class="auth-input-group">
                            <label for="phone">Phone Number</label>
                            <div class="auth-input-wrap auth-login-input @error('phone') is-invalid @enderror">
                                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="+255 7XX XXX XXX" required>
                            </div>
                        </div>

                        <div class="auth-input-group">
                            <label for="email">Email Address (optional)</label>
                            <div class="auth-input-wrap auth-login-input @error('email') is-invalid @enderror">
                                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="email@example.com">
                            </div>
                        </div>

                        <div class="auth-input-group">
                            <label for="address">Address (optional)</label>
                            <div class="auth-input-wrap auth-login-input">
                                <textarea id="address" name="address" class="form-control" rows="2" placeholder="Your location">{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="auth-input-group">
                                    <label for="password">Password</label>
                                    <div class="auth-input-wrap auth-login-input @error('password') is-invalid @enderror">
                                        <input id="password" type="password" name="password" placeholder="Min 6 chars" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="auth-input-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <div class="auth-input-wrap auth-login-input">
                                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repeat password" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-brand auth-login-button mt-3" id="submitBtn">
                            <span>Create Account</span>
                        </button>

                        <div class="auth-login-back-wrap">
                            <a href="{{ route('login') }}" class="auth-login-back-link">
                                <i class="bi bi-arrow-left"></i>
                                <span>Already have an account? Login</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating account...';

    const data = Object.fromEntries(new FormData(this).entries());

    fetch(this.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect || '/customer/dashboard';
        } else {
            let errorMsg = 'Please fix the errors.';
            if (data.errors) {
                errorMsg = Object.values(data.errors).flat().join('<br>');
            } else if (data.message) {
                errorMsg = data.message;
            }
            const errDiv = document.createElement('div');
            errDiv.className = 'alert alert-danger';
            errDiv.innerHTML = errorMsg;
            document.getElementById('registerForm').prepend(errDiv);
            btn.disabled = false;
            btn.innerHTML = 'Create Account';
        }
    })
    .catch(() => {
        // Fallback: submit normally
        document.getElementById('registerForm').submit();
    });
});
</script>
@endsection