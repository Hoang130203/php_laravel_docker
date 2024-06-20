<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use App\Notifications\TwoFactorAuthenticationNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });


        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
//                $code = rand(100000, 999999); // Tạo mã xác thực
                $code= 123456;
                $user->notify(new TwoFactorAuthenticationNotification($code));
                $request->session()->put('2fa:user:id', $user->id);
                $request->session()->put('2fa:code', $code);

                info('User ID: ' . $user->id);  // Log user ID
                info('Code from session: ' . $code);  // Log code from session
                info('Code from request: ' . $code);  // Log code from request
                return $user;
            }
        });

        // API endpoint để xác nhận mã xác thực
        Route::post('/api/auth/two-factor-challenge', function (Request $request) {
            $userId = $request->session()->get('2fa:user:id');
            $code = $request->session()->get('2fa:code');


            if ($request->code == $code) {
                Auth::loginUsingId($userId);
                return response()->json(['message' => 'Xác thực thành công.'], 200);
            }

            return response()->json(['message' => 'Mã xác thực không đúng.'], 401);
        });
    }
}
