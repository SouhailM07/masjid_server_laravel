<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // 
        $userRoleActions = Action::whereIn("name", ["donations", "financial-reports"])
        ->get()
        ->map(fn ($a) => [
            "action_id" => $a->id,
            "read" => true,
        ])->toArray();

        $userRoleData=[
            "name"=>"user",
            "isPublic"=>false,
            "description"=>"Default user role when creating an account for the first time",
        ];
        Role::updateOrCreate($userRoleData)->actions()->sync($userRoleActions);
        $ownerRole=Role::updateOrCreate(['name'=>"owner",'isPublic'=>false,'description'=>"Project Grand Master"]);
        $actionIds=Action::pluck('id');
        $syncData=$actionIds->mapWithKeys(fn($id)=>[
            $id=>[
                'create'=>true,
                'read'=>true,
                'update'=>true,
                'delete'=>true
            ]
        ])->toArray();

        $ownerRole->actions()->sync($syncData);
    }
}
