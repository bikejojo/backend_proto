<?php declare(strict_types=1);

namespace App\GraphQL\Queries;
use App\Models\Cliente_Externo;
use App\Models\Cliente_Interno;
use Illuminate\Support\Facades\DB;

class ClientQuery{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function searchExternalByName($root, array $args)
    {
        $clientName = strtolower($args['name']);
        $clientExterno = Cliente_Externo::where(DB::raw('LOWER("fullName")'), 'LIKE', "%{$clientName}%")
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
        $clientName = strtolower($args['name']);
        $clientInterno = Cliente_Interno::where(DB::raw('LOWER("firstName")'), 'LIKE', "%{$clientName}%")
            ->orWhere(DB::raw('LOWER("lastName")'), 'LIKE', "%{$clientName}%")
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
