<?php

namespace Database\Seeders;

use App\Models\Detalle_Agenda_Tecnico;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Detalle_Agenda_Tecnico::factory()->count(100)->create();
    }
}
