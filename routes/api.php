<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::group(['middleware' => 'auth:api', 'prefix' => 'business'], function(){
    Route::get('/jobs', [JobsController::class, 'business_jobs']);
    Route::post('/create', [JobsController::class, 'create_job']);
    Route::post('/update/{job}', [JobsController::class, 'update_job']);
    Route::delete('/delete/{job}', [JobsController::class, 'delete_job']);
});

Route::get('/jobs', [JobsController::class, 'all_jobs']);
Route::get('/jobs/{job}', [JobsController::class, 'view_job']);

Route::get('/search', [JobsController::class, 'search']);

Route::post('/jobs/{job}', [JobsController::class, 'apply']);
