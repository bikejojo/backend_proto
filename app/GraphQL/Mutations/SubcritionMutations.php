<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use App\Models\Suscripcion;
use App\Models\Pago;
use App\Models\Promocion_suscripcion;
use App\Models\Promocion;
use App\Models\Technician_subcripcion;
use Carbon\Carbon;
use App\Services\StateCatalog;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\AssignOp\Concat;

class SubcritionMutations
{
    protected $now;
    public function __construct()
    {
        $this->now = Carbon::now();
    }
    public function creaSubcription($root, array $args){
        $subcriptionData = $args['requestSubcription'];
        //dd($subcriptionData);
        $now = $this->now;
        DB::beginTransaction();
        try {
            $subcription=Suscripcion::create([
                'name' => $subcriptionData['name'],
                'description' => $subcriptionData['description'],
                'codeSubcription' => $subcriptionData['codeSubcription'],
                'status' => 1,
                'price' => $subcriptionData['price'],
                'createDate' => $now
            ]);
            if(($subcriptionData['code_duration']===StateCatalog::DURATION_ANIO) || ($subcriptionData['code_duration']===StateCatalog::DURATION_ANIOS)){
                if($subcriptionData['duration'] < 2){
                    $subcription->durationDescription = $subcriptionData['duration'].' '.$subcriptionData['code_duration']; // duration por dia
                    $calculation=$subcriptionData['duration']*365;
                }else{
                    return[
                        'message' => 'La duracion del tiempo no debe superar a 1 año'
                    ];
                }
            }
            if(($subcriptionData['code_duration']===StateCatalog::DURATION_MES) || ($subcriptionData['code_duration']===StateCatalog::DURATION_MESS)){
                if($subcriptionData['duration'] > 0 && $subcriptionData['duration'] < 4){
                    $subcription->durationDescription = $subcriptionData['duration'].' '.$subcriptionData['code_duration']; // duration por dia
                    $calculation=$subcriptionData['duration']*30;
                }else{
                    return[
                        'message' => 'La duracion del tiempo no debe superar a 3 meses'
                    ];
                }
            }
            if(($subcriptionData['code_duration']===StateCatalog::DURATION_SEMANA) || ($subcriptionData['code_duration']===StateCatalog::DURATION_SEMANAS)){
                if($subcriptionData['duration'] > 0 && $subcriptionData['duration'] < 5){
                    $subcription->durationDescription = $subcriptionData['duration'].' '.$subcriptionData['code_duration']; // duration por dia
                    $calculation=$subcriptionData['duration']*7;
                }else{
                    return[
                        'message' => 'La duracion del tiempo no debe superar a 4 semanas'
                    ];
                }
            }
            $subcription->duration = $calculation;
            $subcription->save();
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
            'message' => 'El técnico está asociado a una suscripción.',
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
