<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'pending'],
            ['id' => 2, 'name' => 'in progress'],
            ['id' => 3, 'name' => 'completed'],
            ['id' => 4, 'name' => 'cancelled'],    
        ]);
    }
}
