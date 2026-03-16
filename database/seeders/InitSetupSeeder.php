<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $owner=User::create([
            'name'=>'Shadow Warrior',
            'email'=>"lightshadow416@gmail.com",
            'password'=>bcrypt('shadow_init_07')
        ]);
        $ownerRole=Role::where('name','owner')->first();
        $owner->roles()->attach($ownerRole);
    }
}
