<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CenterController;
use App\Http\Controllers\PrayerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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


Route::resource('/users',UserController::class)->names("users");
Route::resource('/actions',ActionController::class)->names('actions');
Route::resource('/roles',RoleController::class)->names('roles');
Route::resource("/centers",CenterController::class)->names("centers");
Route::resource("/prayers",PrayerController::class)->names('prayers');