<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = [
            [
                'name'=>'Admin',
                'password'=> Hash::make('123456'),
                'email'=>'admin@gmail.com',
            ]
        ];

        User::truncate();

        foreach($users as $user){
            User::create($user);
        }
    }
}
