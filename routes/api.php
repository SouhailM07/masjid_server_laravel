<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\V1\ActionController;
use App\Http\Controllers\Api\V1\CenterController;
use App\Http\Controllers\Api\V1\PrayerController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hi',function(){
    $mode='debug mode is OFF';
    if(config('app.debug')) $mode='debug mode is ON';
    return response()->json(["message"=>"welcome to the absolute shadow , $mode"],200);
});


Route::post('/register',[AuthController::class,'register'])->name('register');
Route::post("/login",[AuthController::class,'login'])->name("login");
Route::post("/logout",[AuthController::class,'logout'])->name('logout');


Route::prefix('v1')->group(function(){
    Route::apiResource('/users',UserController::class)->names("users");
    Route::apiResource('/actions',ActionController::class)->names('actions');
    Route::apiResource('/roles',RoleController::class)->names('roles');
    Route::apiResource("/prayers",PrayerController::class)->names('prayers');
    Route::apiResource("/centers",CenterController::class)->names("centers");
    // ! use admins in middleware not urls
    Route::put("/centers/{center}/users/{user}/role",[CenterController::class,'assignUserCenterRole']);
    Route::put("/centers/updateUserCenterRole",[CenterController::class,'assignUserCenterRole']);
    
    Route::post('/centers/{center}/join',[CenterController::class,'joinUserCenter']);
});