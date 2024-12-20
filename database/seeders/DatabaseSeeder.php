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
use App\Models\Sub_group;
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

        Group::create(['name'=>'Hogar y Mantenimiento']);
        Group::create(['name'=>'Tecnologia']);
        Group::create(['name'=>'Automotriz']);
        Group::create(['name'=>'Servicios Especializados']);
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


        Sub_group::create(['description' => 'Aires Acondicionados', 'createDate' => now()]);
        Sub_group::create(['description' => 'Calefonos', 'createDate' => now()]);
        Sub_group::create(['description' => 'Electricistas', 'createDate' => now()]);
        Sub_group::create(['description' => 'Plomero', 'createDate' => now()]);
        Sub_group::create(['description' => 'Piscinero / Jardineria', 'createDate' => now()]);
        Sub_group::create(['description' => 'Veterinario', 'createDate' => now()]);
        Sub_group::create(['description' => 'Pintores', 'createDate' => now()]);
        Sub_group::create(['description' => 'Cocinas / Heladeras / Lavadoras / Hornos', 'createDate' => now()]);
        Sub_group::create(['description' => 'Zapatero', 'createDate' => now()]);
        Sub_group::create(['description' => 'Lava autos', 'createDate' => now()]);
        Sub_group::create(['description' => 'Cambios de aceites / Recojo de vehículos', 'createDate' => now()]);
        Sub_group::create(['description' => 'Baterías', 'createDate' => now()]);
        Sub_group::create(['description' => 'Servicio de computación', 'createDate' => now()]);
        Sub_group::create(['description' => 'Copia de llaves', 'createDate' => now()]);
        Sub_group::create(['description' => 'Servicio de grúa', 'createDate' => now()]);
        Sub_group::create(['description' => 'Catering / Churrasquero', 'createDate' => now()]);
        Sub_group::create(['description' => 'Garzones', 'createDate' => now()]);
        Sub_group::create(['description' => 'Alquiler sillas, mesas, vajillas', 'createDate' => now()]);
        Sub_group::create(['description' => 'Barra de bebidas / Licorería', 'createDate' => now()]);
        Sub_group::create(['description' => 'Grupo musical, mariachi, bandas', 'createDate' => now()]);


/*


/*
        Group::create(['name'=>'Servicios Técnicos y de Mantenimiento']);
        Group::create(['name'=>'Servicios para el Hogar y Llaves']);
        Group::create(['name'=>'Servicios de Transporte y Automotriz']);
        Group::create(['name'=>'Servicios de Catering y Eventos']); */
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
        Suscripcion::create(['name'=>'Suscripcion Basic','description'=>'Duracion de 14 dias por basico','createDate'=>Carbon::now(),'duration'=> 14,'status'=>1,'durationDescription'=>'14 dias','price'=>12.50,'codeSubcription'=>'BASIC']);
        Suscripcion::create(['name'=>'Suscripcion Esencial','description'=>'Duracion de 30 dias por esencial','createDate'=>Carbon::now(),'duration'=> 30,'status'=>1,'durationDescription'=>'30 dias','price'=>25.0,'codeSubcription'=>'ESENCIAL']);
        Suscripcion::create(['name'=>'Suscripcion Premiun','description'=>'Duracion de 60 dias por premium','createDate'=>Carbon::now(),'duration'=> 60,'status'=>1,'durationDescription'=>'60 dias','price'=>60.0,'codeSubcription'=>'PREMIUN']);
    }
}
