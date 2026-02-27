<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
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

        return response()->json(['message'=>"New Role was created",'data'=>$newRole]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        if(!$id){
            return response()->json(['message'=>"Role Id is required"],400);
        }
        $role=Role::find($id);
        if(!$role){
            return response()->json(['message'=>"Role was not found"],404);
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
            return response()->json(["message"=>"Role was not found"],404);
        }
        $updateRole->update($data);
        return response()->json(["message"=>"Role Was Updated","data"=>$updateRole],200);
    }

    /**
     * Remove the specified resource from storage.
     */
public function destroy($id)
{
    $role = Role::find($id);
    if(!$role) response()->json(["message"=>"Role not found"],404);
    $role->delete();

    return response()->json(['message' => 'Role was deleted'], 200);
}
}
