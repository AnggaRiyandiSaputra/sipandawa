<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategoriesFinance extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories_finances')->insert([
            'id' => 1,
            'name' => 'Operasional',
            'type' => 'Pengeluaran',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('categories_finances')->insert([
            'id' => 2,
            'name' => 'Kas',
            'type' => 'Pemasukan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('categories_finances')->insert([
            'id' => 3,
            'name' => 'Pajak',
            'type' => 'Pemasukan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
