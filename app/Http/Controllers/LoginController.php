<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    //

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $loginType => $request->input('login'),
            'password' => $request->input('password')
        ];
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
//        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $code = rand(100000, 999999);
            $user->notify(new \App\Notifications\TwoFactorAuthenticationNotification($code));
            $user->setTwoFactorCode($code);
//            Session::put('2fa:user:id', $user->id);
//            Session::put('2fa:code', $code);
//            Session::flash('success');
//            session()->put('2fa:user:id', $code);
//            info('User ID: ' . $user->id);  // Log user ID
//            info('Code from session: ' . $code);  // Log code from session

            return response()->json(['message' => 'Mã xác thực đã được gửi.'.$code,'userId'=>$user->id,  'session_data' => Session::all()], 200);
        }

        return response()->json(['message' => 'Thông tin đăng nhập không đúng.'], 401);
    }

    public function twoFactorChallenge(Request $request)
    {
//        dd(Session::get());
        $user = User::find($request->id);
//        $userId = Session::get('2fa:user:id');
        $code = $user->getTwoFactorCode();
        $expiry= $user->two_factor_code_expiry;
        if ($request->code == $code && $expiry > now()) {
//            Auth::loginUsingId($userId);

            $user->resetTwoFactorCode();

            $token = $user->createToken('auth_token', ['expires_at' => Carbon::now()->addDays(7)])->plainTextToken;
            $cookie = cookie('2fa_remember', true, 7 * 24 * 60);
            return response()->json(['token' => $token],200)->withCookie($cookie);
//            return response()->json(['message' => 'Xác thực thành công.'], 200);
        }

        return response()->json(['message' => 'Mã xác thực không đúng.'], 401);
    }
}
