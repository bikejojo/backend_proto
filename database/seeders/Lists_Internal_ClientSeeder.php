<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lists_Internal_Client;

class Lists_Internal_ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 50 registros de list_internal_clients
        Lists_Internal_Client::factory()->count(10)->create();      

    }
}
