<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Suscripcion;
use App\Models\Tecnico;
use App\Models\Technician_subcripcion;
use App\Models\Promocion;
use App\Models\Promocion_suscripcion;
use App\Services\ValidationModels;
use Carbon\Carbon;

class SubcriptionQuery
{
    public function getAllTechnician($root , array $args){
        $subcriptionData=$args['requestSubcription'];
        $technician = ValidationModels::validationTechnician($subcriptionData['id_technician']);
        $joint = Technician_subcripcion::where('technicianId',$technician->id)->exists();
        $join = Technician_subcripcion::where('technicianId',$technician->id)->where('status',0)->where('subcriptionsId',1)->count();
        //$joins = Technician_subcripcion::where('technicianId',$technician->id)->where('status',1)->exists();
        //dd($join , $joint);
        if(!$joint){
            return [
                'message' => 'Todas las suscripciones.',
                'suscripcion' => Suscripcion::orderBy('subcriptions.id','ASC')->get()
            ];
        }
        //dd($join , $joins);
        if($join===1 ){
            return[
                'message' => 'Todas las suscripciones menos la Free',
                'suscripcion' => Suscripcion::where('codeSubcription','!=','FREE')->orderBy('id','ASC')->get()
            ];
        }else{
            return[
                'message' => 'Usted tiene ya disponible una suscripcion activa.',
                'suscripcion' => []
            ];
        }
        return[
                'message' => 'Surgio problemas al momento de mostrar las suscripciones.'
        ];


    }
    public function validationDatePromotion($root,array $args){
        $promotionData = $args['requestPromotion'];
        $now=Carbon::now();
        $promotion = Promocion::where('codePromotion',$promotionData['codePromotion'])->first();
        if(!$promotion){
            return [
                'message' => 'La promocion ingresada no existe'];
        }
        if($promotion->status === false){
            return [
                'message' => 'La promocion ingresada ya no tiene validez'];
        }
        if($promotion->finishDate <= $now ){
            return[
                'message' => 'La promocion ingresada esta vencida.'
            ];
        }
        return [
            'message' => 'La promocion sigue vigente',
            'promocion' => $promotion
        ];
    }

    public function expirationPromotion($root,array $args){
        $now=Carbon::now();
        $promotion = Promocion::all();
        $validPromotions = $promotion->filter(function ($promotion) use($now){
            return $promotion->finishDate >= $now;
        });

        if ($validPromotions->isEmpty()) {
            return [
                'message' => 'No hay promociones vigentes',
                'promocion' => []
            ];
        }

        return [
            'message' => 'La promocion sigue vigente',
            'promocion' => $validPromotions
        ];
    }

    public function falsePromotion($root,array $args){
        $now=Carbon::now();
        $promotion = Promocion::all();
        $validPromotions = $promotion->filter(function ($promotion) use($now){
            return $promotion->status === false;
        });

        if ($validPromotions->isEmpty()) {
            return [
                'message' => 'No hay promociones vigentes',
                'promocion' => []
            ];
        }

        return [
            'message' => 'La promociones no siguen vigente',
            'promocion' => $validPromotions
        ];

    }
    public function promotionValid($root,array $args){
        $now=Carbon::now();
        $promotion = Promocion::all();
        $validPromotions = $promotion->filter(function ($promotion) use($now){
            return $promotion->status === true && $promotion->finishDate >= $now;
        });

        if ($validPromotions->isEmpty()) {
            return [
                'message' => 'No hay promociones vigentes',
                'promocion' => []
            ];
        }

        return [
            'message' => 'La promocion sigue vigente',
            'promocion' => $validPromotions
        ];
    }

    public function getSubcriptionPromotion( $root , array $args){
        $subcriptionPromotionData = $args['requestSubcriptionPromotion'];
        $subcriptionData = Suscripcion::find($subcriptionPromotionData['id_suscription']);

        $promotionData = Promocion::where('codePromotion',$subcriptionPromotionData['codePromotion'])
                                ->first();

        $validacion = Promocion_suscripcion::where('subcriptionsId',$subcriptionData->id)
                                        ->where('promotionId',$promotionData->id)
                                        ->first();
        $technician = Tecnico::select('technicians.*')
            ->join('technician_subcription', 'technicians.id', '=', 'technician_subcription.technicianId')
            ->where('technician_subcription.subcriptionsId',$subcriptionData->id)
            ->first();
        if($validacion){
            return[
                'message' => 'Esta suscripcion utilizo la promocion.',
                'result' => true,
                'technician' => $technician,
                'promocion'=> $promotionData,
                'suscripcion'=> $subcriptionData
            ];
        }

        return[
            'message' => 'Esta suscripcion no utilizo la promocion.',
            'result' => false,
            'promocion'=> $promotionData,
            'suscripcion'=> $subcriptionData
        ];
    }

    public function getSuscripcionHistorial($root , array $args){
        $suscripcionData = $args['requestSubcription'];
        $technicianId = $suscripcionData['id_technician'];
        $technician = ValidationModels::validationTechnician($technicianId);
        $suscripciones = Suscripcion::join('technician_subcription','subcriptions.id','=','technician_subcription.subcriptionsId')
        ->where('technician_subcription.technicianId',$technician->id)
        ->select('subcriptions.*','technician_subcription.*')
        ->orderBy('technician_subcription.starDateSubcription','DESC')
        ->get();
            // Formato de respuesta
        $formattedSuscripciones = $suscripciones->map(function ($suscripcion) {
            return [
                'name' => $suscripcion->name,
                'durationDescription' => $suscripcion->durationDescription,
                'codeSubcription' => $suscripcion->codeSubcription,
                'starDateSubcription' => $suscripcion->starDateSubcription,
                'endDateSubcription' => $suscripcion->endDateSubcription,
                'status' => $suscripcion->status
            ];
        });

        return [
            'message' => 'Listado de las suscripciones que ha hecho el tecnico.',
            'suscripcion' => $formattedSuscripciones
        ];
    }
}
