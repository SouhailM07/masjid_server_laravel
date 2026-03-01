<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;

use function Pest\Laravel\assertDatabaseCount;

beforeEach(function(){
    test()->user=User::factory()->create();
});

describe("User Model Relationships",function(){
    it("user can attach multiple roles",function(){
        $count=2;
        $user=test()->user;
        assertDatabaseCount('roles',0);
        $roles=Role::factory($count)->create()->pluck('id');
        assertDatabaseCount('roles',$count);
        $user->roles()->attach($roles);
        assertDatabaseCount('user_role',$count);
    });
    it("user can sync roles",function(){
        $user=test()->user;
        $count=4;
        $roles=Role::factory($count)->create()->pluck('id');
        assertDatabaseCount('roles',$count);
        $user->roles()->sync($roles);
        assertDatabaseCount('user_role',$count);
    });
    it("does not duplicate pivot entries if unique constraint exists",function(){
        $user=test()->user;
        $role=Role::factory()->create()->pluck('id');
        $user->roles()->attach($role);
        expect(function() use ($user,$role){
            $user->roles()->attach($role);
        })->toThrow(QueryException::class,'Duplicate entry');
    });
    it("delete user_role after deleting user",function(){
        $user=test()->user;
        assertDatabaseCount('users',1);
        assertDatabaseCount('user_role',0);
        assertDatabaseCount("roles",0);
        $roles=Role::factory()->create()->pluck('id');
        assertDatabaseCount("roles",1);
        test()->user->roles()->attach($roles);
        assertDatabaseCount('user_role',1);
        $user->delete();
        assertDatabaseCount('users',0);
        assertDatabaseCount('user_role',0);
        assertDatabaseCount('roles',1);
    });
});