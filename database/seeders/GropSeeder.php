<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;
use Illuminate\Support\Facades\Storage;

class GropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Hogar y Mantenimiento',
                'photo' => 'hogarymantenimiento.png'
            ],
            [
                'name' => 'Tecnología',
                'photo' => 'tecnologia.png'
            ],
            [
                'name' => 'Automotriz',
                'photo' => 'automotriz.png'
            ],
            [
                'name' => 'Servicios Especializados',
                'photo' => 'serviciosEspecializados.png'
            ],

        ];

        foreach ($groups as $group) {
            $imageSourcePath = database_path("seeders/images/group/{$group['photo']}");
            if(file_exists($imageSourcePath)){
                $imageDestinationPath = "images/group/{$group['photo']}";
                Storage::disk('public')->put($imageDestinationPath,file_get_contents($imageSourcePath));
                Group::create([
                    'name' => $group['name'],
                    'photo' => config('app.url') . "/storage/{$imageDestinationPath}"
                ]);
            }else{
                Group::create([
                    'name' => $group['name'],
                    'photo' => null
                ]);
            }
        }
    }
}
