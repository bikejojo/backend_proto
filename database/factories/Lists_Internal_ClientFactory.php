<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Lists_Internal_Client;
use App\Models\Tecnico; // Asegúrate de que este modelo existe
use App\Models\Cliente; // Asegúrate de que este modelo existe
use App\Models\Request; // Asegúrate de que este modelo existe

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lists_Internal_Client>
 */
class Lists_Internal_ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model= Lists_Internal_Client::class;
    public function definition()
    {
        return [
            'technicianId' => 1, // Asocia un técnico creado por factory
            'clientId' => rand(1,3), // Asocia un cliente creado por factory
            'typeClient' => 1,
            'requestsId' => 1, // Asocia una solicitud creada por factory
        ];
    }

    /**
     * Define un estado para clientes internos.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function internalClient()
    {
        return $this->state(function (array $attributes) {
            return [
                'typeClient' => self::client_internal,
            ];
        });
    }

    /**
     * Define un estado para clientes externos.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function externalClient()
    {
        return $this->state(function (array $attributes) {
            return [
                'typeClient' => self::client_external,
            ];
        });
    }
}