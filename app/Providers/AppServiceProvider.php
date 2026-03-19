<?php

namespace App\Providers;

use App\Models\AdminLoginActivity;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);

        Event::listen(Login::class, function (Login $event): void {
            $user = $event->user;

            if (!$user instanceof User) {
                return;
            }

            AdminLoginActivity::query()->create([
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'logged_in_at' => now(),
            ]);
        });
    }
}
