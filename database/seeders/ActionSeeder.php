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
        $actions=[["name"=>"prayers","description"=>"Read or control praying times"]];

        foreach($actions as $action){
            Action::updateOrCreate($action);
        }
    }
}
