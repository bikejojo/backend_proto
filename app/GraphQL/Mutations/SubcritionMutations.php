<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use App\Models\Suscripcion;
use App\Models\Pago;
use App\Models\Promocion_suscripcion;
use App\Models\Promocion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubcritionMutations
{
    public function creaSubcription($root,array $args){
        $subcriptionData = $args['requestSubcription'];
        //dd($subcriptionData);
        $technicianId=$subcriptionData['technicianId'];
        if(Tecnico::find($technicianId == null )){
            return[
                'message'=>'No existe la ID del tecnico en la base de datos.'
            ];
        }
        DB::beginTransaction();
        try{
            $now=Carbon::now();
            $nowAdd=$now->copy()->addDays(3);
            $subcription = Suscripcion::create([
                'account'=>$subcriptionData['account'],
                'description'=>$subcriptionData['description'],
                'createDate'=>$now,
                'finishDate'=>$nowAdd,
                'technicianId'=>$technicianId,
                'status'=>1,
                
            ]);
            $payment = Pago::create([
                'bank' => $subcriptionData['bank'],
                'account' => $subcriptionData['account'],
                'social_reason' => $subcriptionData['social_reason'],
                'amount' => $subcriptionData['amount'],
                'method_payment' => $subcriptionData['method_payment'],
                'date_payment' => $nowAdd,
                'photo_qr' => $subcriptionData['photo_qr'],
                'subscriptionId' => $subcription->id,
                'status'=>1,
            ]);
            $payment->amount_pay = $payment->amount - $payment->amount_promotion;
            $payment->save();

        DB::commit();
            return[
                'message'=>'Creacion de subscripcion exitosa',
                'messageNext'=>'Se espera que ingrese alguna promocion',
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

    public function registerSubcriptPromot($root, array $args)
    {
        $now = Carbon::now();
        $subcriptionData = $args['requestSubcription']; // Cambié el nombre al esperado en el esquema
        $promotionCode = $subcriptionData['codePromotion'];
    
        // Buscar promoción válida
        $promotion = Promocion::where('codePromotion', $promotionCode)
            ->where('status', true) // Activa
            ->where('finishDate', '>=', $now) // No vencida
            ->first();
    
        if (!$promotion) {
            return [
                'message' => 'La promoción no es válida o está vencida.',
            ];
        }
        $technician = Tecnico::find($subcriptionData['technician_id']);
        if(!$technician){
            return[
                'message'=>'No existe ID del tecnico.'
            ];
        }
        // Buscar suscripción
        $subcription = Suscripcion::find($subcriptionData['id']);
    
        if (!$subcription) {
            return [
                'message' => 'No se encontró la suscripción.',
            ];
        }
    
        // Verificar si la promoción ya está aplicada
        $existingPromotion = Promocion_suscripcion::where('subcriptionsId', $subcription->id)
            ->where('promotionId', $promotion->id)
            ->first();
    
        if ($existingPromotion) {
            return [
                'message' => 'La promoción ya ha sido aplicada a esta suscripción.',
            ];
        }
    
        DB::beginTransaction();
        try {
            // Registrar la relación entre promoción y suscripción
            $register = Promocion_suscripcion::create([
                'subcriptionsId' => $subcription->id,
                'promotionId' => $promotion->id,
            ]);
    
            // Aplicar descuento a la suscripción
            $payment = Pago::where('subscriptionId', $subcription->id)->first();
            if (!$payment) {
                return [
                    'message'=>'No se encontró un pago relacionado con esta suscripción.'
                ];
            }
            if($promotion->type === 'Fecha'){
                $subcription->finishDate = $now->copy()->addDays($promotion->discount_value);
                $subcription->save();
                $payment->amount_promotion = $payment->amount;
                $payment->amount_pay = $payment->amount - $payment->amount_promotion;
                $payment->save();

            }
            if($promotion->type === 'Descuento'){
                $payment->amount_promotion = $payment->amount*($promotion->discount_vale / 100);
                $payment->amount_pay = $payment->amount - $payment->amount_promotion;
                $payment->save();
            }

            DB::commit();
    
            return [
                'message' => 'La promoción se ha aplicado correctamente.',
                'subcription' => $subcription,
                'promotion' =>   $promotion,
                'payment' =>     $payment,
                'technician' =>  $technician
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'message' => 'Fallo en el proceso: ' . $e->getMessage(),
            ];
        }
    }

    public function lowSubcription($root,array $args){
        $subcriptionData = $args['requestSubcription'];
        if(!Suscripcion::find($subcriptionData['id'])){
            return[
                'message'=>'No existe suscripcion!.'
            ];
        }
        $subcription=Suscripcion::find($subcriptionData['id']);
        DB::beginTransaction();
        try{
            $subcription->status=0;
            $subcription->save();
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

    public function technicalSubscription($root,array $args){
        $subcriptionData = $args['requestSubcription'];
        $technician=Tecnico::find($subcriptionData['technicianId']);
        if(!$technician){
            return[
                'message'=>'No existe tecnico.'
            ];
        }
        $suscription=Suscripcion::where('technicianId',$technician->id)->where('status',1)->first();
        if (!$suscription) {
            return [
                'message' => 'El técnico no tiene una suscripción activa.',
            ];
        }
        $now=Carbon::now();
        $finishDate = Carbon::parse($suscription->finishDate);
        if ($finishDate->isPast()) {
            return [
                'message' => 'La suscripción ha expirado.',
                'day' => 'La suscripción venció el ' . $finishDate->toDateString() . '.',
                'technician' => $technician,
                'suscripcion' => $suscription,
            ];
        }
    
        // Calcular días restantes para que finalice la suscripción
        $daysRemaining = ceil($now->diffInHours($finishDate) / 24);
    
        return [
            'message' => 'Validación de suscripción exitosa.',
            'day' => 'Faltan ' . $daysRemaining . ' días para que termine la suscripción.',
            'technician' => $technician,
            'suscripcion' => $suscription,
        ];
    }
}
