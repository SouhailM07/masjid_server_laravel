<?php

namespace Database\Seeders;

use App\Models\Action;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $actions=require database_path("data/actions.php");

        foreach($actions as $action){
            Action::factory()->create([...$action,'isPublic'=>true]);
        }
    }
}
