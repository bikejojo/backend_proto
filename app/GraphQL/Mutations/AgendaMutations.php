<?php

namespace App\GraphQL\Mutations;

use App\Models\Agenda_Tecnico;
use Carbon\Carbon;
use App\Models\Tipo_Actividad;
Use App\Models\Tipo_Estado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgendaMutations
{

    public function indexTecnico($root ,array $args){
        $agendaData = $args['agendaRequest'];
    }

    public function aggActivity($root, array $args){
        DB::beginTransaction();
        try {
            $requestActivity  = $args['requestActivite'];
            $activity = new Tipo_Actividad();
                $activity->description = $requestActivity['description'];
                $activity->entity_type = $requestActivity['entity_type'];
            $activity->save();
            DB::commit();
            return [
                'message'=>'agregado existoso',
                'activity'=>$activity,
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::warning('Se presentaron los siguientes problemas: ',$e->getMessage());
            return [
                'message' => 'problemaas de backend' . $e->getMessage()
            ];
        }
    }

    public function updateActivity($root,array $args){
        try{
            DB::beginTransaction();
            $requestActivity = $args['requestActivity'];
            $idActivity = $requestActivity['id'];
            $activityData = Tipo_Actividad::where('id',$idActivity)->first();
                $activityData->description = $requestActivity['description'];
                $activityData->entity_type = 'service';
            $activityData->save();

            DB::commit();
            return [
                'message'=>'creacion existosa en actividades',
                'activity' => $activityData
            ];
        } catch(\Exception $e){
            Log::warning('Surgio un problema ' . $e->getMessage());
            DB::rollBack();
            return [
                'message' => 'surgio un problema al crear' . $e->getMessage(),
            ];
        }
    }

    public function deleteActivity($root , array $args){
        try {
            DB::beginTransaction();
            $idActivity = $args['id'];
            $activityData = Tipo_Actividad::where('id',$idActivity)->first();
                $activityData->status = 0;
            $activityData->save();

        }catch(\Exception $e){
            DB::rollBack();
            Log::warning('Surgio los siguientes problemas ' , $e->getMessage());
            return [
                'message' => 'Surgio los siguientes problemas ' . $e->getMessage()
            ];
        }
    }
}
