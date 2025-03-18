<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PublicitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las categorías existentes
        $categories = DB::table('category_publicity')->get();

        // Verificar que existan categorías antes de insertar publicidades
        if ($categories->isEmpty()) {
            $this->command->info('No hay categorías en category_publicity. Ejecute el seeder de categorías primero.');
            return;
        }

        // Insertar publicidades para cada categoría
        foreach ($categories as $category) {
            DB::table('publicity')->insert([
                'descriptionPublicity' => 'Publicidad de  ' . $category->description,
                'logo' => 'logo_' . Str::random(6) . '.png',
                'commercialName' => 'Commercial de' . $category->description,
                'link' => 'https://' . Str::slug($category->description) . '.com',
                'createdDate' => Carbon::now(),
                'startDate' => Carbon::now(),
                'finishDate' => Carbon::now()->addMinute(),
                'status' => 1,  // 1 activo, 0 dado de baja
                'categoryId' => $category->id,  // Relación con la categoría
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Publicidades creadas exitosamente.'); //
    }
}
