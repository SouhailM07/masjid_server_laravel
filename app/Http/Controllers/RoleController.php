<?php

namespace App\Http\Controllers;

use App\Helpers\Api\RoleApiResponse;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected RoleApiResponse $apiResponses;
        public function __construct()
    {
        $this->apiResponses=new RoleApiResponse();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $roles= Role::all();
        return response()->json(["data"=>$roles],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data=$request->validate([
            'name'=>"required|string|unique:roles",
            'isPublic'=>"boolean",
            'description'=>"string"
        ]);

        $newRole=Role::create($data);

        return response()->json(...$this->apiResponses->createResponse(['data'=>$newRole]));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        if(!$id){
            return response()->json([],400);
        }
        $role=Role::find($id);
        if(!$role){
            return response()->json(...$this->apiResponses->notFoundResponse());
        }

        return response()->json(['data'=>$role]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data=$request->validate([
            'name'=>"required|string",
            "isPublic"=>"required|boolean",
            "description"=>"string"
        ]);
        $updateRole=Role::find($id);
        if(!$updateRole){
            return response()->json(...$this->apiResponses->notFoundResponse());
        }
        $updateRole->update($data);
        return response()->json(...$this->apiResponses->updateResponse(["data"=>$updateRole]));
    }

    /**
     * Remove the specified resource from storage.
     */
public function destroy($id)
{
    $role = Role::find($id);
    if(!$role){
        return response()->json(...$this->apiResponses->notFoundResponse());
    } 
    $role->delete();

    return response()->json(...$this->apiResponses->deleteResponse());
}
}
