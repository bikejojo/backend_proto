<?php

namespace Database\Factories;

use App\Models\Cliente_Externo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente_Externo>
 */
class Cliente_ExternoFactory extends Factory
{
    protected $model = Cliente_Externo::class;

    public function definition()
    {
        $valorActive = 1;
        return [
            'fullName' => $this->faker->name,
            'phoneNumber' => $this->faker->phoneNumber,
            'status' => $valorActive,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
