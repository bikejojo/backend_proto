<?php

namespace App\GraphQL\Queries;

use App\Models\Ciudad;
use App\Models\Cliente_Interno;

class CiudadQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function getAllCities_two($root , array $args){
       $cities = Ciudad::whereHas('technicians')  // usar internalClients (clientes internos relacionados)
        ->select('id','name')
        ->get();

        return $cities;
    }
}
