<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Register User
Route::post('register', [AuthController::class,'register']);

//Login User
Route::post('login', [AuthController::class,'login']);

//Check User
Route::get('user', [AuthController::class,'getAuthenticatedUser'])->middleware('jwt.verify');