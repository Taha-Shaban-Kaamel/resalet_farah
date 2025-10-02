<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class dataBaseSeader extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      
        $user = User::create([
            "name" => "super admin",
            "email" => "tahashaban743@gmail.com",
            "password" => "12345678",
        ]);

        $user->assignRole('super admin');
    }
}
