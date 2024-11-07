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
            'stateId' => 3,
            'clientId' => 10,
            'technicianId' => 40,
            'requestDescription' => $this->faker->sentence, // Utiliza $this->faker para acceder al generador de Faker
            'status' => 1,
            'registrationDateTime' => now()->addDays(rand(0, 16)),
        ];
    }
}
