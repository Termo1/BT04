<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'first_name' => 'Adam',
                'last_name' => 'Admin',
                'email' => 'adamburansky9@gmail.com',
                'password' => Hash::make('test'),
                'role' => 'admin',
                'premium_until' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Dávid',
                'last_name' => 'Držík',
                'email' => 'ddrzik@ukf.sk',
                'password' => Hash::make('test'),
                'role' => 'user',
                'premium_until' => now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Jozef',
                'last_name' => 'Kapusta',
                'email' => 'jkapusta@ukf.sk',
                'password' => Hash::make('test'),
                'role' => 'user',
                'premium_until' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Mária',
                'last_name' => 'Nováková',
                'email' => 'mnovaková@ukf.sk',
                'password' => Hash::make('test'),
                'role' => 'user',
                'premium_until' => now()->addDays(60),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Peter',
                'last_name' => 'Horváth',
                'email' => 'phorvath@ukf.sk',
                'password' => Hash::make('test'),
                'role' => 'user',
                'premium_until' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}