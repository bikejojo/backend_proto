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
            'stateId' => 2,
            'clientId' => rand(1,3),
            'activityId'=>rand(1,2),
            'titleRequests' => $this->faker->sentence,
            'requestDescription' => $this->faker->sentence, // Utiliza $this->faker para acceder al generador de Faker
            'technicianId' => 1,
            'latitude'=> -89.12,
            'longitude'=> -98.5678,
            'reference_phone'=>rand(72066928,79836202),
            'status' => 1,
            'registrationDateTime' => now()->addDays(rand(0,2))->addMinutes(rand(2, 40)),
        ];
    }
}
