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
            'contrasenia' => '123',
            'ci' => '123',
            'tipo_usuario' => 1,
            'token' => "1"
        ]);
        #ciudad para tecnicos y clientes
        Ciudad::create(['descripcion'=>'Santa Cruz']);
        Ciudad::create(['descripcion'=>'Cochabamba']);
        Ciudad::create(['descripcion'=>'Chuquisaca']);
        Ciudad::create(['descripcion'=>'Tarija']);
        Ciudad::create(['descripcion'=>'Beni']);
        Ciudad::create(['descripcion'=>'Pando']);
        Ciudad::create(['descripcion'=>'La Paz']);
        Ciudad::create(['descripcion'=>'El Alto']);
        Ciudad::create(['descripcion'=>'Oruro']);
        Ciudad::create(['descripcion'=>'Potosi']);


        #habilidades para tecnicos
        Habilidad::create(['nombre' => 'Plomeria']);
        Habilidad::create(['nombre' => 'Electricista']);
        Habilidad::create(['nombre' => 'Carpinteria']);
        Habilidad::create(['nombre' => 'Pintor']);
        Habilidad::create(['nombre' => 'Mecanico automotriz']);
        Habilidad::create(['nombre' => 'Soldador']);
        Habilidad::create(['nombre' => 'Jardineria']);
        Habilidad::create(['nombre' => 'Albanileria']);
        Habilidad::create(['nombre' => 'Cocinero']);
        Habilidad::create(['nombre' => 'Cerrajero']);
        Habilidad::create(['nombre' => 'Pintor de obras']);
        Habilidad::create(['nombre' => 'Fumigador']);
        Habilidad::create(['nombre' => 'Vidriero']);
        Habilidad::create(['nombre' => 'Tecnico en computacion']);
        Habilidad::create(['nombre' => 'Tecnico en redes electricas']);
        Habilidad::create(['nombre' => 'Tecnico en electrodomesticos']);
        Habilidad::create(['nombre' => 'Limpieza General']);
        Habilidad::create(['nombre' => 'Tecnico en telefonia movil']);

        Tipo_Actividad::create(['descripcion'=>'mantenimiento']);
        Tipo_Actividad::create(['descripcion'=>'reparacion']);
        Tipo_Actividad::create(['descripcion'=>'seguimiento']);
        Tipo_Actividad::create(['descripcion'=>'visita']);
        Tipo_Actividad::create(['descripcion'=>'finalizado']);
        #solicitud
        Tipo_Estado::create(['descripcion'=>'pendiente por aceptar']);
        Tipo_Estado::create(['descripcion'=>'aceptada']);
        Tipo_Estado::create(['descripcion'=>'cancelada por tiempo de espera']);
        Tipo_Estado::create(['descripcion'=>'rechazada']);
        #cita
        Tipo_Estado::create(['descripcion'=>'en progreso']);
        Tipo_Estado::create(['descripcion'=>'completada']);
        Tipo_Estado::create(['descripcion'=>'cliente ausente']);
        Tipo_Estado::create(['descripcion'=>'reprogramada']);
    }
}
