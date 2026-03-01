<?php

use App\Models\User;

beforeEach(function(){
    test()->user=User::factory()->create();
});