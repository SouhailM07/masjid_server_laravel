<?php

namespace App\Http\Controllers;

use App\Helpers\Api\UserApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    protected UserApiResponse $apiResponses;

    public function __construct()
    {
        $this->apiResponses = new UserApiResponse();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users=User::with(['roles'])->get();
        return response()->json(["data"=>$users],200);
    }

    public function show(string $id)
    {
        //
        $user=User::find($id);
        return response()->json(["data"=>$user]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user=User::find($id);
        if(!$user){
            return response()->json(...$this->apiResponses->notFoundResponse());
        }
        //
        $data=$request->validate([
            'name'=>"string",
            "email"=>"email",
            "roles"=>['nullable','array'],
            "roles.*.id"=>["required","exists:roles,id"],
        ]);

        /**@disregard */
        $user->update(Arr::only($data,['name','email']));
        if(count(Arr::get($data,"roles",[]))>0){
            $roleIds = collect($data['roles'])->pluck('id')->toArray();
            /**@disregard */
            $user->roles()->sync($roleIds);
        }

        $debugData=[];
        if(config("app.debug")){
            /**@disregard */
            $debugData=["res"=>["data"=>$user->load("roles")]];
        }
        return response()->json(...$this->apiResponses->updateResponse($debugData));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $deletedUser=User::find($id);
        if(!$deletedUser){
            return response()->json(...$this->apiResponses->notFoundResponse());
        }

        return response()->json(...$this->apiResponses->deleteResponse());
    }
}
