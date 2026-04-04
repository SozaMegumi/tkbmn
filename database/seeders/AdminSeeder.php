<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    // Create Admin
    \Illuminate\Support\Facades\DB::table('admins')->insert([
        'name' => 'Super Admin',
        'email' => 'admin@kemas.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create a Class
    \Illuminate\Support\Facades\DB::table('classrooms')->insert([
        'class_name' => 'Tabika A (Bunga Raya)',
        'created_at' => now(),
    ]);
}
}
