<?php

namespace Database\Factories;

use App\Models\Solicitud;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SolicitudFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model= Solicitud::class;
    public function definition()
    {
        return [
            'stateId' => rand(1,3),
            'clientId' => rand(2,11),
            'technicianId' => 60,
            'requestDescription' => $this->faker->sentence, // Utiliza $this->faker para acceder al generador de Faker
            'status' => 1,
            'registrationDateTime' => now()->addDays(rand(0,2))->addMinutes(rand(2, 40)),
        ];
    }
}
