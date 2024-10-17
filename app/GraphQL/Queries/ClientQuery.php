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
        $client = $args['namet'];
        dd($client);
        $client_internal = Cliente_Interno::where('firstName','like',"%{$client}%")
        ->orwhere('lastname','like',"%{$client}%")
        ->get();
        $client_external = Cliente_Externo::where('firstName','like',"%{$client}%")
        ->orwhere('lastname','like',"%{$client}%")
        ->get();
        $result = $client_internal->merge($client_external);
        if($result){
            return [
                'message' => 'No se encontro ninguna semejanza'
            ];
        }
        return [
            'message' => 'Busqueda Completada',
            'client' => [$result]
        ];
    }
}
