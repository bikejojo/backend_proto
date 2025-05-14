<?php

namespace App\GraphQL\Mutations;

use App\Models\Agenda_Tecnico;
use App\Models\Note;
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
}
