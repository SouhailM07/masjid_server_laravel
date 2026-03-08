<?php

namespace App\Http\Controllers;

use App\Helpers\Api\CenterApiResponse;
use App\Models\Center;
use App\Models\Prayer;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

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
        $centers=Center::withCount("users")->get()->makeHidden(["created_at","updated_at"]);
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
            "latitude"=>['required',"decimal:0",],
            "longitude"=>['required',"decimal:0"],
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
    $center = Center::find($id); 
    if (!$center) {
        return response()->json(...$this->apiResponses->notFoundResponse());
    }

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
    $center["users"]=$center->users()->count();
    $center['prayerTimes']=$mergedTimings;
    /**@disregard */
    return response()->json([
        'data' => 
             $center->except(["prayers","updated_at","created_at"])
    ]);
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
