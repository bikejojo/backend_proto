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
use App\Models\Group_Subgroup;
use App\Models\SubGroup_skill;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

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

        /*Group::create(['name'=>'Hogar y Mantenimiento']);//1
        Group::create(['name'=>'Tecnologia']);//2
        Group::create(['name'=>'Automotriz']);//3
        Group::create(['name'=>'Servicios Especializados']);//4*/

        $this->call(GropSeeder::class);
        $this->call(SubGropSeeder::class);
        $this->call(SkillSeeder::class);
        #habilidades para tecnicos

       /* Habilidad::create(['name' => 'Plomeria','status'=>1,'icons'=>'account-wrench-outline']); //1
        Habilidad::create(['name' => 'Electricista','status'=>1,'icons'=>'fuse-alert']); //2
        Habilidad::create(['name' => 'Carpinteria','status'=>1,'icons'=>'hammer']); //3
        Habilidad::create(['name' => 'Pintor','status'=>1,'icons'=>'format-paint']); //4
        Habilidad::create(['name' => 'Mecanico automotriz','status'=>1,'icons'=>'car-cog']); //5
        Habilidad::create(['name' => 'Soldador','status'=>1,'icons'=>'soldering-iron']); //6
        Habilidad::create(['name' => 'Jardineria','status'=>1,'icons'=>'mower-bag']); //7
        Habilidad::create(['name' => 'Albanileria','status'=>1,'icons'=>'hammer-screwdriver']); //8
        Habilidad::create(['name' => 'Cocinero','status'=>1,'icons'=>'chef-hat']);//9
        Habilidad::create(['name' => 'Cerrajero','status'=>1 ,'icons'=>'key-chain-variant']); //10
        Habilidad::create(['name' => 'Pintor de obras','status'=>1 ,'icons'=>'format-color-fill']); //11
        Habilidad::create(['name' => 'Fumigador','status'=>1 ,'icons'=>'sprinkler-variant']); //12
        Habilidad::create(['name' => 'Vidriero','status'=>1 ,'icons'=>'google-glass']); //13
        Habilidad::create(['name' => 'Tecnico en computacion','status'=>1,'icons'=>'cable-data']); //14
        Habilidad::create(['name' => 'Tecnico en redes electricas','status'=>1 ,'icons'=>'lightning-bolt-circle']); //15
        Habilidad::create(['name' => 'Tecnico en electrodomesticos','status'=>1 ,'icons'=>'fridge-alert-outline']); //16
        Habilidad::create(['name' => 'Limpieza General','status'=>1 ,'icons'=>'format-clear']); //17
        Habilidad::create(['name' => 'Tecnico en telefonia movil','status'=>1 ,'icons'=>'cellphone-cog']); //18
        Habilidad::create(['name' => 'Veterinario(a)','status'=>1 ,'icons'=>'doctor']); //19
        Habilidad::create(['name' => 'piscenero','status'=>1 ,'icons'=>'pool']); //20
        Habilidad::create(['name' => 'limpieza general en vehiculo','status'=>1 ,'icons'=>'spray-bottle' ]); //21
        Habilidad::create(['name' => 'mesero / camarero / mozo','status'=>1 ,'icons'=>'silverware-fork-knife' ]); //22
        Habilidad::create(['name' => 'Organizador de eventos','status'=>1 ,'icons'=>'party-popper']); //23
        Habilidad::create(['name' => 'Arrendador  de mobiliario','status'=>1 ,'icons'=>'sofa-single']); //24
        Habilidad::create(['name' => 'bartender / barman','status'=>1 ,'icons'=>'glass-cocktail']); //25
        Habilidad::create(['name' => 'Vocalista','status'=>1 ,'icons'=>'microphone-message']); //26
        Habilidad::create(['name' => 'Operador de grua','status'=>1 ,'icons'=>'microphone-message']); //27
        Habilidad::create(['name' => 'Zapatero / Reparador de calzado','status'=>1 ,'icons'=>'microphone-message']);// */

        /*Sub_group::create(['description' => 'Aires Acondicionados', 'createDate' => now()]);//1
        Sub_group::create(['description' => 'Calefonos', 'createDate' => now()]);//2
        Sub_group::create(['description' => 'Electricistas', 'createDate' => now()]);//3
        Sub_group::create(['description' => 'Plomero', 'createDate' => now()]);//4
        Sub_group::create(['description' => 'Piscinero / Jardineria', 'createDate' => now()]);//5
        Sub_group::create(['description' => 'Veterinario', 'createDate' => now()]);//6
        Sub_group::create(['description' => 'Pintores', 'createDate' => now()]);//7
        Sub_group::create(['description' => 'Cocinas / Heladeras / Lavadoras / Hornos', 'createDate' => now()]);//8
        Sub_group::create(['description' => 'Zapatero', 'createDate' => now()]);//9
        Sub_group::create(['description' => 'Lava autos', 'createDate' => now()]);//10
        Sub_group::create(['description' => 'Cambios de aceites / Recojo de vehículos', 'createDate' => now()]);//11
        Sub_group::create(['description' => 'Baterías', 'createDate' => now()]);//12
        Sub_group::create(['description' => 'Servicio de computación', 'createDate' => now()]);//13
        Sub_group::create(['description' => 'Copia de llaves', 'createDate' => now()]);//14
        Sub_group::create(['description' => 'Servicio de grúa', 'createDate' => now()]);//15
        Sub_group::create(['description' => 'Catering / Churrasquero', 'createDate' => now()]);//16
        Sub_group::create(['description' => 'Garzones', 'createDate' => now()]);//17
        Sub_group::create(['description' => 'Alquiler sillas, mesas, vajillas', 'createDate' => now()]);//18
        Sub_group::create(['description' => 'Barra de bebidas / Licorería', 'createDate' => now()]);//19
        Sub_group::create(['description' => 'Grupo musical, mariachi, bandas', 'createDate' => now()]);//20*/

        #grupo de habilidades
        Skills_group::create(['groupId'=>1,'skillsId'=>2]);
        Skills_group::create(['groupId'=>1,'skillsId'=>1]);
        Skills_group::create(['groupId'=>1,'skillsId'=>6]);
        Skills_group::create(['groupId'=>1,'skillsId'=>7]);
        Skills_group::create(['groupId'=>1,'skillsId'=>12]);
        Skills_group::create(['groupId'=>1,'skillsId'=>20]);
        Skills_group::create(['groupId'=>1,'skillsId'=>8]);
        Skills_group::create(['groupId'=>1,'skillsId'=>11]);
        Skills_group::create(['groupId'=>1,'skillsId'=>13]);
        Skills_group::create(['groupId'=>1,'skillsId'=>17]);
        Skills_group::create(['groupId'=>1,'skillsId'=>9]);

        Skills_group::create(['groupId'=>2,'skillsId'=>14]);
        Skills_group::create(['groupId'=>2,'skillsId'=>15]);
        Skills_group::create(['groupId'=>2,'skillsId'=>16]);
        Skills_group::create(['groupId'=>2,'skillsId'=>18]);

        Skills_group::create(['groupId'=>3,'skillsId'=>21]);
        Skills_group::create(['groupId'=>3,'skillsId'=>27]);
        Skills_group::create(['groupId'=>3,'skillsId'=>5]);

        Skills_group::create(['groupId'=>4,'skillsId'=>19]);
        Skills_group::create(['groupId'=>4,'skillsId'=>10]);
        Skills_group::create(['groupId'=>4,'skillsId'=>9]);
        Skills_group::create(['groupId'=>4,'skillsId'=>22]);
        Skills_group::create(['groupId'=>4,'skillsId'=>23]);
        Skills_group::create(['groupId'=>4,'skillsId'=>24]);
        Skills_group::create(['groupId'=>4,'skillsId'=>25]);
        Skills_group::create(['groupId'=>4,'skillsId'=>26]);

        #grupo y subgrupo
        Group_Subgroup::create(['groupId'=>1,'subGroupId'=>1,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>1,'subGroupId'=>3,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>1,'subGroupId'=>4,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>1,'subGroupId'=>5,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>1,'subGroupId'=>7,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>1,'subGroupId'=>8,'createDate'=>now()]);

        Group_Subgroup::create(['groupId'=>2,'subGroupId'=>13,'createDate'=>now()]);

        Group_Subgroup::create(['groupId'=>3,'subGroupId'=>10,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>3,'subGroupId'=>15,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>3,'subGroupId'=>11,'createDate'=>now()]);

        Group_Subgroup::create(['groupId'=>4,'subGroupId'=>6,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>4,'subGroupId'=>14,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>4,'subGroupId'=>16,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>4,'subGroupId'=>17,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>4,'subGroupId'=>18,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>4,'subGroupId'=>19,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>4,'subGroupId'=>20,'createDate'=>now()]);
        Group_Subgroup::create(['groupId'=>4,'subGroupId'=>9,'createDate'=>now()]);

        SubGroup_skill::create(['skillId'=>2,'subGroupId'=>1,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>1,'subGroupId'=>1,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>2,'subGroupId'=>3,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>16,'subGroupId'=>3,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>7,'subGroupId'=>5,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>1,'subGroupId'=>5,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>12,'subGroupId'=>5,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>5,'subGroupId'=>5,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>8,'subGroupId'=>7,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>11,'subGroupId'=>7,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>13,'subGroupId'=>7,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>17,'subGroupId'=>8,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>9,'subGroupId'=>8,'createDate'=>now()]);

        SubGroup_skill::create(['skillId'=>14,'subGroupId'=>13,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>15,'subGroupId'=>13,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>16,'subGroupId'=>13,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>18,'subGroupId'=>13,'createDate'=>now()]);

        SubGroup_skill::create(['skillId'=>21,'subGroupId'=>10,'createDate'=>now()]);

        SubGroup_skill::create(['skillId'=>27,'subGroupId'=>15,'createDate'=>now()]);

        SubGroup_skill::create(['skillId'=>5,'subGroupId'=>11,'createDate'=>now()]);

        SubGroup_skill::create(['skillId'=>19,'subGroupId'=>6,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>10,'subGroupId'=>14,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>9,'subGroupId'=>16,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>22,'subGroupId'=>17,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>23,'subGroupId'=>18,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>24,'subGroupId'=>18,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>25,'subGroupId'=>19,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>26,'subGroupId'=>20,'createDate'=>now()]);
        SubGroup_skill::create(['skillId'=>28,'subGroupId'=>9,'createDate'=>now()]);


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

        $this->call(RoleSeeder::class);

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('123'),
            'ci' => '123',
            'type_user' => 3,
            'token' => "1"
        ]);

        // Asignar el rol al usuario
        $user->assignRole('Administrativo');

        $user->givePermissionTo('view-publicity', 'manage-clients', 'manage-technician', 'access-dashboard','manage-users-roles','manage-subcription','view-promotion');
    }
}
