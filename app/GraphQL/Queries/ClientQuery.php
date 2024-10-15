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

    public function searchByName($root,array $args){
        $client = $args['name'];
        //dd($client);
        $client_internal = Cliente_Interno::where('firstName','like',"%{$client}%")
        ->orwhere('lastname','like',"%{$client}%")
        ->get();
        dd($client_internal);
        $client_external = Cliente_Externo::where('firstName','like',"%{$client}%")
        ->orwhere('lastname','like',"%{$client}%")
        ->get();
        
        $result = $client_internal->merge($client_external);
        //$result->all();
        //dd($result);
        if($result->isEmpty()){
            return [
                'message' => 'No se encontro ninguna semejanza.'
            ];
        }
        return [
            'message' => "Busqueda Completada",
            'client_i' => $client_internal,
            'cliente_e' => $client_external
        ];
    }
}
