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
        $clientData = $args['requestClient'];
        $tecnicoId = $clientData['technicalId'];
        if($clientData['searchParameter']!=null){
            $clientNamePhone = strtolower($clientData['searchParameter']);
            $clientExterno = Cliente_Externo::whereHas('associantions', function ($query) use ($tecnicoId) {
                $query->where('technicalId', $tecnicoId);  // Filtrar por ID del técnico
            })
            ->where(function($query) use ($clientNamePhone) {
                $query->where(DB::raw('LOWER("fullName")'), 'LIKE', "%{$clientNamePhone}%")
                    ->orWhere(DB::raw('LOWER("phoneNumber")'), 'LIKE', "%{$clientNamePhone}%");
            })
            ->get();
        }else{
            $clientExterno = Cliente_Externo::whereHas('associantions', function ($query) use ($tecnicoId) {
                $query->where('technicalId', $tecnicoId);  // Filtrar por ID del técnico
            })->get();
        }
        if ($clientExterno->isEmpty()) {
            return [
                'message' => 'No se encontraron resultados',
                'customer_internal' => null
            ];
        }
        return [
            'message' => 'Resultados encontrados',
            'customer_external' => $clientExterno
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

        $listado=Asociacion_Cliente_Tecnico::where('technicalId',$technicianId)
        ->leftjoin('external_clients','external_clients.id','=','clientId')
        ->orderBy('external_clients.id', 'desc')
        ->get();
        if($listado->isEmpty()){
            return[
                'message' => 'El tecnico no tiene una lista de clientes propios',
                'customer_external' => null
            ];
        }

        return[
            'message' => 'Clientes propios de los tecnicos',
            'customer_external' => $listado
        ];
    }
}
