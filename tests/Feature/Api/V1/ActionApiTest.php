<?php

use App\Models\Action;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;


beforeEach(function () {
    test()->action = Action::factory()->create([
        'name' => 'Test Action',
        'isPublic'=>true,
        'description' => 'This is a test action'
    ]);
});

/*
|--------------------------------------------------------------------------
| CRUD Tests
|--------------------------------------------------------------------------
*/
describe('Action API CRUD Testing', function () {
    it('creates a new action', function () {
        $response = postJson('/api/v1/actions', [
            'name' => 'Another Test Action',
            'isPublic'=>true,
            'description' => 'Another This is a test action'
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'isPublic',
                    'name',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ]);
    });

    it('gets all actions', function () {
        $response = getJson('/api/v1/actions');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name','isPublic', 'description', 'created_at', 'updated_at']
                ]
            ]);
    });

    it('finds a specific action', function () {
        $response = getJson('/api/v1/actions/' . test()->action->id);

        assertDatabaseCount('actions', 1);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'isPublic','name', 'description', 'created_at', 'updated_at']
            ]);
    });

    it('updates an existing action', function () {
        $response = putJson('/api/v1/actions/' . test()->action->id, [
            'name' => 'Updated Test Name',
            'isPublic'=>false,
            'description' => 'Updated description'
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => ['id','isPublic', 'name', 'description', 'created_at', 'updated_at']
            ]);

            expect(test()->action->fresh())
                ->name->toBe('Updated Test Name')
                ->description->toBe('Updated description');
    });

    it('deletes an existing action', function () {
        $actionId = test()->action->id;
    
        $response = deleteJson('/api/v1/actions/' . $actionId);
        $response->assertOk()->assertJsonStructure(['message']);

        expect(Action::find($actionId))->toBeNull();
    });
});

/*
|--------------------------------------------------------------------------
| Validation Tests
|--------------------------------------------------------------------------
*/
describe('Action API Validation Testing', function () {
    it('rejects empty name when creating an action',function(){
        $response = postJson('/api/v1/actions',[
            'name'=>"",
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('requires name when creating an action', function () {
        $response = postJson('/api/v1/actions', [
            'description' => 'Missing name'
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('validates unique name when creating an action', function () {
        $response = postJson('/api/v1/actions', [
            'name' => 'Test Action',
            'description' => 'Duplicate name'
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });
    it('validates unique name when updating an action', function () {
        $newAction = Action::factory()->create([
            'name' => 'Unique Name Test',
            'description' => 'Testing unique name validation on update'
        ]);

        $response = putJson('/api/v1/actions/' . test()->action->id, [
            'name' => 'Unique Name Test',
            'description' => 'Trying to update with duplicate name'
        ]);
        
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });
});

/*
|--------------------------------------------------------------------------
| 404 / Not Found Tests
|--------------------------------------------------------------------------
*/
describe('Action API 404 Error Testing', function () {
    it('returns 404 when action not found (GET)', function () {
        $response = getJson('/api/v1/actions/999');
        $response->assertNotFound()->assertJson(['message' => 'Action not found']);
    });

    it('returns 404 when action not found (PUT)', function () {
        $response = putJson('/api/v1/actions/999', [
            'name' => 'Update Test',
            'description' => 'Trying to update nonexistent action'
        ]);

        $response->assertNotFound()->assertJson(['message' => 'Action not found']);
    });

    it('returns 404 when action not found (DELETE)', function () {
        $response = deleteJson('/api/v1/actions/999');
        $response->assertNotFound()->assertJson(['message' => 'Action not found']);
    });
});