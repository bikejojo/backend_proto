<?php

namespace Database\Factories;

use App\Models\Servicio;
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
        return [
            'stateId' => $this->faker->numberBetween(4, 5),
            'requestsId' => $this->faker->numberBetween(2200,2439),
            'technicalId'=> $this->faker->numberBetween(60), //'technicalId'=> rand(150,62)
            'clientId' =>  $this->faker->numberBetween(2, 13),
            'activityId' => $this->faker->numberBetween(1,4),
            'typeClient' => 1,
            'titleService' => $this->faker->sentence,
            'serviceDescription' => $this->faker->sentence,
            'createdDateTime' => now(),
            'finishDateTime' =>now()->addDays(4,7),
            'updatedDateTime' =>now()->addDays(2,3),
            'status' => 1
            //
        ];
    }
}
