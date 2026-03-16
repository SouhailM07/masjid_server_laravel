<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Helpers\Api\PrayerApiResponse;
use App\Models\Prayer;
use Illuminate\Http\Request;

class PrayerController extends Controller
{
    protected $apiResponses;
    public function __construct()
    {
        $this->apiResponses=new PrayerApiResponse();
    }

    public function index()
    {
        //
        $prayers=Prayer::with("center_id")->get();
        return response()->json(["data"=>$prayers]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data=$request->validate([
            "time"=>['required',"date_format:H:i"],
            "type"=>["required","in:istis9a,aid,janaza"],
            "center_id"=>['required',"exists:centers,id"]
        ]);

        $newPrayer=Prayer::create($data);
        $debugData=[];
        if(config('app.debug')){
            $debugData=['data'=>$newPrayer];
        }
        return response()->json(...$this->apiResponses->createResponse($debugData));
    }

    /**
     * Display the specified resource.
     */
    public function show(Prayer $prayer)
    {
        //
        return response()->json(["data"=>$prayer]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prayer $prayer)
    {
        //
        $data=$request->validate([
            "time"=>['nullable',"date"],
            "type"=>["nullable","in:istis9a,aid,janaza"],
        ]);

        $updatedPrayer=Prayer::update($data);
        $debugData=[];
        if(config('app.debug')){
            $debugData=['data'=>$updatedPrayer];
        }
        return response()->json(...$this->apiResponses->updateResponse($debugData));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prayer $prayer)
    {
        //
        $prayer->delete();
        $debugData=[];
        if(config("app.debug")){
            $debugData=["data"=>$prayer];
        }
        return response()->json(...$this->apiResponses->deleteResponse($debugData));
    }
}
