<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\models\Agenda_Tecnico;
use App\Models\Detalle_Agenda_Tecnico;
use Carbon\Carbon;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class Detalle_Agenda_TecnicoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clientId' => rand(1,4),
            'agendaTechnicalId' => 1,
            'serviceId' => $this->faker->numberBetween(1, 5),
            'typeClient' => 1,
            'createDate' => now(),
            'serviceDate' => '2024-11-30 12:12:11',
        ];
    }
}
