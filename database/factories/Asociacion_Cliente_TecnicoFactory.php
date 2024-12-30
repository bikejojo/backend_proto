<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Cliente_Externo;
use App\Models\Tecnico;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class Asociacion_Cliente_TecnicoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Asociacion_Cliente_Tecnico::class;

    public function definition(): array
    {
        return [
            'technicalId' => 1,
            'clientId' => Cliente_Externo::inRandomOrder()->first()->id,
            'dateTimeCreated' => Carbon::now(),
        ];
    }
}
