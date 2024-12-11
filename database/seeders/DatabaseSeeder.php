<?php

namespace Database\Seeders;

use App\Models\Categoria_Publicidad;
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
        Tipo_Actividad::create(['description'=>'mantenimiento','entity_type'=>'service']);
        Tipo_Actividad::create(['description'=>'reparacion','entity_type'=>'service']);
        Tipo_Actividad::create(['description'=>'instalacion','entity_type'=>'service']);
        Tipo_Actividad::create(['description'=>'inspeccion','entity_type'=>'service']);
        #solicitud
        Tipo_Estado::create(['description'=>'pendiente por aceptar','entity_type'=>'request']);
        Tipo_Estado::create(['description'=>'rechazado por cliente.','entity_type'=>'request']);
        Tipo_Estado::create(['description'=>'aceptado','entity_type'=>'request']);
        #servicio
        Tipo_Estado::create(['description'=>'pendiente','entity_type'=>'service']);
        Tipo_Estado::create(['description'=>'terminado','entity_type'=>'service']);

        Tipo_Estado::create(['description'=>'rechazado por tecnico','entity_type'=>'request']);

        Categoria_Publicidad::create(['description'=>'tecnologia','entity_type'=>'publicity','code'=>'PUB001']);
        Categoria_Publicidad::create(['description'=>'servicios','entity_type'=>'publicity','code'=>'PUB002']);
        Categoria_Publicidad::create(['description'=>'productos','entity_type'=>'publicity','code'=>'PUB003']);
        Categoria_Publicidad::create(['description'=>'consultoria','entity_type'=>'publicity','code'=>'PUB004']);
    }
}
