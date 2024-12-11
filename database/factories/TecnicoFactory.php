<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tecnico;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tecnico>
 */
class TecnicoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Tecnico::class;
    public function definition(): array
    {
        return [
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'frontIdCard' => $this->faker->imageUrl(200, 200, 'id card front'),
            'backIdCard' => $this->faker->imageUrl(200, 200, 'id card back'),
            'email' => $this->faker->unique()->safeEmail,
            'phoneNumber' => $this->faker->phoneNumber,
            'password' => Hash::make('123'), // Puedes usar bcrypt('password') si lo prefieres
            'average_rating' => $this->faker->randomFloat(2, 0, 5), // Puntuación entre 0 y 5
            'userId' => User::factory(), // Relación con User
        ];
    }
}
