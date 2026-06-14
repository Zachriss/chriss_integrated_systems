<?php

namespace App\Providers;

use App\Models\StaffTask;
use App\Models\SystemSetting;
use App\Models\User;
use App\Observers\SensitiveActivityObserver;
use App\Services\AuditTrailService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_contains(config('app.url'), 'ngrok-free.dev') || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);

        User::observe(SensitiveActivityObserver::class);

        Event::listen(Login::class, function (Login $event): void {
            app(AuditTrailService::class)->logAuthEvent('login', $event->user, [
                'guard' => $event->guard,
            ]);
        });

        Event::listen(Logout::class, function (Logout $event): void {
            app(AuditTrailService::class)->logAuthEvent('logout', $event->user, [
                'guard' => $event->guard,
            ]);
        });

        Event::listen(Registered::class, function (Registered $event): void {
            $user = $event->user;

            app(AuditTrailService::class)->logAuthEvent('registered', $user, [
                'registered_user_email' => $user->email ?? null,
            ]);
        });

        Event::listen(Failed::class, function (Failed $event): void {
            app(AuditTrailService::class)->logAuthEvent('failed', null, [
                'guard' => $event->guard,
                'email' => $event->credentials['email'] ?? null,
            ]);
        });

        Event::listen(PasswordReset::class, function (PasswordReset $event): void {
            app(AuditTrailService::class)->logAuthEvent('password_reset', $event->user, [
                'email' => $event->user->email ?? null,
            ]);
        });

        // Share system settings globally with all views
        View::composer('*', function ($view) {
            try {
                $settings = SystemSetting::getSettings();
                $view->with('system_settings', $settings);
            } catch (\Exception $e) {
                // If table doesn't exist yet (during migration), provide a fallback
                $view->with('system_settings', (object) [
                    'system_name' => 'Chriss Integrated Systems',
                    'system_short_name' => 'CIS',
                    'system_logo' => null,
                    'system_favicon' => null,
                    'primary_color' => '#1a73e8',
                    'secondary_color' => '#6c757d',
                    'accent_color' => '#0d6efd',
                    'login_background' => null,
                    'currency' => 'TZS',
                    'timezone' => 'Africa/Dar_es_Salaam',
                    'email' => 'info@chrissintegrated.com',
                    'phone' => '+255 000 000 000',
                    'address' => 'Tanzania',
                    'footer_text' => 'All rights reserved.',
                    'email_from_name' => null,
                    'email_from_address' => null,
                    'maintenance_mode' => false,
                ]);
            }
        });

        // Share staff tasks with the staff sidebar for the expandable My Tasks menu
        View::composer('layouts.sidebar.staff', function ($view) {
            $user = auth()->user();
            $staffTasks = collect();

            if ($user && $user->role === 'staff') {
                try {
                    $staffTasks = StaffTask::with(['service', 'category'])
                        ->where('staff_id', $user->id)
                        ->orderByDesc('date')
                        ->orderByDesc('id')
                        ->limit(10)
                        ->get();
                } catch (\Exception $e) {
                    $staffTasks = collect();
                }
            }

            $view->with('staffTasks', $staffTasks);
        });
    }
}
