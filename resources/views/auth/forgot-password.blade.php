@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <section class="auth-page auth-login-page">
        <div class="auth-login-frame">
            <div class="auth-login-shell">
                <aside class="auth-login-brand-panel" style="background-image: url('{{ $system_settings && $system_settings->system_logo ? asset('storage/' . $system_settings->system_logo) : asset('img/logo.svg') }}'); background-size: 60% auto; background-position: 85% 90%; background-repeat: no-repeat; background-blend-mode: overlay;">
                    <a href="{{ route('home') }}" class="auth-login-brand">
                        <img src="{{ $system_settings && $system_settings->system_logo ? asset('storage/' . $system_settings->system_logo) : asset('img/logo.svg') }}" alt="{{ $system_settings->system_name ?? 'System' }} Logo" class="auth-login-brand-icon" style="width: 38px; height: 38px; border-radius: 8px; margin-right: 12px;">
                        <span>
                            <strong>{{ $system_settings->system_name ?? 'Chriss Integrated Systems' }}</strong>
                            <small>Unified Access Portal</small>
                        </span>
                    </a>

                    <div class="auth-login-copy">
                        <h1>Recover account access</h1>
                        <p>Enter your account email address and we will send a secure password reset link.</p>
                    </div>

                    <div class="auth-login-guide">
                        <h3>How this works</h3>
                        <ul>
                            <li>Enter the email address linked to your account.</li>
                            <li>The system checks whether that email exists.</li>
                            <li>If found, a reset link is sent to that email for secure recovery.</li>
                        </ul>
                    </div>

                    <div class="auth-login-watermark" aria-hidden="true">
                        <i class="bi bi-key"></i>
                    </div>
                </aside>

                <div class="auth-login-form-panel">
                    <div class="auth-login-form-card">
                        <div class="auth-login-title-row">
                            <h2 class="auth-login-title">
                                <i class="bi bi-envelope-paper"></i>
                                <span>Request Password Reset</span>
                            </h2>
                        </div>

                        <p class="auth-login-subtitle">Use your account email. If the account exists, we will email a password reset link.</p>

                        @include('auth.partials.feedback')

                        <form method="POST" action="{{ route('password.email') }}" class="auth-form auth-login-form" data-auth-form>
                            @csrf

                            <div class="auth-input-group">
                                <label for="email">Email Address</label>
                                <div class="auth-input-wrap auth-login-input @error('email') is-invalid @enderror">
                                    <i class="bi bi-envelope auth-input-icon"></i>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="user@example.com" required autocomplete="email">
                                </div>
                                @error('email')
                                    <div class="auth-field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn-brand auth-login-button auth-forgot-button" data-auth-submit data-loading-text="Sending link...">
                                <i class="bi bi-send"></i>
                                <span data-auth-submit-label>Send Reset Link</span>
                            </button>

                            <div class="auth-login-back-wrap">
                                <a href="{{ route('login') }}" class="auth-login-back-link">
                                    <i class="bi bi-arrow-left"></i>
                                    <span>Back to Login</span>
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
    <script src="{{ asset('js/chrissAuth.js') }}"></script>
@endsection