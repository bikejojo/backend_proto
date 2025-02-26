<?php

namespace Database\Factories;

use App\Models\Servicio;
use App\Models\Solicitud;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Servicio>
 */
class ServicioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Servicio::class;

    public function definition(): array
    {
         // Filtrar solicitudes con stateId = 2 y clientId = 2
         $solicitud = Solicitud::where('stateId', 2)
         ->where('clientId', 2) // Asegurar que la solicitud pertenece al cliente correcto
         ->inRandomOrder()
         ->first();
         return [
            'stateId' => $this->faker->numberBetween(1, 4),
            'requestsId' => $solicitud?->id,  // Si no hay solicitudes, asigna null
            'technicalId' => 1,
            'clientId' => 2,  // Cliente correcto
            'activityId' => $this->faker->numberBetween(1, 4),
            'typeClient' => 1,
            'titleService' => $solicitud?->titleRequests ?? $this->faker->sentence,
            'serviceDescription' => $solicitud?->requestDescription ?? $this->faker->sentence,
            'createdDateTime' => now(),
            'updatedDateTime' => now()->addDays(rand(2, 3)),
            'status' => 1,
            'service_origin' => 1
        ];
    }
}
