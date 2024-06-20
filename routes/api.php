
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => ['api'],
    'prefix' => 'auth'

], function ($router) {
//    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'userProfile']);
    Route::post('/update-profile', [AuthController::class, 'update']);
    Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login']);
    Route::post('/two-factor-challenge', [\App\Http\Controllers\LoginController::class, 'twoFactorChallenge']);

});

Route::get("/data",function (){
    return response()->json(['message' => 'This is public data']);
});
