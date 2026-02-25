<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hi',function(){
    return response()->json(["message"=>"welcome to the absolute shadow"],201);
});

Route::resource('/users',UserController::class);

Route::post('/register',[AuthController::class,'register']);

Route::resource('/actions',ActionController::class)->names('actions');