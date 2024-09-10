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

        Ciudad::create(['descripcion'=>'SC']);
        Ciudad::create(['descripcion'=>'CH']);
        Ciudad::create(['descripcion'=>'CB']);
        Ciudad::create(['descripcion'=>'TJ']);
        Ciudad::create(['descripcion'=>'BN']);
        Ciudad::create(['descripcion'=>'PD']);
        Ciudad::create(['descripcion'=>'LP']);
    }
}
