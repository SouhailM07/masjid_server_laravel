<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hi',function(){
    return response()->json(["message"=>"welcome to the absolute shadow"],201);
});

Route::post('/register',[AuthController::class,'register']);