<?php

// ! model for behavior testing
// ! [piviots, relations]

use App\Models\Role;
use App\Models\Action;
use App\Models\User;
use Illuminate\Database\QueryException;

use function Pest\Laravel\assertDatabaseCount;

beforeEach(function(){
    test()->role=Role::factory()->create();
});

describe('Role Model Relationships', function () {

    it('role can attach multiple actions', function () {

        $actions = Action::factory()->count(3)->create();

        test()->role->actions()->attach($actions->pluck('id'));

        expect(test()->role->actions)->toHaveCount(3);

        assertDatabaseCount('role_action', 3);
    });

    it('role can sync actions', function () {

        $actions = Action::factory()->count(5)->create();

        test()->role->actions()->attach($actions->pluck('id'));

        $newActions = Action::factory()->count(2)->create();

        test()->role->actions()->sync($newActions->pluck('id'));

        expect(test()->role->actions)->toHaveCount(2);
    });

    it('does not duplicate pivot entries if unique constraint exists', function () {
        $role=test()->role;
        $action = Action::factory()->create();

        $role->actions()->attach($action->id);

        expect(function () use ($role, $action) {
            $role->actions()->attach($action->id);
        })->toThrow(QueryException::class,'Duplicate entry');
    });

    it('deleted role_action after deleting role',function(){
        $role=test()->role;
        $action = Action::factory()->create();

        $role->actions()->attach($action->id);
        assertDatabaseCount('role_action',1);
        $role->delete();
        assertDatabaseCount('role_action',0);
    });

    it("deleted user_role after deleting role",function(){
        assertDatabaseCount("user_role",0);
        assertDatabaseCount("users",0);
        assertDatabaseCount("roles",1);
        $user=User::factory()->create();
        assertDatabaseCount("users",1);
        $role=test()->role;
        $user->roles()->attach($role->id);
        assertDatabaseCount("user_role",1);
        $role->delete();
        assertDatabaseCount("roles",0);
        assertDatabaseCount("users",1);
        assertDatabaseCount("user_role",0);
    });
});