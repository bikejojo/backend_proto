<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Suscripcion;
use App\Models\Tecnico;
use App\Models\Technician_subcripcion;
use App\Models\Promoocion_suscripcion;
use App\Models\Promocion;

class SubcriptionQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function getSuscripcion($root,array $args){
        $suscripcionData=$args['requestSuscription'];
        
    }
}
