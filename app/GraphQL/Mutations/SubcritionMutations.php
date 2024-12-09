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
                
            ]);
        DB::commit();
            return[
                'message'=>'Creacion de subscripcion exitosa',
                'message'=>'Se espera que ingrese alguna promocion',
                'technician'=>Tecnico::find($technicianId),
                'subcription'=>$subcription
            ];
        } catch (\Exception $e){
            DB::rollback();
            return[
                'message'=>'Ocurrio el siguiente problema. ' .$e->getMessage()
            ];
        }
    }

    public function registerSubcriptPromot($root,array $args){
        $subcriptionData = $args['requestSubcription'];
        $promotionData = $subcriptionData['codePromotion'];
        if(Promocion::where('codePromotion',$promotionData)->first()){
            return [
                'message' => 'La promocion dejo de ser valida.'
            ];
        }
        $subcription=Suscripcion::find($subcriptionData['id']);
        $promotion=Promocion::where('codePromotion',$promotionData)->first();
        DB::beginTransaction();
        try{

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return [
                'message'=> 'Fallo en el ' . $e->getMessage()
            ];
        }
    }

    public function editSubcription($root,array $args){
        $subcriptionData = $args['requestSubcription'];
    }

    public function lowSubcription($root,array $args){
        $subcriptionData = $args['requestSubcription'];
    }
}
