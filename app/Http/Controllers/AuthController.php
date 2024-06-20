<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function __construct(){
        $this->middleware(['auth:api','2fa'], ['except' => ['login', 'register']]);
    }

    public function login(Request $request):object{
        // Xác thực đầu vào, cho phép username hoặc email
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Kiểm tra xem 'login' là email hay username
        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Tạo thông tin đăng nhập
        $credentials = [
            $loginType => $request->input('login'),
            'password' => $request->input('password')
        ];

        // Cố gắng đăng nhập người dùng
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
//        return $this->createNewToken($token);
//        return response()->json([
//            'access_token' => $token,
//            'user' => $user
//        ]);
    }
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'username'=> 'required|string|between:2,100|unique:users',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'current_password' => 'required_with:new_password|string|min:6',
            'new_password' => 'nullable|string|confirmed|min:6',
            'avatar' => 'nullable',
        ]);
//
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

//        if ($request->hasFile('avatar')) {
//            // Delete the old avatar if exists
//            if ($user->avatar) {
//                Storage::delete('public/avatars/' . $user->avatar);
//            }
//
//            $avatarName = time() . '.' . $request->avatar->extension();
//            $request->avatar->storeAs('public/avatars', $avatarName);
//            $user->avatar = $avatarName;
//        }
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time().'_' .$user->name. '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('avatars', $avatarName, 'direct_public');
            $user->avatar ='/avatars/'. $avatarName;
        }
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = bcrypt($request->new_password);
            } else {
                return response()->json(['error' => 'Current password is incorrect'], 400);
            }
        }

        $user->save();

        return response()->json(['message' => 'User information updated successfully', 'user' => $user]);
    }
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }
    public function userProfile(Request $request) {
        $user = $request->user();

        // Kiểm tra xem người dùng đã đăng nhập chưa
        if ($user) {
            // Trả về thông tin của người dùng
            return response()->json(['user' => $user, 'created_at'=>$user->remember_token]);
        } else {
            // Trả về thông báo lỗi nếu không tìm thấy người dùng
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
//        'access_token'=>$token,
        ]);
    }
}
