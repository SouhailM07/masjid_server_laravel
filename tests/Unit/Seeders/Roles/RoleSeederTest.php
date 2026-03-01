<?php

use App\Models\Role;

use function Pest\Laravel\seed;

it('role seeder creates base roles', function () {

    seed(); // or specific seeder

    expect(Role::where('name', 'admin')->exists())->toBeTrue();
});