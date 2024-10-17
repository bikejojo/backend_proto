<?php declare(strict_types=1);

namespace App\GraphQL\Queries;
use App\Models\Cliente_Externo;
use App\Models\Cliente_Interno;

class ClientQuery{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function searchExternalByName($root, array $args)
    {
        $clientName = $args['name'];

        $clientExterno = Cliente_Externo::where('firstName', 'ILIKE', "%{$clientName}%")
            ->orWhere('lastName', 'like', "%{$clientName}%")
            ->get();


        $filteredClientExterno = $clientExterno->filter(function($client) {
            return !is_null($client->id);  // Asegurarse de que el id no es null
        });
        //dd($clientInterno);
        if ($clientExterno->isEmpty()) {
            return [
                'message' => 'No se encontraron resultados',
                'client_i' => null
            ];
        }
        return [
            'message' => 'Resultados encontrados',
            'client_e' => $clientExterno
        ];
    }

    public function searchInternalByName($root, array $args){
        $clientName = $args['name'];

        // Buscar en ambas tablas (ClienteInterno y ClienteExterno)
        $clientInterno = Cliente_Interno::where('firstName', 'ILIKE', "%{$clientName}%")
            ->orWhere('lastName', 'like', "%{$clientName}%")
            ->get();
            //dd($clientInterno);
        $filteredClientInterno = $clientInterno->filter(function($client) {
            return !is_null($client->id);  // Asegurarse de que el id no es null
        });

        if ($clientInterno->isEmpty()) {
            return [
                'message' => 'No se encontraron resultados',
                'client_i' => null
            ];
        }
        return [
            'message' => 'Resultados encontrados',
            'client_i' => $clientInterno
        ];
    }
}
