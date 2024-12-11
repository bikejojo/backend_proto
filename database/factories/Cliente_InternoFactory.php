<?php

namespace Database\Factories;

use App\Models\Cliente_Interno;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente_Interno>
 */
class Cliente_InternoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Cliente_Interno::class;


    public function definition(): array{
        return [
            'firstName' => $this->faker->firstName(),
            'lastName' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'phoneNumber' => $this->faker->phoneNumber(),
            'cityId' => 1,
            'userId' => User::factory(),
            'loginMethod'=> $this->faker->randomElement(['email', 'google', 'facebook']),
        ];
    }
}
