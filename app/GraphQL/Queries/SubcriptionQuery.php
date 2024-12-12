<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Suscripcion;
use App\Models\Tecnico;
use App\Models\Technician_subcripcion;
use App\Models\Promoocion_suscripcion;
use App\Models\Promocion;
use Carbon\Carbon;

class SubcriptionQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function validationDatePromotion($root,array $args){
        $promotionData = $args['requestPromotion'];
        $now=Carbon::now();
        $promotion = Promocion::where('codePromotion',$promotionData['codePromotion'])->first();
        if(!$promotion){
            return [
                'message' => 'La promocion ingresada no existe'];
        }
        if($promotion->status === false){
            return [
                'message' => 'La promocion ingresada ya no tiene validez'];
        }
        if($promotion->finishDate <= $now ){
            return[
                'message' => 'La promocion ingresada esta vencida.'
            ];
        }
        return [
            'message' => 'La promocion sigue vigente',
            'promocion' => $promotion
        ];
    }

    public function expirationPromotion($root,array $args){
        $now=Carbon::now();
        $promotion = Promocion::all();
        $validPromotions = $promotion->filter(function ($promotion) use($now){
            return $promotion->finishDate <= $now;
        });

        if ($validPromotions->isEmpty()) {
            return [
                'message' => 'No hay promociones vigentes',
                'promocion' => []
            ];
        }

        return [
            'message' => 'La promocion sigue vigente',
            'promocion' => $validPromotions
        ];
    }

    public function falsePromotion($root,array $args){
        $now=Carbon::now();
        $promotion = Promocion::all();
        $validPromotions = $promotion->filter(function ($promotion) use($now){
            return $promotion->status === false;
        });

        if ($validPromotions->isEmpty()) {
            return [
                'message' => 'No hay promociones vigentes',
                'promocion' => []
            ];
        }

        return [
            'message' => 'La promocion sigue vigente',
            'promocion' => $validPromotions
        ];

    }
    public function promotionValid($root,array $args){
        $now=Carbon::now();
        $promotion = Promocion::all();
        $validPromotions = $promotion->filter(function ($promotion) use($now){
            return $promotion->status === true && $promotion->finishDate >= $now;
        });

        if ($validPromotions->isEmpty()) {
            return [
                'message' => 'No hay promociones vigentes',
                'promocion' => []
            ];
        }

        return [
            'message' => 'La promocion sigue vigente',
            'promocion' => $validPromotions
        ];
    }
}
