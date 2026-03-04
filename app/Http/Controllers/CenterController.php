<?php

namespace App\Http\Controllers;

use App\Helpers\Api\CenterApiResponse;
use App\Models\Center;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CenterController extends Controller
{
    protected $apiResponses;
    public function __construct()
    {
        $this->apiResponses=new CenterApiResponse();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $centers=Center::with("users")->get();
        return response()->json(["data"=>$centers]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data=$request->validate([
            "name"=>["string","required"],
            "logo"=>['nullable'],
            "city"=>['string',"required"],
            "wilaya"=>['string','required'],
            "type"=>['required','string',"in:masjid,mousala"],
            'user_id' => ["required", "exists:users,id"]
        ]);

        $newCenter=Center::create(Arr::except($data,['user_id']));
        $userRoleId=Role::where("name","user")->first()->id;
        $newCenter->users()->attach($data['user_id'],[
            'role_id'=>$userRoleId,
            "center_id"=>$newCenter->id
        ]);

        $debugData=[];
        if(config("app.debug")){
            $debugData=['data'=>$newCenter];
        }
        return response()->json(...$this->apiResponses->createResponse($debugData));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $center=Center::find($id);
        if(!$center) {
            return response()->json(...$this->apiResponses->notFoundResponse());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Center $center)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Center $center)
    {
        //
        $center->delete();
        return response()->json(...$this->apiResponses->deleteResponse());

    }
}
