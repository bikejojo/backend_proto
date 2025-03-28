<?php

namespace Database\Seeders;

use App\GraphQL\Queries\PublicityQuery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Publicidad;
use App\Models\Categoria_Publicidad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PublicidadCategSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = [
            ['description'=>'tecnologia','photo'=>'tecnologia.png','entity_type'=>'publicity','code'=>'PUB001'],
            ['description'=>'servicios','photo'=>'servicio.png','entity_type'=>'publicity','code'=>'PUB002'],
            ['description'=>'productos','photo'=>'producto.png','entity_type'=>'publicity','code'=>'PUB003'],
            ['description'=>'consultoria','photo'=>'consultoria.png','entity_type'=>'publicity','code'=>'PUB004'],
        ];

        foreach ($category as $categorias){
            $imageSourcePath  = database_path("seeders/images/publicidadCategoria/{$categorias['photo']}");
            if(file_exists($imageSourcePath)){
                $imageDestinationPath = "images/categoria/publicidad/{$categorias['photo']}";
                Storage::disk('public')->put($imageDestinationPath,file_get_contents($imageSourcePath));
                Categoria_Publicidad::create([
                    'description' => $categorias['description'],
                    'entity_type' => $categorias['entity_type'],
                    'code' => $categorias['code'],
                    'photo' => env('APP_URL') . "/storage/{$imageDestinationPath}"
                ]);
            }else{
                Categoria_Publicidad::create([
                    'description' => $categorias['description'],
                    'entity_type' => $categorias['entity_type'],
                    'code' => $categorias['code'],
                    'photo' => null
                ]);
            }
        }
    }
}
