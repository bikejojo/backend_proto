<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cliente_Externo;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente_Externo>
 */
class Cliente_ExternoFactory extends Factory
{
    protected $model = Cliente_Externo::class;
    public function definition()
    {
        return [
            'fullName' => $this->faker->name,
            'phoneNumber' => $this->faker->phoneNumber
        ];
    }
}
