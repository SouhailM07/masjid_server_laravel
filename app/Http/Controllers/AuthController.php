<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function logout(Request $req){
        /** @disregard*/
        $req->user()->currentAccessToken()->delete();
        return response()->json(["message"=>"Logged out successfully"]);
    }
    public function login(Request $req){
        $userData=$req->validate(['email'=>"required|email",
                                "password"=>"required|string|min:8"]);

        if(!Auth::attempt($userData)){
            return  response()->json(["message"=>"Invalid Credentials"],401);
        }

        $user=Auth::user();
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "message"=>"Logged In Successfully",
            "user"=>$user,
            "token"=>$token
        ]);

    }
    public function register(Request $req){
        $userData=$req->validate(['name'=>"required|string",
                                "email"=>"required|email|unique:users,email",
                                "password"=>"required|string|min:8",
                                "password_confirmation"=>"required|same:password"]);
        $userRoleId=Role::where("name","user")->first()->id;
        $newUser=User::create($userData);
        $newUser->roles()->attach($userRoleId);
        $token = $newUser->createToken("auth_token")->plainTextToken;
        $debugData=[];
        if(config("app.debug")){
            $debugData=["data"=>["user"=>$newUser]];
        }
        return response()->json([
            "message"=>"Registered successfully",
            "token"=>$token,
            ...$debugData
        ],201);
    }
}
