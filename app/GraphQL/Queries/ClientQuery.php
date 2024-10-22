<?php declare(strict_types=1);

namespace App\GraphQL\Queries;
use App\Models\Cliente_Externo;
use App\Models\Cliente_Interno;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Tecnico;
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

    public function technicianByclient($root,array $args){
        $technicianId = $args['id_technician'];
        $listado=Asociacion_Cliente_Tecnico::where('technicalId',$technicianId)->get();
        if($listado->isEmpty()){
            return[
                'message' => 'El tecnico no tiene una lista de clientes propios',
                'client_e' => null
            ];
        }
        $client=[];
        foreach($listado as $cliente){
            $listadoss=Cliente_Externo::find($cliente->clientId);
            $client[]=$listadoss;
        }
        return[
            'message' => 'Clientes propios de los tecnicos',
            'client_e' => $client
        ];
    }
}
