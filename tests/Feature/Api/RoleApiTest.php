<?php

// ! api for result testing

use App\Helpers\Api\RoleApiResponse;
use App\Models\Action;
use App\Models\Role;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function(){
    test()->apiResponses=new RoleApiResponse();
    test()->role=Role::factory()->create([
        'name'=>"Test Role",
        'isPublic'=>false,
        "description"=>"This is a test role"
    ]);
});

/*
|--------------------------------------------------------------------------
| CRUD Tests
|--------------------------------------------------------------------------
*/
describe("Role Api CRUD Testing",function(){
    it('create a new role',function(){
        $response = postJson("/api/roles",[
            "name"=>"new role",
            "isPublic"=>true,
            "description"=>"hi"
        ]);

        $response->assertCreated()->assertJson(test()->apiResponses->createResponse()[0]);
    });

    it("gets all roles",function(){
        $response= getJson("/api/roles");
        $response->assertOk()->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'created_at', 'updated_at']
                ]
]);
    });

    it("gets a specific role",function(){
        $roleId=test()->role->id;
        $response = getJson("/api/roles/$roleId");
        assertDatabaseCount('roles',1);

        $response->assertOk()->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'created_at', 'updated_at']
            ]);
    });

    it("update a specific role",function(){
        $newData=['name'=>"update role",'isPublic'=>true,"description"=>"update description"];
        $roleId=test()->role->id;
        $response = putJson("/api/roles/$roleId",$newData);

        $response->assertOk()->assertJson(test()->apiResponses->updateResponse()[0]);

    });

    it("delete a specific role",function(){
        $roleId=test()->role->id;
        $response = deleteJson("/api/roles/$roleId");
        assertDatabaseCount('roles',0);
        $response->assertOk()->assertJson(test()->apiResponses->deleteResponse()[0]);
    });
});

/*
|--------------------------------------------------------------------------
| Validation Tests
|--------------------------------------------------------------------------
*/
describe("Role Api Validation Testing",function(){
    it("rejects empty name and isPublic when creating",function(){
        $response=postJson("/api/roles",[
            "name"=>"",
            "isPublic"=>""
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(["name","isPublic"]);
    });
});
/*
|--------------------------------------------------------------------------
| Piviot and Relationships Tests
|--------------------------------------------------------------------------
*/
describe("Role Api Piviot and Relationships Tests",function(){
    it("creates a role with actions",function(){
        $action1=Action::factory()->create();
        // $action2=Action::factory()->create();
        $actions=[['id'=>$action1->id,'create'=>true]];
        $response=postJson("/api/roles",['name'=>"shadow warrior","isPublic"=>false,'actions'=>$actions]);
        $response->assertCreated()->assertJsonStructure(['message']);
    });
    it('update the actions of role',function(){
        $action=Action::factory()->create();
        $roleId=test()->role->id;
        $updateResponse=putJson("/api/roles/$roleId",["actions"=>[$action]]);
        $updateResponse->assertOk();
        assertDatabaseCount('role_action',1);
    });
    it("reject duplicated actions in the same role",function(){
        assertDatabaseCount('role_action',0);
        assertDatabaseCount("actions",0);
        $roleId=test()->role->id;
        $action=Action::factory()->create();
        assertDatabaseCount("actions",1);
        $response=putJson("/api/roles/$roleId",["actions"=>[$action,$action]]);
        $response->assertUnprocessable()->assertJsonValidationErrors(["actions.0.id","actions.1.id"]);
    });
    it('delete a role_action after deleting role',function(){
        $action=Action::factory()->create();
        $roleId=test()->role->id;
        $updateResponse=putJson("/api/roles/$roleId",["actions"=>[$action]]);
        $updateResponse->assertOk();
        assertDatabaseCount('role_action',1);
        $deleteResponse=deleteJson("/api/roles/$roleId");
        $deleteResponse->assertOk();
        assertDatabaseCount("role_action",0);
    });
});
/*
|--------------------------------------------------------------------------
| 404 / Not Found Tests
|--------------------------------------------------------------------------
*/

describe("Role API 404 Error Test",function(){
    it("returns 404 when role not found (GET)",function(){
        $response =getJson("/api/roles/9999");
        $response->assertNotFound()->assertJson(test()->apiResponses->notFoundResponse()[0]);
    });

    it("returns 404 when role not found (PUT)",function(){
        $newData=['name'=>"update role",'isPublic'=>true,"description"=>"update description"];
        $response=putJson('/api/roles/9999',$newData);
        $response->assertNotFound()->assertJson(test()->apiResponses->notFoundResponse()[0]);

    });

    it("returns 404 when role not found (DELETE)",function(){
        $response=deleteJson('/api/roles/9999');
        $response->assertNotFound()->assertJson(test()->apiResponses->notFoundResponse()[0]);
    });
});