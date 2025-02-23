<?php

namespace Database\Seeders;

use App\Models\CustomFieldMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomFieldMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $fields = [
            'text',
            'email',
            'phone',
            'textarea',
            'date',
            'url',
            'file',
        ];

        CustomFieldMaster::truncate();

        foreach($fields as $field){
            CustomFieldMaster::create(
                [
                    'type'=>$field
                ]
            );
        }

    }
}
