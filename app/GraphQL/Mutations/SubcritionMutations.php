<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use App\Models\Suscripcion;
use App\Models\Pago;
use App\Models\Promocion_suscripcion;
use App\Models\Promocion;
use App\Models\Technician_subcripcion;
use Carbon\Carbon;
use App\Services\StateCatalog;
use App\Services\ValidationModels;
use Illuminate\Support\Facades\DB;

class SubcritionMutations
{
    protected $now;
    public function __construct()
    {
        $this->now = Carbon::now();
    }
    public function creaSubcription($root, array $args){
        $subcriptionData = $args['requestSubcription'];

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
        $subcriptionData = $args['requestSubcription']['id'];
        if(!Suscripcion::find($subcriptionData)){
            return[
                'message'=>'No existe suscripcion!.'
            ];
        }
        $subcription=Suscripcion::find($subcriptionData);
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

    public function technicalSubscription($root, array $args)
    {
        $subcriptionData = $args['requestSubcription'];
        $technician = Tecnico::find($subcriptionData['technicianId']);

        // Verificar si el técnico existe
        if (!$technician) {
            return [
                'message' => 'No existe el técnico.',
                'result' => false,
            ];
        }

        // Verificar si el técnico tiene una suscripción activa
        $subscriptionAssociation = Technician_subcripcion::where('technicianId', $technician->id)
            ->where('technician_subcription.status', 1) // Suscripción activa
            ->leftjoin('subcriptions', 'technician_subcription.subcriptionsId', '=', 'subcriptions.id')
            ->whereDate('technician_subcription.endDateSubcription', '>=', Carbon::now()) // No vencida
            ->select(
                'subcriptions.id',
                'subcriptions.name',
                'subcriptions.description',
                'subcriptions.codeSubcription',
                'technician_subcription.starDateSubcription',
                'technician_subcription.endDateSubcription'
            )
            ->first();

        if (!$subscriptionAssociation) {
            return [
                'message' => 'El técnico no está asociado a una suscripción activa o esta ya ha expirado.',
                'result' => false,
                'technician' => $technician,
            ];
        }

        return [
            'message' => 'El técnico tiene una suscripción activa y vigente.',
            'result' => true,
            'technician' => $technician,
            'suscripcion' => $subscriptionAssociation,
        ];
    }


    public function registerSubcriptionTechncian($root, array $args){
        {
            $subcriptionData = $args['requestSubcription'];
            $technician = ValidationModels::validationTechnician($subcriptionData['technicianId']);

            $subcription = Suscripcion::find($subcriptionData['subcriptionId']);
            if (!$subcription) {
                return [
                    'message' => 'No existe la suscripción.',
                    'result' => false
                ];
            }

            $existingSubscriptionFree = DB::table('technician_subcription as ts')
            ->join('subcriptions as s', 'ts.subcriptionsId', '=', 's.id')
            ->where('ts.technicianId', $technician->id)
            ->where('s.codeSubcription','FREE')
            ->where('ts.subcriptionsId',$subcription->id)
            ->where('ts.status',0)
            ->exists();
            if($existingSubscriptionFree){
                return[
                    'message' => 'Usted realizo y utilizo una suscripcion FREE.'
                ];
            }
            $existingSubscriptionAll = DB::table('technician_subcription as ts')
            ->join('subcriptions as s', 'ts.subcriptionsId', '=', 's.id')
            ->where('ts.technicianId', $technician->id)
            ->where('ts.status',1)
            //->select('s.id','s.name','s.description','s.codeSubcription','ts.starDateSubcription','ts.endDateSubcription')
            ->exists();
            if($existingSubscriptionAll){
                $existingSubscriptionAll = DB::table('technician_subcription as ts')
                ->join('subcriptions as s', 'ts.subcriptionsId', '=', 's.id')
                ->where('ts.technicianId', $technician->id)
                ->where('ts.status',1)
                ->select('s.id','s.name','s.description','s.codeSubcription','ts.starDateSubcription','ts.endDateSubcription')->first();

                return[
                    'message' => 'Usted cuenta con una suscripcion activa en el sistema.',
                    'suscripcion' => [
                        'id'=>$existingSubscriptionAll->id,
                        'name'=>$existingSubscriptionAll->name,
                        'description'=>$existingSubscriptionAll->description,
                        'codeSubcription'=>$existingSubscriptionAll->codeSubcription,
                        'starDateSubcription'=>$existingSubscriptionAll->starDateSubcription,
                        'endDateSubcription'=>$existingSubscriptionAll->endDateSubcription ] //$existingSubscriptionAll
                        ,'technician' => $technician,
                    'result' => false
                ];
            }

            DB::beginTransaction();
            try {
                $newSubscription = Technician_subcripcion::create([
                    'technicianId' => $technician->id,
                    'subcriptionsId' => $subcription->id
                ]);

                $newSubscription->starDateSubcription = $this->now;
                $newSubscription->endDateSubcription = $this->now->copy()->addDay($subcription->duration);
                $newSubscription->status = StateCatalog::STATUS_ACTIVE;
                $newSubscription->save();
                $subcriptionNew  = Suscripcion::join('technician_subcription','subcriptions.id','=','technician_subcription.subcriptionsId')
                    ->where('technician_subcription.id',$newSubscription->id)
                    ->select('subcriptions.name','subcriptions.description','subcriptions.codeSubcription','technician_subcription.starDateSubcription','technician_subcription.endDateSubcription')
                    ->first();
                DB::commit();
                return [
                    'message' => 'El registro de suscripción fue exitoso.',
                    'technician' => $technician,
                    'suscripcion' => $subcriptionNew,
                    'result' => true
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                return [
                    'message' => 'Sucedió un problema al registrar la suscripción. ' . $e->getMessage(),
                    'result' => false
                ];
            }
        }
    }

    public function disableExpirateSuscription(){
        try {
            // Obtener suscripciones expiradas
            $expiredSuscriptions = Technician_subcripcion::where('endDateSubcription', '<', now())
                ->where('status', 1)
                ->get();

            // Desactivar suscripciones directamente en una sola consulta
            Technician_subcripcion::whereIn('id', $expiredSuscriptions->pluck('id'))
                ->update(['status' => 0]);

            // Desactivar técnicos directamente en una sola consulta
            Tecnico::whereIn('id', $expiredSuscriptions->pluck('technicianId'))
                ->update(['status' => 0]);

            return [
                'message' => 'Suscripciones expiradas desactivadas exitosamente.',
                'count' => $expiredSuscriptions->count()
            ];

        } catch (\Exception $e) {
            return [
                'message' => 'Se presentaron las siguientes fallas: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString(),  // Para una depuración más clara
            ];
        }
    }
}
