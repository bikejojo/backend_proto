<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Habilidad;
use Illuminate\Support\Facades\Storage;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skill = [
            ['name' => 'Plomeria', 'photo' => 'plomero.png', 'icons' => 'account-wrench-outline'],
            ['name' => 'Electricista', 'photo' => 'electricista.png', 'icons' => 'fuse-alert'],
            ['name' => 'Carpinteria', 'photo' => 'carpinteria.png', 'icons' => 'hammer'],
            ['name' => 'Pintor', 'photo' => 'pintores.png', 'icons' => 'format-paint'],
            ['name' => 'Mecanico automotriz', 'photo' => 'mecanicoAutomotriz.png', 'icons' => 'car-cog'],
            ['name' => 'Soldador', 'photo' => 'soldador.png', 'icons' => 'soldering-iron'],
            ['name' => 'Jardineria', 'photo' => 'PiscineroJardineria.png', 'icons' => 'mower-bag'],
            ['name' => 'Albanileria', 'photo' => 'pintores.png', 'icons' => 'hammer-screwdriver'],
            ['name' => 'Cocinero', 'photo' => 'CocinasHeladerasLavadoras.png', 'icons' => 'chef-hat'],
            ['name' => 'Cerrajero', 'photo' => 'Copiadellaves.png' , 'icons' => 'key-chain-variant'],
            ['name' => 'Pintor de obras', 'photo' => 'pintores.png', 'icons' => 'sprinkler-variant'],
            ['name' => 'Fumigador', 'photo' => 'PiscineroJardineria.png', 'icons' => 'google-glass'],
            ['name' => 'Vidriero', 'photo' => 'pintores.png', 'icons' => 'cable-data'],
            ['name' => 'Tecnico en computacion', 'photo' => 'computacion.png', 'icons' => 'lightning-bolt-circle'],
            ['name' => 'Tecnico en redes electricas', 'photo' => 'computacion.png', 'icons' => 'fridge-alert-outline'],
            ['name' => 'Tecnico en electrodomesticos', 'photo' => 'computacion.png', 'icons' => 'format-clear'],
            ['name' => 'Limpieza General', 'photo' => 'CocinasHeladerasLavadoras.png', 'icons' => 'cellphone-cog'],
            ['name' => 'Tecnico en telefonia movil', 'photo' => 'computacion.png', 'icons' => 'doctor'],
            ['name' => 'Veterinario(a)', 'photo' => 'veterinario.png', 'icons' => 'pool'],
            ['name' => 'Piscenero', 'photo' => 'PiscineroJardineria.png', 'icons' => 'spray-bottle'],
            ['name' => 'Limpieza general en vehiculo', 'photo' => 'lavaautos.png'],
            ['name' => 'Mesero / Camarero / Mozo', 'photo' => 'Garzones.png', 'icons' => 'silverware-fork-knife'],
            ['name' => 'Organizador de eventos', 'photo' => 'CateringChurrasquero.png', 'icons' => 'party-popper'],
            ['name' => 'Arrendador de mobiliario', 'photo' => 'alquilersillas.png', 'icons' => 'sofa-single'],
            ['name' => 'Bartender / Barman', 'photo' => 'BarradebebidasLicoreria.png', 'icons' => 'glass-cocktail'],
            ['name' => 'Vocalista', 'photo' => 'Grupomusicalmariachibandas.png', 'icons' => 'microphone-message'],
            ['name' => 'Operador de grua', 'photo' => 'Serviciogrua.png', 'icons' => 'microphone-message'],
            ['name' => 'Zapatero / Reparador de calzado', 'photo' => 'zapatero.png', 'icons' => 'microphone-message'],
        ];


        foreach ($skill as $habilidad) {
            $imageSourcePath = database_path("seeders/images/subgroup/{$habilidad['photo']}");

            if (file_exists($imageSourcePath)) {
                $imageDestinationPath = "images/subgroup/{$habilidad['photo']}";
                Storage::disk('public')->put($imageDestinationPath, file_get_contents($imageSourcePath));

                Habilidad::create([
                    'name' => $habilidad['name'],
                    'status' => 1,
                    'photo' => env('APP_URL') . "/storage/{$imageDestinationPath}",
                    'icons' => $habilidad['icons'] ?? 'default-icon'
                ]);
            } else {
                Habilidad::create([
                    'name' => $habilidad['name'],
                    'status' => 1,
                    'photo' => env('APP_URL') . "/storage/images/habilidades/default.png",
                    'icons' => $habilidad['icons'] ?? 'default-icon'
                ]);
            }
        }
    }
}
