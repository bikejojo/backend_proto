<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use App\Models\Suscripcion;
use App\Models\Pago;
use App\Models\Promocion_suscripcion;
use App\Models\Promocion;
use App\Models\Technician_subcripcion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubcritionMutations
{
    public function creaSubcription($root, array $args){
        $subcriptionData = $args['requestSubcription'];

        DB::beginTransaction();
        try {
            // Crear una nueva suscripción
            $now = Carbon::now();
            $subcription = Suscripcion::create([
                'name' => $subcriptionData['name'],
                'description' => $subcriptionData['description'],
                'createDate' => $now,
                'duration' => $subcriptionData['duration'],
                'status' => 1, // Estado inicial de la suscripción
            ]);

            DB::commit();

            return [
                'message' => 'Creación de suscripción exitosa.',
                'subcription' => $subcription
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'message' => 'Ocurrió el siguiente problema: ' . $e->getMessage(),
            ];
        }
    }


    public function registerSubcriptPromot($root, array $args){
        $now = Carbon::now();
        $paymentData = $args['requestPayment'];
        $subscriptionId = $paymentData['subscriptionId'];
        $promotionCode = $paymentData['promotionCode'] ?? null;

        // Buscar la suscripción
        $subscription = Suscripcion::find($subscriptionId);
        if (!$subscription) {
            return ['message' => 'Suscripción no encontrada.'];
        }

        // Validar promoción (si se proporciona un código)
        $promotion = null;
        if ($promotionCode) {
            $promotion = Promocion::where('codePromotion', $promotionCode)
                ->where('status', true)
                ->where('finishDate', '>=', $now)
                ->first();

            if (!$promotion) {
                return ['message' => 'La promoción no es válida o está vencida.'];
            }

            // Verificar si ya fue aplicada
            $existingPromotion = Promocion_suscripcion::where('subcriptionsId', $subscription->id)
                ->where('promotionId', $promotion->id)
                ->first();

            if ($existingPromotion) {
                return ['message' => 'La promoción ya fue aplicada a esta suscripción.'];
            }
            DB::beginTransaction();
            try{
                // Registrar la promoción aplicada
                Promocion_suscripcion::create([
                    'subcriptionsId' => $subscription->id,
                    'promotionId' => $promotion->id,
                ]);


                // Calcular monto a pagar
                $amount = $paymentData['amount']; // Monto original
                $amountPromotion = 0; // Monto de descuento
                $amountPay = $amount;

                if ($promotion) {
                    if ($promotion->type === 'Descuento') {
                        $amountPromotion = $amount * ($promotion->discount_value / 100);
                        $amountPay = $amount - $amountPromotion;
                    }
                }

                // Registrar el pago
                $payment = Pago::create([
                    'bank' => $paymentData['bank'],
                    'account' => $paymentData['account'],
                    'social_reason' => $paymentData['social_reason'],
                    'amount' => $amount,
                    'amount_promotion' => $amountPromotion,
                    'amount_pay' => $amountPay,
                    'method_payment' => $paymentData['method_payment'],
                    'date_payment' => $now,
                    'subscriptionId' => $subscription->id,
                    'status' => 0,
                ]);

                // Procesar la extensión de duración (si aplica)
                if ($promotion && $promotion->type === 'Fecha') {
                    $subscription->finishDate = $subscription->finishDate->addDays($promotion->discount_value);
                    $subscription->save();
                }
                DB::commit();
                return [
                    'message' => 'Pago procesado correctamente.',
                    'subscription' => $subscription,
                    'payment' => $payment,
                    'promotion' => $promotion,
                ];
            }catch(\Exception $e){
                DB::rollBack();
                return [
                    'message' => 'Se produjo el siguiente error ' . $e->getMessage()
                ];
            }
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

    public function technicalSubscription($root, array $args){

        $subcriptionData = $args['requestSubcription'];
        $technician = Tecnico::find($subcriptionData['technicianId']);

        // Verificar si el técnico existe
        if (!$technician) {
            return [
                'message' => 'No existe el técnico.',
            ];
        }

        // Verificar si el técnico tiene una asociación con alguna suscripción
        $subscriptionAssociation = Technician_subcripcion::where('technicianId', $technician->id)->first();

        if (!$subscriptionAssociation) {
            return [
                'message' => 'El técnico no está asociado a ninguna suscripción.',
                'result' => false ,
                'technician' => $technician
            ];
        }

        $suscripcion = Suscripcion::where('id',$subscriptionAssociation->subcriptionsId)->first();
        return [
            'message' => 'El técnico no está asociado a ninguna suscripción.',
            'result' => true ,
            'technician' => $technician,
            'suscripcion' => $suscripcion
        ];
    }

    public function registerSubcriptionTechncian($root, array $args){

        $subcriptionData = $args['requestSubcription'];
        $technician = Tecnico::find($subcriptionData['technicianId']);

        // Verificar si el técnico existe
        if (!$technician) {
            return [
                'message' => 'No existe el técnico.',
            ];
        }

        $subcription = Suscripcion::find($subcriptionData['subcriptionId']);

        if(!$subcription){
            return [
                'message' => 'No existe la suscripcion.',
            ];
        }
        DB::beginTransaction();
        try{
            $newSubscription = Technician_subcripcion::create([
                'technicianId' => $technician->id,
                'subcriptionsId' => $subcription->id
            ]);
            $now=Carbon::now();
            $newSubscription->starDate = Carbon::now();
            $newSubscription->endDate = $now->addDay($subcription->duration);
            $newSubscription->save();
            //dd($newSubscription);
        DB::commit();
        return [
            'message' => 'El registro de suscripcion fue exitosa.',
            'technician'=>$technician,
            'suscripcion'=>$subcription
        ];

        } catch( \Exception $e ){
            DB::rollBack();
            return[
                'message' => 'sucedio un problema al registrar su suscripcion. ' . $e->getMessage()
            ];
        }
    }
}
