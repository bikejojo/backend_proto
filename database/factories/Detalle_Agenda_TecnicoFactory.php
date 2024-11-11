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
            'clientId' => rand(3,10),
            'agendaTechnicalId' => 43,
            'serviceId' => $this->faker->numberBetween(501, 900),
            'typeClient' => 1,
            'createDate' => now(),
            'serviceDate' => '2024-11-10 12:12:11',
        ];
    }
}
