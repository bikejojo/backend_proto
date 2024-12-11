<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Promocion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PromotionMutations
{
    public function creaPromotion($root,array $args){
        $promotionData = $args['requestPromotion'];
        DB::beginTransaction();
        try{
            $promotion = Promocion::create([
                'codePromotion' =>$promotionData['codePromotion'],
                'namePromotion' =>$promotionData['namePromotion'],
                'description'   =>$promotionData['description'],
                'type'          =>$promotionData['type'],
                'discount_value'=>$promotionData['discount_value'],
                'createDate'    =>$promotionData['createDate'],
                'finishDate'    =>$promotionData['finishDate'],
            ]);

            // Calcular la duración en días y asegurarse de que sea un entero
            $promotion->status=1;
            $startDate = Carbon::parse($promotionData['createDate']);
            $endDate = Carbon::parse($promotionData['finishDate']);
            $promotion->duration = round($startDate->diffInDays($endDate)); 
            $promotion->save();

        DB::commit();
            return[
                'message'=>'creacion de promocion exitosa',
                'promotion'=>$promotion
            ];
        }catch (\Exception $e){
            DB::rollback();
            return[
                'message'=>'El error es: '. $e->getMessage()
                ];
        }
    }

    public function editPromotion($root,array $args){
        $promotionData = $args['requestPromotion'];
        $promotionId = $promotionData['promotion_id'];
        $promotion = Promocion::find($promotionId);
        if(!$promotion){
            return[
                'message'=>'No existe la promocion que mandaste.'
            ];
        }

        DB::beginTransaction();
        try{
            $promotion->update([
                'namePromotion' =>trim($promotionData['namePromotion']),
                'description'   =>isset($promotionData['description']) ? trim($promotionData['description']) : null,
                'type'          =>$promotionData['type'],
                'discount_value'=>$promotionData['discount_value'],
                'createDate'    =>$promotionData['createDate'],
                'finishDate'    =>$promotionData['finishDate'],
            ]);
            $promotion->status=1;
            $startDate = Carbon::parse($promotionData['createDate']);
            $endDate = Carbon::parse($promotionData['finishDate']);
            $promotion->duration = round($startDate->diffInDays($endDate)); 
            $promotion->save();

            DB::commit();
            return[
                'message'=>'Edicion de promocion exitosa',
                'promotion'=>$promotion
            ];

        }catch(\Exception $e ){
            DB::rollback();
            return[
                'message'=>'El error es: '. $e->getMessage()
            ];
        }
    }

    public function lowPromotion($root ,array $args){
        $promotionData = $args['requestPromotion']['promotion_id'];
        if(!$promotionData){
            return[
                'message' => 'No existe la promocion que mandaste.'
            ];
        }
        $promotion=Promocion::find($promotionData);
        $promotion->status=0;
        $promotion->save();

        return [
            'message' => 'La promocion se ha bajado su estado.',
            'promotion' => $promotion
        ];
    }
}
