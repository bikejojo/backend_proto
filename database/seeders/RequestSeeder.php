<?php

namespace Database\Seeders;

use App\Models\Solicitud;
use Illuminate\Database\Seeder;
use Database\Factories\RequestFactorySeeder;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            RequestFactorySeeder::create(20);  // Generar 20 solicitudes
        } catch (\Exception $e) {
            $this->command->info($e->getMessage());
        }
    }
}
