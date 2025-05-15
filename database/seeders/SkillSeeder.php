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
            ['name' => 'Mecánico automotriz', 'photo' => 'mecanicoAutomotriz.png', 'icons' => 'car-wrench'],
            ['name' => 'Soldador', 'photo' => 'soldador.png', 'icons' => 'briefcase'],
            ['name' => 'Jardinería', 'photo' => 'Piscinero-Jardineria.png', 'icons' => 'mower-bag'],
            ['name' => 'Albanilería', 'photo' => 'albanil.png', 'icons' => 'hammer'],
            ['name' => 'Cocinero', 'photo' => 'cocinero.png', 'icons' => 'chef-hat'],
            ['name' => 'Cerrajero', 'photo' => 'Copiadellaves.png' , 'icons' => 'key-chain-variant'],
            ['name' => 'Pintor de obras', 'photo' => 'pintorObras.png', 'icons' => 'format-paint'],
            ['name' => 'Fumigador', 'photo' => 'fumigacion.png', 'icons' => 'sprinkler-variant'],
            ['name' => 'Vidriero', 'photo' => 'vidriero.png', 'icons' => 'google-glass'],
            ['name' => 'Técnico en computación', 'photo' => 'computacion.png', 'icons' => 'laptop'],
            ['name' => 'Técnico en redes electricas', 'photo' => 'tecnicoRedesElectrica.png', 'icons' => 'flash-outline'],
            ['name' => 'Técnico en electrodómesticos', 'photo' => 'electrodomesticos.png', 'icons' => 'fridge-outline'],
            ['name' => 'Limpieza General', 'photo' => 'limpiezas.png', 'icons' => 'format-clear'],
            ['name' => 'Técnico en telefonía movil', 'photo' => 'telefono.png', 'icons' => 'cellphone-cog'],
            ['name' => 'Veterinario(a)', 'photo' => 'veterinario.png', 'icons' => 'doctor'],
            ['name' => 'Piscinero', 'photo' => 'PiscineroJardineria.png', 'icons' => 'swim'],
            ['name' => 'Limpieza general en vehículo', 'photo' => 'lavaautos.png','icons' => 'spray-bottle'],
            ['name' => 'Mesero / Camarero / Mozo', 'photo' => 'Garzones.png', 'icons' => 'silverware-fork-knife'],
            ['name' => 'Organizador de eventos', 'photo' => 'CateringChurrasquero.png', 'icons' => 'party-popper'],
            ['name' => 'Arrendador de mobiliario', 'photo' => 'alquilersillas.png', 'icons' => 'sofa-single'],
            ['name' => 'Bartender / Barman', 'photo' => 'BarradebebidasLicoreria.png', 'icons' => 'glass-wine'],
            ['name' => 'Vocalista', 'photo' => 'Grupomusicalmariachibandas.png', 'icons' => 'microphone-variant'],
            ['name' => 'Operador de grúa', 'photo' => 'Serviciodegrua.png', 'icons' => 'crane'],
            ['name' => 'Zapatero / Reparador de calzado', 'photo' => 'zapatero.png', 'icons' => 'shoe-formal'],
        ];


        foreach ($skill as $habilidad) {
            $imageSourcePath = database_path("seeders/images/subgroup/{$habilidad['photo']}");

            if (file_exists($imageSourcePath)) {
                $imageDestinationPath = "images/subgroup/{$habilidad['photo']}";
                Storage::disk('public')->put($imageDestinationPath, file_get_contents($imageSourcePath));

                Habilidad::create([
                    'name' => $habilidad['name'],
                    'status' => 1,
                    'photo' => config('app.url') . "/storage/{$imageDestinationPath}",
                    'icons' => $habilidad['icons'] ?? 'hammer-wrench'
                ]);
            } else {
                Habilidad::create([
                    'name' => $habilidad['name'],
                    'status' => 1,
                    'photo' => config('app.url') . "/storage/images/subgroup/default.png",
                    'icons' => $habilidad['icons'] ?? 'hammer-wrench'
                ]);
            }
        }
    }
}
