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
            'stateId' => 1,
            'clientId' => 1,
            'activityId'=>rand(1,2),
            'reference_phone'=>rand(77066928,79836202),
            'technicianId' => 1,
            'requestDescription' => $this->faker->sentence, // Utiliza $this->faker para acceder al generador de Faker
            'latitude'=> -89.12,
            'longitude'=> -98,5678,
            'status' => 1,
            'registrationDateTime' => now()->addDays(rand(0,2))->addMinutes(rand(2, 40)),
        ];
    }
}
