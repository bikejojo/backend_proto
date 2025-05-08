<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sub_group;
use Illuminate\Support\Facades\Storage;

class SubGropSeeder extends Seeder
{
    public function run(): void
    {
        $subGroups = [
            ['description' => 'Aires Acondicionados', 'photo' => 'aireacondicionado.png'],
            ['description' => 'Calefonos', 'photo' => 'calefon.png'],
            ['description' => 'Electricistas', 'photo' => 'electricista.png'],
            ['description' => 'Plomero', 'photo' => 'plomero.png'],
            ['description' => 'Piscinero / Jardíneria', 'photo' => 'PiscineroJardineria.png'],
            ['description' => 'Veterinario', 'photo' => 'veterinario.png'],
            ['description' => 'Pintores', 'photo' => 'pintores.png'],
            ['description' => 'Cocinas / Heladeras / Lavadoras / Hornos', 'photo' => 'CocinasHeladerasLavadorasHornos.png'],
            ['description' => 'Zapatero', 'photo' => 'zapatero.png'],
            ['description' => 'Lava autos', 'photo' => 'lavaautos.png'],
            ['description' => 'Cambios de aceites / Recojo de vehículos', 'photo' => 'CambiosaceitesRecojo.png'],
            ['description' => 'Baterías', 'photo' => 'Baterias.png'],
            ['description' => 'Servicio de computación', 'photo' => 'computación.png'],
            ['description' => 'Copia de llaves', 'photo' => 'Copiadellaves.png'],
            ['description' => 'Servicio de grúa', 'photo' => 'Serviciodegrua.png'],
            ['description' => 'Catering / Churrasquero', 'photo' => 'CateringChurrasquero.png'],
            ['description' => 'Garzones', 'photo' => 'Garzones.png'],
            ['description' => 'Alquiler sillas, mesas, vajillas', 'photo' => 'alquilersillas.png'],
            ['description' => 'Barra de bebidas / Licorería', 'photo' => 'BarradebebidasLicorería.png'],
            ['description' => 'Grupo musical, mariachi, bandas', 'photo' => 'Grupomusicalmariachi.png'],
        ];

        foreach ($subGroups as $subGroup) {
            $imageSourcePath = database_path("seeders/images/subgroup/{$subGroup['photo']}");

            if (file_exists($imageSourcePath)) {
                $imageDestinationPath = "images/subgroup/{$subGroup['photo']}";
                Storage::disk('public')->put($imageDestinationPath, file_get_contents($imageSourcePath));

                Sub_group::create([
                    'description' => $subGroup['description'],
                    'createDate' => now(),
                    'photo' => config('app.url') . "/storage/{$imageDestinationPath}"
                ]);
            } else {
                Sub_group::create([
                    'description' => $subGroup['description'],
                    'createDate' => now(),
                    'photo' => config('app.url') . "/storage/images/subgroup/default.png"
                ]);
            }
        }
    }
}
