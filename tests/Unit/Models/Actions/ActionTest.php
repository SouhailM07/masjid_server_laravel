<?php

use App\Models\Action;
use App\Models\Role;

use function Pest\Laravel\assertDatabaseCount;

describe("Action Model Relationships",function(){
    it("delete role_action after action get's deleted",function(){
        assertDatabaseCount('role_action',0);
        $action=Action::factory()->create(["name"=>"adhan"]);
        $role=Role::factory()->create();
        $role->actions()->attach($action->id);
        assertDatabaseCount('role_action',1);
        $action->delete();
        assertDatabaseCount('role_action',0);

    });
});