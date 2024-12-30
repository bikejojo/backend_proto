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
            'titleRequests' => fake()->sentence(),
            'requestDescription' => fake()->paragraph(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'serviceLocation' => fake()->address(),
            'reference_phone' => fake()->phoneNumber(),
            'status' => 1,  // Estado activo
            'registrationDateTime' => now(),
        ];
    }
}
