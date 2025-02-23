<?php

namespace Database\Seeders;

use App\Models\GenderMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GenderMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $genders = [
            'Male',
            'Female'
        ];

        GenderMaster::truncate();

        foreach($genders as $gender){
            GenderMaster::create([
                'name'=>$gender,
                'slug'=> Str::slug($gender)
            ]);
        }


    }
}
