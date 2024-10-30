<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Cliente_Interno;
use Illuminate\Database\Seeder;

class InternalClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Cliente_Interno::factory()->count(10)->create();

    }
}
