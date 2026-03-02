<?php

namespace App\Http\Controllers;

use App\Helpers\Api\RoleApiResponse;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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
        $roles= Role::with('actions')->get();
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
            'description'=>"nullable|string",
            // 
            "actions"=>["nullable",'array'],
            "actions.*.id"=>["required",'integer','exists:actions,id'],
            'actions.*.create' => ['nullable', 'boolean'],
            'actions.*.read' => ['nullable', 'boolean'],
            'actions.*.update' => ['nullable', 'boolean'],
            'actions.*.delete' => ['nullable', 'boolean'],
        ]);

        $newRole=Role::create([
            'name'=>$data['name'],
            'isPublic'=>$data['isPublic'],
            "description"=>$data['description']??null,
        ]);

        if(!empty($data['actions'])){
            $attachData= collect($data['actions'])->mapWithKeys(fn($action)=> [
                $action['id']=>[
                'create' => $action['create'] ?? false,
                'read'   => $action['read'] ?? false,
                'update' => $action['update'] ?? false,
                'delete' => $action['delete'] ?? false,
            ]])->toArray();
            $newRole->actions()->sync($attachData);
        }
        $dataRes=[];
        if(config('app.debug')){
            $dataRes=['res'=>["data"=>$newRole->load('actions')]];
        }
        return response()->json(...$this->apiResponses->createResponse($dataRes));
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
        $role=Role::with('actions')->find($id);
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
            'name'=>"string",
            "isPublic"=>"boolean",
            "description"=>"string",
            // 
            "actions"=>['nullable','array'],
            "actions.*.id"=>['required','exists:actions,id','distinct'],
            "actions.*.create"=>['nullable',"boolean"],
            "actions.*.read"=>['nullable',"boolean"],
            "actions.*.update"=>['nullable',"boolean"],
            "actions.*.delete"=>['nullable','boolean']
        ]);
        $updateRole=Role::find($id);
        if(!$updateRole){
            return response()->json(...$this->apiResponses->notFoundResponse());
        }
        /** @disregard */
        $updateRole->update([
            'name' => $data['name'] ?? $updateRole->name,
            'isPublic' => $data['isPublic'] ?? $updateRole->isPublic,
            'description' => $data['description'] ?? $updateRole->description,
        ]);

        if(count(Arr::get($data, 'actions', [])) > 0){
            $attachData = collect($data['actions'])->mapWithKeys(fn($action)=>[
                $action['id']=>[
                    'create'=>$action['create']??false,
                    'read'=>$action['read']??false,
                    'update'=>$action['update']??false,
                    'delete'=>$action['delete']??false,
                ]
            ]);
            /** @disregard */
            $updateRole->actions()->sync($attachData);
        }
        $dataRes=[];
        if(config('app.debug')){
            /** @disregard */
            $dataRes=['res'=>["data"=>$updateRole->load('actions')]];
        }
        return response()->json(...$this->apiResponses->updateResponse($dataRes));
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
    /** @disregard */
    $role->delete();

    return response()->json(...$this->apiResponses->deleteResponse());
}
}
