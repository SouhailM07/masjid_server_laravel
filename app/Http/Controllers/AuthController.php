<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $req){
        $userData=$req->validate(['name'=>"required|string",
                                "email"=>"required|email|unique:users,email",
                                "password"=>"required|string|min:8",
                                "password_confirmation"=>"required|same:password"]);
        $userRole=Role::where("name","user")->first()->id;
        $userData["role_id"]=$userRole;
        $newUser=User::create($userData);

        $token = $newUser->createToken("auth_token")->plainTextToken;

        return response()->json([
            "message"=>"Registered successfully",
            "user"=>$newUser,
            "token"=>$token
        ],201);
    }
}
