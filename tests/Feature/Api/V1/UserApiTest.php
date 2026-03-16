<?php

use App\Helpers\Api\UserApiResponse;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;
use function Pest\Laravel\seed;

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
        $response = getJson('/api/v1/users');
        $response->assertOk()->assertJsonStructure(['data']);
    });
    it("get a specific user",function(){
        $userId=test()->user->id;
        $response = getJson("/api/v1/users/$userId");
        $response->assertOk()->assertJsonStructure(["data"]);
    });
    it("update a specific user",function(){
        $userId=test()->user->id;
        $newData=["name"=>"shadow test"];
        $response=putJson("/api/v1/users/$userId",$newData);
        $response->assertOk()->assertJsonStructure(["message"]);
    });
    it('delete a specific user',function(){
        assertDatabaseCount('users',1);
        $userId=test()->user->id;
        $response = deleteJson("/api/v1/users/$userId");
        $response->assertOk()->assertJsonStructure(["message"]);
        assertDatabaseCount('users',0);
    });
});

describe("User Piviot and Relationships Test",function(){
    it("return error when creating duplicate roles in the same user", function () {
        seed();
        $userId=test()->user->id;
        $response = putJson("/api/v1/users/$userId",[ "roles" => [
            ["id" => 1],
            ["id" => 1],
        ]]);
        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['roles.0.id', 'roles.1.id']);

        // make sure nothing extra was inserted
        assertDatabaseCount("user_role", 0);
    });
    it("delete user_role when user is deleted",function(){
        seed();
        assertDatabaseCount("user_role",0);
        assertDatabaseCount('users',1);
        $newUser=[
            "name"=>"test user",
            "email"=>"test@gmail.com",
            "password"=>"12345678",
            "password_confirmation"=>"12345678"
            ];
            $response = postJson(route('register'),$newUser);
            $response->assertCreated()->assertJsonStructure(["message","token"]);
            assertDatabaseCount('user_role',1);
            assertDatabaseCount('users',2);
        $newUserId=User::where("name",'test user')->first()->id;
        $deleteResponse=deleteJson("/api/v1/users/$newUserId");
        $deleteResponse->assertOk()->assertJsonStructure(["message"]);
        assertDatabaseCount('user_role',0);
        assertDatabaseCount('users',1);
    });
});
/*
|--------------------------------------------------------------------------
| 404 / Not Found Tests
|--------------------------------------------------------------------------
*/
describe("User Api 404 Error Test",function(){
    it("return 404 when user not found (GET)",function(){
        $response =getJson("/api/v1/users/9999");
        $response->assertNotFound()->assertJson(test()->apiResponses->notFoundResponse()[0]);
    });
    it("return 404 when user not found (PUT)",function(){
        $response=putJson("/api/v1/users/9999",["name"=>"new test name"]);
        $response->assertNotFound()->assertJson(test()->apiResponses->notFoundResponse()[0]);
    });
    it("return 404 when user not found (DELETE)",function(){
        $response =deleteJson("/api/v1/users/9999");
        $response->assertNotFound()->assertJson(test()->apiResponses->notFoundResponse()[0]);
    });
});