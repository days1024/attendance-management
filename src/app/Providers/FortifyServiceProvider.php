<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Models\User;
use App\Models\Admin;
use App\Responses\LogoutResponse;
use Illuminate\Support\Facades\Auth;

class FortifyServiceProvider extends ServiceProvider
{

    public function register(): void
{
}
    public function boot(): void
    {
        

        Fortify::createUsersUsing(CreateNewUser::class);


        Fortify::loginView(function (Request $request) {
            if ($request->is('admin/login')) {
                return view('auth.admin.login');
            }
            return view('auth.user.login');
        });

        Fortify::registerView(fn () => view('auth.user.register'));
        
        Fortify::verifyEmailView(fn () => view('auth.user.email'));

        
        RateLimiter::for('login', function (Request $request) {

            $key = $request->input('type') === 'admin'
                ? 'admin:' . $request->ip()
                : 'user:' . $request->ip();

            return Limit::perMinute(1000)->by($key);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(1000)->by($request->session()->get('login.id'));
        });
    }
}