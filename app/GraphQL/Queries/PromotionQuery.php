<?php

namespace App\GraphQL\Queries;

use App\Models\Promocion;

class PromotionQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function promotionAll($root, array $args){
        $promotions = Promocion::all();
        return [
            'message'=>'Todas las promociones',
            'promotion'=>$promotions,
        ];
    }
}
