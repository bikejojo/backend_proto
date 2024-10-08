<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ciudad;
use App\Models\Habilidad;
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
            'password' => '123',
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
        Habilidad::create(['nombre' => 'Pastelero']);
        Habilidad::create(['nombre' => 'Costurero']);
        Habilidad::create(['nombre' => 'Repostero']);
        Habilidad::create(['nombre' => 'Sastre']);
        Habilidad::create(['nombre' => 'Peluquero']);
        Habilidad::create(['nombre' => 'Manicurista']);
        Habilidad::create(['nombre' => 'Maquillador']);
        Habilidad::create(['nombre' => 'Panadero']);
        Habilidad::create(['nombre' => 'Cerrajero']);
        Habilidad::create(['nombre' => 'Pintor de obras']);
        Habilidad::create(['nombre' => 'Fumigador']);
        Habilidad::create(['nombre' => 'Ninera']);
        Habilidad::create(['nombre' => 'Cuidador de ancianos']);
        Habilidad::create(['nombre' => 'Tapicero']);
        Habilidad::create(['nombre' => 'Vidriero']);
        Habilidad::create(['nombre' => 'Tecnico en computacion']);
        Habilidad::create(['nombre' => 'Tecnico en redes electricas']);
        Habilidad::create(['nombre' => 'Tecnico en electrodomesticos']);
        Habilidad::create(['nombre' => 'Empleado de limpieza']);
        Habilidad::create(['nombre' => 'Vigilante']);
        Habilidad::create(['nombre' => 'Secretariado']);
        Habilidad::create(['nombre' => 'Ayudante de obra']);
        Habilidad::create(['nombre' => 'Agricultor']);
        Habilidad::create(['nombre' => 'Vendedor']);
        Habilidad::create(['nombre' => 'Mecanico industrial']);
        Habilidad::create(['nombre' => 'Tornero']);
        Habilidad::create(['nombre' => 'Confeccionista']);
        Habilidad::create(['nombre' => 'Electricista automotriz']);
        Habilidad::create(['nombre' => 'Fotografo']);
        Habilidad::create(['nombre' => 'Disenador grafico']);
        Habilidad::create(['nombre' => 'Tecnico en telefonia movil']);
        Habilidad::create(['nombre' => 'Chofer']);
        Habilidad::create(['nombre' => 'Operador de maquinaria pesada']);


    }
}
