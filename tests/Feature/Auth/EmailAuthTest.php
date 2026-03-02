<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\seed;

describe("User Auth Test",function(){
    it("register user",function(){
        assertDatabaseCount('users',0);
        assertDatabaseCount('user_role',0);
        seed();
        $newUser=[
            "name"=>"test user",
            "email"=>"test@gmail.com",
            "password"=>"12345678",
            "password_confirmation"=>"12345678"
            ];
            $response = postJson(route('register'),$newUser);
            $response->assertCreated()->assertJsonStructure(["message","token"]);
        assertDatabaseCount('user_role',1);
            assertDatabaseCount('users',1);
            });

        it("login user",function(){
            $user=User::factory()->create(["password"=>'12345678']);
            $userData=['email'=>$user->email,"password"=>"12345678"];
            $response=postJson(route("login"),$userData);
            $response->assertOk();
        });

        it("logs out authenticated user", function () {
            // $response = postJson(route('logout'));
            // $response->assertOk(); // or assertNoContent()
            // assertGuest(); // 🔥 THIS is the real check
});

});

describe("User Piviot and Relationships Test",function(){
    it("creates user_role when register",function(){
        seed();
        assertDatabaseCount("user_role",0);
        assertDatabaseCount('users',0);
        $response=postJson(route("register"),[
            "name"=>"shadow test",
            "email"=>"test@gmail.com",
            "password"=>"12345678",
            "password_confirmation"=>"12345678"
        ]);
        $response->assertCreated()->assertJsonStructure(['message']);
        assertDatabaseCount("user_role",1);
        assertDatabaseCount('users',1);
    });
});

describe("User Auth Validation Test", function () {

    it("reject login if credentials were missing", function () {
        postJson(route('login'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    });


    it("reject register if credentials were missing", function () {
        assertDatabaseCount('users', 0);
        postJson(route('register'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);

        assertDatabaseCount('users', 0); // ensure nothing created
    });


    it("return error if user credentials are wrong when login", function () {
        $user = User::factory()->create([
            'password' => bcrypt('12345678'),
        ]);

        postJson(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertUnauthorized(); // 401 makes more sense than 422
    });


    it("returns error if user already exist when register", function () {
        $user = User::factory()->create([
            'email' => 'test@gmail.com'
        ]);

        assertDatabaseCount('users', 1);

        postJson(route('register'), [
            'name' => 'test user',
            'email' => 'test@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        assertDatabaseCount('users', 1); // still only one
    });

});

// describe("User Auth Validation Test",function(){
//     it("reject login if credentials were missing");
//     it("reject register if credentials were missing");
//     it("return error if user credentials are wrong when login");
//     it("returns error if user already exist when register");
// });