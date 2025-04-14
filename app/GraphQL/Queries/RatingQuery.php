<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Tecnico;
use App\Models\Calificacion;


class RatingQuery{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function getTechnicianRating($root, array $args){

        $technicianId = $args['requestRating']['id_technician'];

        $technician = Tecnico::find($technicianId);
        if (!$technician) {
            return [
                'message' => 'Técnico no encontrado.',
                'average_rating' => null,
                'ratings_count' => 0,
            ];
        }
        $formattedRating = $technician->average_rating;
        $ratingsCount = Calificacion::where('technicialId', $technicianId)->count();
        
        return [
            'message' => 'Calificación promedio del técnico.',
            'average_rating' => $formattedRating, // Valor redondeado
            'ratings_count' => $ratingsCount,
        ];
    }

}
