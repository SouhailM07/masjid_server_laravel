<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Action;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActionController extends Controller
{
    //
    
  public function index() {
        $actions = Action::all();
        return response()->json(['data' => $actions], 200);
    }

    public function show($id) {
        $action = Action::find($id);
        if(!$action){
            return response()->json(['message'=>"Action not found"], 404);
        }
        return response()->json(['data' => $action], 200);
    }

    public function store(Request $request){
        $data=$request->validate([
            'name'=>'required|string|unique:actions',
            "isPublic"=>['required','boolean'],
            'description'=>['nullable',"string"]
        ]);

        $newAction = Action::create($data);
        // 
        return response()->json(["message"=>"New Action was created successfully",'data'=>$newAction],201);
    }

    public function update(Request $request, $id){
        $data=$request->validate([
            'name' => 'string|unique:actions,name,' . $id,
            'isPublic'=>['boolean'],
            'description'=>"string"
        ]);

        $action = Action::find($id);
        if(!$action){
            return response()->json(["message"=>"Action not found"],404);
        }
        /**@disregard */
        $action->update($data);
        
        return response()->json(["message"=>"Action updated successfully",'data'=>$action],200);
    }
    public function destroy($id){
        if(!$id){
            return response()->json(["message"=>"Action id is required"],400);
        }
        $action = Action::find($id);
        if(!$action){
            return response()->json(["message"=>"Action not found"],404);
        }
        /**@disregard */
        $action->delete();
        return response()->json(["message"=>"Action deleted successfully"],200);
    }
}
