<?php

use App\Helpers\Api\UserApiResponse;
use App\Models\User;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;

/*
|--------------------------------------------------------------------------
| CRUD Tests
|--------------------------------------------------------------------------
*/

beforeEach(function(){
    test()->apiResponses=new UserApiResponse();
    test()->user=User::factory()->create();
});

describe("User Api CRUD Test",function(){
    it("get all users",function(){
        $response = getJson('/api/users');
        $response->assertOk()->assertJsonStructure(['data']);
    });
    it("get a specific user",function(){
        $userId=test()->user->id;
        $response = getJson("/api/users/$userId");
        $response->assertOk()->assertJsonStructure(["data"]);
    });
    it("update a specific user",function(){
        $userId=test()->user->id;
        $newData=["name"=>"shadow test"];
        $response=putJson("/api/users/$userId",$newData);
        $response->assertOk()->assertJsonStructure(["message"]);
    });
    it('delete a specific user',function(){
        assertDatabaseCount('users',1);
        $userId=test()->user->id;
        $response = deleteJson("/api/users/$userId");
        $response->assertOk()->assertJsonStructure(["message"]);
        assertDatabaseCount('users',0);
    });
});

/*
|--------------------------------------------------------------------------
| 404 / Not Found Tests
|--------------------------------------------------------------------------
*/
describe("User Api 404 Error Test",function(){
    it("return 404 when user not found (GET)",function(){
        $response =getJson("/api/users/9999");
        $response->assertNotFound()->assertJson(test()->apiResponses->notFoundResponse()[0]);
    });
    it("return 404 when user not found (PUT)",function(){
        $response=putJson("/api/users/9999",["name"=>"new test name"]);
        $response->assertNotFound()->assertJson(test()->apiResponses->notFoundResponse()[0]);
    });
    it("return 404 when user not found (DELETE)",function(){
        $response =deleteJson("/api/users/9999");
        $response->assertNotFound()->assertJson(test()->apiResponses->notFoundResponse()[0]);
    });
});