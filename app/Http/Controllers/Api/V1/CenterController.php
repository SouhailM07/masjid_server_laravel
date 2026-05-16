<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Api\CenterApiResponse;
use App\Models\Center;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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
            "logo"=>['nullable','image','mimes:jpg,jpeg,png','max:2028'],
            "city"=>['string',"required"],
            "wilaya"=>['string','required'],
            "type"=>['required','string',"in:masjid,mousala"],
            "latitude"=>['required',"decimal:0",],
            "longitude"=>['required',"decimal:0"],
        ]);

        if(empty($data['logo'])){
            $data['logo']=$data['type']=="masjid" ? "defaults/mosque-logo.png":"defaults/mousala-logo.png";
        }
        else{
            $path= $request->file('logo')->store('centers/logos','public');
            $data['logo']=$path;
        }

        $newCenter=Center::create($data);

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
    /**@disregard */
             $center->except(["prayers","updated_at","created_at"])
    ]);
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Center $center)
    {
        $data=$request->validate([
            "name"=>["string"],
            "logo"=>['nullable','image','mimes:jpg,jpeg,png','max:2028'],
            "city"=>['string'],
            "wilaya"=>['string','required'],
            "type"=>['string',"in:masjid,mousala"],
            "latitude"=>["decimal:0",],
            "longitude"=>["decimal:0"],
        ]);
        if(!empty($data['logo'])){
            if($center->logo && Storage::disk('public')->exists($center->logo)){
                Storage::disk('public')->delete($center->logo);
            }
            $path=$request->file('logo')->store('centers/logos','public');
            $data['logo']=$path;
        }
        $center->update($data);
        return response()->json(...$this->apiResponses->updateResponse());
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
    /*=============================================================================================*/
    /* custom handlers */
    /*=============================================================================================*/
    public function joinUserCenter($center){
        if(config('app.debug')){
            Auth::loginUsingId(1);
        };
        $userId=Auth::id();

        $membershipExists=DB::table('user_center')->where([
            'user_id'=>$userId,
            'center_id'=>$center
        ])->exists();
        if($membershipExists){
            return response()->json(['message'=>"User already joined this center"],409);
        }
        // ! dont forget to change it later for user center role
        $membership_role_id=Role::where("name",'user')->first()->id;
        DB::table('user_center')->insert([
            'user_id'=>$userId,
            'center_id'=>$center,
            'role_id'=>$membership_role_id
        ]);
        return response()->json(['message'=>"Successfully joined the center"],201);
    }
    // 
    public function assignUserCenterRole(Request $request){
        /*=============================================================================================*/
        /* 
            if user role was true and exist then do nothing
            if user role was true and dont exist add that role to that user in that center
            if user role was false and exist then delete it
            if user role was false and dont exist then do nothing
        */
        /*=============================================================================================*/
        $data=$request->validate([
            'user_id'=>['required','exists:users,id'],
            'center_id'=>['required','exists:centers,id'],
            "roles"=>['nullable','array'],
            'roles.*.id'=>['required','exists:roles,id','distinct'],
            "roles.*.value"=>['required','boolean']
            ]);
        if($data['roles']){
            foreach($data['roles'] as $role){
                    $recordExists=DB::table('user_center')->where([
                        'user_id'=>$data['user_id'],
                        'center_id'=>$data['center_id'],
                        'role_id'=>$role['id']
                    ])->first();
                if($role['value']){
                    if(!$recordExists){
                        DB::table('user_center')->insert([
                            'user_id'=>$data['user_id'],
                            'center_id'=>$data['center_id'],
                            'role_id'=>$role['id']
                        ]);
                    }
                }else{
                    if($recordExists){
                        DB::table("user_center")->where([
                            'user_id'=>$data['user_id'],
                            'center_id'=>$data['center_id'],
                            'role_id'=>$role['id']
                        ]);
                    }
                }
                }
        }
        /**@disregard */
        /*
        ! there is a problem here , if user is member , then how to toggle things up
        ! like user can have multiple roles , how can toggle admin role if exist
        ! user_id and center_id dont change , only role_id is changing ,note for js map
        */
    return response()->json(["message"=>"User Center Role was assigned successfully"]);
    }
}
