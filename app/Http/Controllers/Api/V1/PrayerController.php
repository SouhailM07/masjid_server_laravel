<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Api\PrayerApiResponse;
use App\Models\Center;
use App\Models\Prayer;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

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

    public function indexByCenter(Center $center){
            // Call Aladhan API
    $prayerTimesResponse = Http::withoutVerifying()
        ->get('https://api.aladhan.com/v1/timingsByCity', [
            'city' => 'Algiers',
            'country' => 'Algeria'
        ])
        ->json();

    // Extract only 'timings'
    $timings = $prayerTimesResponse['data']['timings'] ?? [];

    // Map DB prayers to ['type' => 'time'] format
    $dbPrayers = $center->prayers->pluck('time', 'type')->map(function($time) {
        return Carbon::createFromFormat('H:i:s', $time)->format('H:i');
    })->toArray();

    // Merge them into one timings object
    $mergedTimings = array_merge($timings, $dbPrayers);
    /**@disregard */
    $center['prayerTimes']=$mergedTimings;
    /**@disregard */
    return response()->json([
        'data' => 
             $center->except(["prayers","updated_at","created_at"])
    ]);
    }

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
