<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Admin Pandawa',
            'email' => 'pandawa@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('adminpandawa'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
