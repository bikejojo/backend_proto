<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Payment;
use App\Models\Tecnico;
use App\Models\Suscripcion;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PagoMutations
{
    public function generatorPayment($root, array $args)
    {
        $paymentData = $args['requestPayment'];

        $subscription = Suscripcion::find($paymentData['subscriptionId']);
        if (!$subscription) {
            return [
                'message' => 'No se encontró la suscripción especificada.'
            ];
        }
        $technician=Tecnico::find($subscription->technicianId);
        if(!$technician){
            return [
                'message' => 'No se encontró el tecnico.'
            ];
        }

        $comprobacion=Suscripcion::where('id',$paymentData['subscriptionId'])->where('technicianId',$subscription->technicianId)->first();
        if(!$comprobacion){
            return[
                'message'=>'No existe relacion entre tecnico y suscripcion.'
            ];
        }

        DB::beginTransaction();
        try {
            // Crear un nuevo pago con estado "Pendiente" (status = 0)
           
            $payment = Pago::create([
                'bank' => $paymentData['bank'],
                'account' => $paymentData['account'],
                'social_reason' => $paymentData['social_reason'],
                'amount' => $paymentData['amount'],
                'amount_promotion' => 0,
                'amount_pay' => $paymentData['amount'],
                'method_payment' => $paymentData['method_payment'],
                'date_payment' => Carbon::now(),
                'photo_qr' => $paymentData['photo_qr'] ?? null,
                'subscriptionId' => $subscription->id,
                'status' => 0, // Pendiente
            ]);

            DB::commit();

            return [
                'message' => 'El pago inicial ha sido generado correctamente.',
                'payment' => $payment,
                'technician' => $technician,
                'suscripcion' => $subscription,
            ];
        } catch (\Exception $e) {
            DB::rollback();

            return [
                'message' => 'Error al generar el pago: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Procesar un pago existente (Realizar el pago)
     */
    public function payment($root, array $args)
    {
        $paymentData = $args['requestPayment'];

        $payment = Pago::find($paymentData['paymentId']);
        if (!$payment) {
            return [
                'message' => 'No se encontró el pago especificado.'
            ];
        }
        $subscription = Suscripcion::where('id',$payment->subscriptionId)->first();
        if (!$subscription) {
            return [
                'message' => 'No se encontró la suscripción especificada.'
            ];
        }
        $technician=Tecnico::where('id',$subscription->technicianId)->first();
        if(!$technician){
            return [
                'message' => 'No se encontró el tecnico.'
            ];
        }

        // Validar el estado actual del pago
        if ($payment->status !== 0) { // Solo procesar si el estado es "Pendiente"
            return [
                'message' => 'El pago ya ha sido procesado o está inactivo.'
            ];
        }

        DB::beginTransaction();
        try {
            // Actualizar el estado del pago a "Realizado" (status = 1)
            $payment->status = 1; // Realizado
            $payment->save();

            DB::commit();

            return [
                'message' => 'El pago ha sido procesado correctamente.',
                'payment' => $payment,
                'technician' => $technician,
                'suscripcion' => $subscription,
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cancelar un pago existente
     */
    public function cancelPayment($root, array $args)
    {
        $paymentData = $args['requestPayment'];

        $payment = Pago::find($paymentData['paymentId']);
        if (!$payment) {
            return [
                'message' => 'No se encontró el pago especificado.'
            ];
        }

        // Validar el estado actual del pago
        if ($payment->status !== 0) { 
            return [
                'message' => 'El pago ya ha sido procesado o está inactivo.'
            ];
        }

        DB::beginTransaction();
        try {
            // Actualizar el estado del pago a "Cancelado" (status = 2)
            $payment->status = 2; // Cancelado
            $payment->save();

            DB::commit();

            return [
                'message' => 'El pago ha sido cancelado correctamente.',
                'payment' => $payment
            ];
        } catch (\Exception $e) {
            DB::rollback();

            return [
                'message' => 'Error al cancelar el pago: ' . $e->getMessage()
            ];
        }
    }
}
