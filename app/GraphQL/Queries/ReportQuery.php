<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Tecnico;
use App\Models\Calificacion;
use App\Models\Suscripcion;
use App\Models\Technician_subcripcion;

class ReportQuery{
    public function reportSuscription($root,array $args){
        $startDate = $args['input']['startDate'] ?? null;
        $finishDate = $args['input']['finishDate'] ?? null;
        $type = $args['input']['type'] ?? 'FREE';
        $satus = $args['input']['status'] ?? 1;
    }

}
