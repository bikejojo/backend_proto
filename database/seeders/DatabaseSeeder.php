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
use App\Models\Type;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

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
            'type_user' => 3,
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

        Habilidad::create(['name' => 'Plomeria','status'=>1,'icons'=>'account-wrench-outline']); //1
        Habilidad::create(['name' => 'Electricista','status'=>1,'icons'=>'fuse-alert']); //1
        Habilidad::create(['name' => 'Carpinteria','status'=>1,'icons'=>'hammer']); //1
        Habilidad::create(['name' => 'Pintor','status'=>1,'icons'=>'format-paint']); //1
        Habilidad::create(['name' => 'Mecanico automotriz','status'=>1,'icons'=>'car-cog']); //3
        Habilidad::create(['name' => 'Soldador','status'=>1,'icons'=>'soldering-iron']); //3
        Habilidad::create(['name' => 'Jardineria','status'=>1,'icons'=>'mower-bag']); //4
        Habilidad::create(['name' => 'Albanileria','status'=>1,'icons'=>'hammer-screwdriver']); //1
        Habilidad::create(['name' => 'Cocinero','status'=>1,'icons'=>'chef-hat']);
        Habilidad::create(['name' => 'Cerrajero','status'=>1 ,'icons'=>'key-chain-variant']); //4
        Habilidad::create(['name' => 'Pintor de obras','status'=>1 ,'icons'=>'format-color-fill']); //4
        Habilidad::create(['name' => 'Fumigador','status'=>1 ,'icons'=>'sprinkler-variant']); //4
        Habilidad::create(['name' => 'Vidriero','status'=>1 ,'icons'=>'google-glass']); //4
        Habilidad::create(['name' => 'Tecnico en computacion','status'=>1,'icons'=>'cable-data']); //2
        Habilidad::create(['name' => 'Tecnico en redes electricas','status'=>1 ,'icons'=>'lightning-bolt-circle']); //2
        Habilidad::create(['name' => 'Tecnico en electrodomesticos','status'=>1 ,'icons'=>'fridge-alert-outline']); //2
        Habilidad::create(['name' => 'Limpieza General','status'=>1 ,'icons'=>'format-clear']); //1
        Habilidad::create(['name' => 'Tecnico en telefonia movil','status'=>1 ,'icons'=>'cellphone-cog']); //2
        Habilidad::create(['name' => 'Veterinario(a)','status'=>1 ,'icons'=>'doctor']); //2

        Habilidad::create(['name' => 'piscenero','status'=>1 ,'icons'=>'pool']); //2
        Habilidad::create(['name' => 'limpieza general en vehiculo','status'=>1 ,'icons'=>'spray-bottle' ]); //2
        Habilidad::create(['name' => 'mesero / camarero / mozo','status'=>1 ,'icons'=>'silverware-fork-knife' ]); //2
        Habilidad::create(['name' => 'Organizador de eventos','status'=>1 ,'icons'=>'party-popper']); //2
        Habilidad::create(['name' => 'Arrendador  de mobiliario','status'=>1 ,'icons'=>'sofa-single']); //2
        Habilidad::create(['name' => 'bartender / barman','status'=>1 ,'icons'=>'glass-cocktail']); //2
        Habilidad::create(['name' => 'Vocalista','status'=>1 ,'icons'=>'microphone-message']); //2

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
        Tipo_Estado::create(['description'=>'Aceptado']);
        Tipo_Estado::create(['description'=>'Rechazado']);
        Tipo_Estado::create(['description'=>'Terminado']);
        Tipo_Estado::create(['description'=>'Completado']);

        Categoria_Publicidad::create(['description'=>'tecnologia','entity_type'=>'publicity','code'=>'PUB001']);
        Categoria_Publicidad::create(['description'=>'servicios','entity_type'=>'publicity','code'=>'PUB002']);
        Categoria_Publicidad::create(['description'=>'productos','entity_type'=>'publicity','code'=>'PUB003']);
        Categoria_Publicidad::create(['description'=>'consultoria','entity_type'=>'publicity','code'=>'PUB004']);

        //suscripcion inicial
        Suscripcion::create(['name'=>'Suscripcion Free','description'=>'Duracion de 7 dias por Free','createDate'=>Carbon::now(),'duration'=> 7,'status'=>1,'durationDescription'=>'7 dias','price'=>0,'codeSubcription'=>'FREE']);
        Suscripcion::create(['name'=>'Suscripcion Basic','description'=>'Duracion de 14 dias por basico','createDate'=>Carbon::now(),'duration'=> 14,'status'=>1,'durationDescription'=>'14 dias','price'=>12.50,'codeSubcription'=>'BASIC']);
        Suscripcion::create(['name'=>'Suscripcion Esencial','description'=>'Duracion de 30 dias por esencial','createDate'=>Carbon::now(),'duration'=> 30,'status'=>1,'durationDescription'=>'30 dias','price'=>25.0,'codeSubcription'=>'ESENCIAL']);
        Suscripcion::create(['name'=>'Suscripcion Premiun','description'=>'Duracion de 60 dias por premium','createDate'=>Carbon::now(),'duration'=> 60,'status'=>1,'durationDescription'=>'60 dias','price'=>60.0,'codeSubcription'=>'PREMIUN']);

        Type::create(['description'=>'Solicitud enviada por el cliente','code_notifications'=>'SOL_CL']);
        Type::create(['description'=>'Solicitud enviada por el tecnico','code_notifications'=>'SOL_TC']);
        Type::create(['description'=>'Publicidad','code_notifications'=>'PUBLIC']);
        Type::create(['description'=>'Solicitud','code_notifications'=>'SOLIC']);

        /*Role::create(['name'=>'Soporte']);
        Role::create(['name'=>'Comerial']);
        Role::create(['name'=>'Administrativo']);*/
        $this->call(RoleSeeder::class);
    }
}
