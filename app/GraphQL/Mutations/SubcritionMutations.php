<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use App\Models\Suscripcion;
use App\Models\Pago;
use App\Models\Promocion_suscripcion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubcritionMutations
{
    public function creaSubcription($root,array $args){
        $subcriptionData = $args['requestSubcription'];
        $technicianId=$subcriptionData['technicianId'];
        if(Tecnico::find($technicianId == null )){
            return[
                'message'=>'No existe la ID del tecnico en la base de datos.'
            ];
        }
        DB::beginTransaction();
        try{
            $now=Carbon::now();
            $nowAdd=$now()->addDays(3);
            $subcription = Suscripcion::create([
                'account'=>$subcriptionData['account'],
                'description'=>$subcriptionData['description'],
                'createDate'=>$now,
                'finishDate'=>$nowAdd,
                'technicianId'=>$technicianId,
                'status'=>1,
                
            ]);

            $payment=Pago::create([
                'bank'=>$subcriptionData['bank'],
                'account'=>$subcriptionData['account'],
                'social_reason'=>$subcriptionData['social_reason'],
                'amount'=>$subcriptionData['amount'],
                'amount_promotion'=> 0,
                'method_payment'=>$subcriptionData['method_payment'],
                'date_payment'=>$nowAdd,
                'photo_qr'=>$subcriptionData['photo_qr'],
                'subcriptionId'=>$subcription->id
            ]);

            $payment->amount_pay = $payment->amount - $payment->amount_promotion;
            $payment->save();

        DB::commit();
            return[
                'message'=>'Creacion de subscripcion exitosa',
                'message'=>'Se espera que ingrese alguna promocion',
                'technician'=>Tecnico::find($technicianId),
                'subcription'=>$subcription,
                'payment'=>$payment
            ];
        } catch (\Exception $e){
            DB::rollback();
            return[
                'message'=>'Ocurrio el siguiente problema. ' .$e->getMessage()
            ];
        }
    }

    public function registerSubcriptPromot($root,array $args){
        $now=Carbon::now();
        $subcriptionData = $args['requestSubcription'];
        $promotionData = $subcriptionData['codePromotion'];
        $promotion = Promocion::where('codePromotion', $promotionData)
        ->where('status', true) // Activa
        ->where('finishDate', '>=', now()) // No vencida
        ->first();

        if (!$promotion) {
            return [
                'message' => 'La promoción no es válida o está vencida.',
            ];
        }

        $subcription=Suscripcion::find($subcriptionData['id']);

        $promotion = Promocion::where('codePromotion', $promotionCode)
        ->where('status', true) // La promoción debe estar activa
        ->where('finishDate', '>=', now()) // No debe estar vencida
        ->first();

        $existingPromotion = Promocion_suscripcion::where('subcriptionsId', $subcription->id)
        ->where('promotionId', $promotion->id)
        ->first();

        if ($existingPromotion) {
            return [
                'message' => 'La promoción ya ha sido aplicada a esta suscripción.'
            ];
        }

        DB::beginTransaction();
        try{
            $register=Promocion_suscripcion::create([
                'subcriptionsId'=>$subcription->id,
                'promotionId'=>$promotion->id,
            ]);
            $promotion = $payment->amount;
            $payment->amount_promotion = $promotion;
            $payment->amount_pay = $payment->amount - $payment->amount_promotion;
            $payment->save();
            $subcription->createDate = $now;
            $subcription->finishDate = $now->copy()->addDays($promotion->discount_value);
            $subcription->save();
            DB::commit();
            return [
                'message'=>'La promocion se ha aplicado correctamente.',
                'subcription'=>$subcription,
                'payment'=>$payment,
                'promotion'=>$promotion
            ];
        }catch(\Exception $e){
            DB::rollback();
            return [
                'message'=> 'Fallo en el ' . $e->getMessage()
            ];
        }
    }

    public function lowSubcription($root,array $args){
        $subcriptionData = $args['requestSubcription'];
        if(Suscripcion::find($subcriptionData['id'])){
            return[
                'message'=>'No existe suscripcion!.'
            ];
        }
        $subcription=Suscripcion::find($subcriptionData['id']);
        DB::beginTransaction();
        try{
            $subcription->status=0;
            DB::commit();
            return[
                'message'=>'La suscripcion se ha cancelado correctamente.',
               'subcription'=>$subcription
            ];
        }catch(\Exception $e){
            DB::rollback();
            return[
                'message'=>'Ocurrio el siguiente problema. '. $e->getMessage()
            ];
        }
    }
}
