<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Create a Teacher
        DB::table('teachers')->insert([
            'full_name' => 'Cikgu Sarah',
            'username' => 'cikgusarah',
            'email' => 'teacher@kemas.com',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create a Parent
        DB::table('parents')->insert([
            'parent_name' => 'Encik Ali',
            'username' => 'ali_baba',
            'email' => 'parent@kemas.com',
            'password' => Hash::make('password123'),
            'phone_number' => '0123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}