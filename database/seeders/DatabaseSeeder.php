<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ciudad;
use App\Models\Habilidad;
use App\Models\Tipo_Estado;
use App\Models\Tipo_Actividad;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('123'),
            'ci' => '123',
            'type_user' => 1,
            'token' => "1"
        ]);
        #ciudad para tecnicos y clientes
        Ciudad::create(['name'=>'Santa Cruz']);
        Ciudad::create(['name'=>'Cochabamba']);
        Ciudad::create(['name'=>'Chuquisaca']);
        Ciudad::create(['name'=>'Tarija']);
        Ciudad::create(['name'=>'Beni']);
        Ciudad::create(['name'=>'Pando']);
        Ciudad::create(['name'=>'La Paz']);
        Ciudad::create(['name'=>'El Alto']);
        Ciudad::create(['name'=>'Oruro']);
        Ciudad::create(['name'=>'Potosi']);


        #habilidades para tecnicos
        Habilidad::create(['name' => 'Plomeria']);
        Habilidad::create(['name' => 'Electricista']);
        Habilidad::create(['name' => 'Carpinteria']);
        Habilidad::create(['name' => 'Pintor']);
        Habilidad::create(['name' => 'Mecanico automotriz']);
        Habilidad::create(['name' => 'Soldador']);
        Habilidad::create(['name' => 'Jardineria']);
        Habilidad::create(['name' => 'Albanileria']);
        Habilidad::create(['name' => 'Cocinero']);
        Habilidad::create(['name' => 'Cerrajero']);
        Habilidad::create(['name' => 'Pintor de obras']);
        Habilidad::create(['name' => 'Fumigador']);
        Habilidad::create(['name' => 'Vidriero']);
        Habilidad::create(['name' => 'Tecnico en computacion']);
        Habilidad::create(['name' => 'Tecnico en redes electricas']);
        Habilidad::create(['name' => 'Tecnico en electrodomesticos']);
        Habilidad::create(['name' => 'Limpieza General']);
        Habilidad::create(['name' => 'Tecnico en telefonia movil']);
        #agenda
        Tipo_Actividad::create(['description'=>'mantenimiento']);
        Tipo_Actividad::create(['description'=>'reparacion']);
        Tipo_Actividad::create(['description'=>'seguimiento']);
        Tipo_Actividad::create(['description'=>'visita']);
        Tipo_Actividad::create(['description'=>'finalizado']);
        #solicitud
        Tipo_Estado::create(['description'=>'pendiente por aceptar']);
        Tipo_Estado::create(['description'=>'rechazada por tiempo de espera']);
        Tipo_Estado::create(['description'=>'en conversacion']);
        Tipo_Estado::create(['description'=>'rechazada por tiempo']);
        Tipo_Estado::create(['description'=>'rechazada por tecnico']);
        Tipo_Estado::create(['description'=>'aceptada']);
        #cita
        Tipo_Estado::create(['description'=>'en progreso']);
        Tipo_Estado::create(['description'=>'cliente ausente']);
        Tipo_Estado::create(['description'=>'reprogramada']);
        Tipo_Estado::create(['description'=>'completada']);
    }
}
