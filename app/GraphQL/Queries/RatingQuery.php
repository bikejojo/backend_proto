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

        $ratingsCount = Calificacion::where('technicialId', $technicianId)->count();
        $ratingsCount_ = $ratingsCount === 0 ? 1 : $ratingsCount + 1;
        //dd($ratingsCount);
        $ratingsSum = Calificacion::where('technicialId', $technicianId)->sum('rating');
        //dd($ratingsSum);
        $ratingsSum_ = $ratingsCount === 0 ? $technician->average_rating : $technician->average_rating + $ratingsSum ;
        $averageRating = $ratingsCount_ > 0 ? $ratingsSum_ / $ratingsCount_ : 0;
        //dd($averageRating);
        // Redondear al múltiplo más cercano de 0.5
        $roundedRating = round($averageRating * 4) / 4;
        $formattedRating = number_format($roundedRating, 2);
        $technician->average_rating = $roundedRating;
        $technician->save();
        
        return [
            'message' => 'Calificación promedio del técnico.',
            'average_rating' => $formattedRating, // Valor redondeado
            'ratings_count' => $ratingsCount,
        ];
    }

}
