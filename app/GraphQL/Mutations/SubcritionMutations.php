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
