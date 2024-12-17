<?php

namespace Database\Seeders;

use App\Models\Categoria_Publicidad;
use App\Models\User;
use App\Models\Ciudad;
use App\Models\Habilidad;
use App\Models\Tipo_Estado;
use App\Models\Tipo_Actividad;
use App\Models\Suscripcion;
use App\Models\Skills_group;
use App\Models\Group;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

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
        Habilidad::create(['name' => 'Plomeria']); //1
        Habilidad::create(['name' => 'Electricista']); //1
        Habilidad::create(['name' => 'Carpinteria']); //1
        Habilidad::create(['name' => 'Pintor']); //1
        Habilidad::create(['name' => 'Mecanico automotriz']); //3
        Habilidad::create(['name' => 'Soldador']); //3
        Habilidad::create(['name' => 'Jardineria']); //4
        Habilidad::create(['name' => 'Albanileria']); //1
        Habilidad::create(['name' => 'Cocinero']);
        Habilidad::create(['name' => 'Cerrajero']); //4
        Habilidad::create(['name' => 'Pintor de obras']); //4
        Habilidad::create(['name' => 'Fumigador']); //4
        Habilidad::create(['name' => 'Vidriero']); //4
        Habilidad::create(['name' => 'Tecnico en computacion']); //2
        Habilidad::create(['name' => 'Tecnico en redes electricas']); //2
        Habilidad::create(['name' => 'Tecnico en electrodomesticos']); //2
        Habilidad::create(['name' => 'Limpieza General']); //1
        Habilidad::create(['name' => 'Tecnico en telefonia movil']); //2
        #
        Group::create(['name'=>'Hogar y Mantenimiento']);
        Group::create(['name'=>'Tecnologia']);
        Group::create(['name'=>'Automotriz']);
        Group::create(['name'=>'Servicios Especializados']);
        #grupo de habilidades
        Skills_group::create(['groupId'=>1,'skillsId'=>1]);
        Skills_group::create(['groupId'=>1,'skillsId'=>2]);
        Skills_group::create(['groupId'=>1,'skillsId'=>3]);
        Skills_group::create(['groupId'=>1,'skillsId'=>4]);
        Skills_group::create(['groupId'=>1,'skillsId'=>8]);
        Skills_group::create(['groupId'=>1,'skillsId'=>9]);
        Skills_group::create(['groupId'=>1,'skillsId'=>17]);

        Skills_group::create(['groupId'=>2,'skillsId'=>14]);
        Skills_group::create(['groupId'=>2,'skillsId'=>15]);
        Skills_group::create(['groupId'=>2,'skillsId'=>16]);
        Skills_group::create(['groupId'=>2,'skillsId'=>18]);

        Skills_group::create(['groupId'=>3,'skillsId'=>5]);
        Skills_group::create(['groupId'=>3,'skillsId'=>6]);

        Skills_group::create(['groupId'=>4,'skillsId'=>7]);
        Skills_group::create(['groupId'=>4,'skillsId'=>10]);
        Skills_group::create(['groupId'=>4,'skillsId'=>11]);
        Skills_group::create(['groupId'=>4,'skillsId'=>12]);
        Skills_group::create(['groupId'=>4,'skillsId'=>13]);


        #agenda
        Tipo_Actividad::create(['description'=>'mantenimiento','entity_type'=>'service']);
        Tipo_Actividad::create(['description'=>'reparacion','entity_type'=>'service']);
        Tipo_Actividad::create(['description'=>'instalacion','entity_type'=>'service']);
        Tipo_Actividad::create(['description'=>'inspeccion','entity_type'=>'service']);
        #solicitud
        Tipo_Estado::create(['description'=>'Pendiente']);
        Tipo_Estado::create(['description'=>'Rechazado']);
        Tipo_Estado::create(['description'=>'Aceptado']);
        Tipo_Estado::create(['description'=>'Terminado']);

        Categoria_Publicidad::create(['description'=>'tecnologia','entity_type'=>'publicity','code'=>'PUB001']);
        Categoria_Publicidad::create(['description'=>'servicios','entity_type'=>'publicity','code'=>'PUB002']);
        Categoria_Publicidad::create(['description'=>'productos','entity_type'=>'publicity','code'=>'PUB003']);
        Categoria_Publicidad::create(['description'=>'consultoria','entity_type'=>'publicity','code'=>'PUB004']);

        //suscripcion inicial
        Suscripcion::create(['name'=>'Suscripcion Free','description'=>'Duracion de 7 dias por Free','createDate'=>Carbon::now(),'duration'=> 7,'status'=>1,'durationDescription'=>'7 dias','price'=>0,'codeSubcription'=>'FREE']);
    }
}
