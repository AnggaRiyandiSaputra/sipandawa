<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('settings')->insert([
            'id' => 1,
            'pajak' => 11,
            'kas' => 9,
            'komisi' => 79,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
