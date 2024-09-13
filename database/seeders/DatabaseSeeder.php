<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ciudad;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            'password' => '123',
            'ci' => '123',
            'tipo_usuario' => 1,
            'token' => "1"
        ]);

        Ciudad::create(['descripcion'=>'Santa Cruz']);
        Ciudad::create(['descripcion'=>'Cochabamba']);
        Ciudad::create(['descripcion'=>'Chuquisaca']);
        Ciudad::create(['descripcion'=>'Tarija']);
        Ciudad::create(['descripcion'=>'Beni']);
        Ciudad::create(['descripcion'=>'Pando']);
        Ciudad::create(['descripcion'=>'La Paz']);
        Ciudad::create(['descripcion'=>'El Alto']);
        Ciudad::create(['descripcion'=>'Oruro']);
        Ciudad::create(['descripcion'=>'Potosi']);

    }
}
