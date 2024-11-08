<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\GraphQL\Mutations\ServicioMutations;
use App\Helpers\StatusHelper;
use App\Models\Agenda_Tecnico;
USE App\Models\Cliente_Externo;
USE App\Models\Cliente_Interno;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Servicio;
use App\Models\Tecnico;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AgendaQuery{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    const servicioInternal = ServicioMutations::clientInternal;
    const servicioExternal = ServicioMutations::clientExternal;

    public function listAgendaInternalClient($root , array $args){
        $agendaData = $args['requestAgenda'];
        $technicianId = $agendaData['technicianId'];
        $dateFilter = $agendaData['entryDate'] ?? StatusHelper::ORDER_NAME_RECENT;
        $tecnico = Tecnico::find($technicianId);
        if(!$tecnico){
            return[
                'message' => 'No existe tecnico.'
            ];
        }
        $agenda = Agenda_Tecnico::where('technicianId',$tecnico->id)->first();
        if(!$agenda){
            return[
                'message' => 'El tecnico no tiene una agenda.'
            ];
        }
        $query = Detalle_Agenda_Tecnico::where('agendaTechnicalId',$agenda->id)
        ->where('typeClient',self::servicioInternal)->get();
        if($dateFilter){
            $_query = self::dateHelper($dateFilter,$query,'serviceDate');
        }
        $service = $_query->get();
    }
    public function listAgendaExternalClient($root , array $args){
        $agendaData = $args['requestAgenda'];
        $technicianId = $agendaData['technicianId'];
        $dateFilter = $agendaData['entryDate'] ?? StatusHelper::ORDER_NAME_RECENT;
        $tecnico = Tecnico::find($technicianId);
        if(!$tecnico){
            return[
                'message' => 'No existe tecnico.'
            ];
        }
        $agenda = Agenda_Tecnico::where('technicianId',$tecnico->id)->first();
        if(!$agenda){
            return[
                'message' => 'El tecnico no tiene una agenda.'
            ];
        }
        $query = Detalle_Agenda_Tecnico::where('agendaTechnicalId',$agenda->id)
        ->where('typeClient',self::servicioExternal);

        if($dateFilter){
            $_query = self::dateHelper($dateFilter,$query,'serviceDate');
        }

        $service = $_query->get();
        if ($service->isEmpty()) {
            return [
                'message' => 'No hay servicios en la agenda',
                'agenda' => null
            ];
        }

        $agenda = $service->map(function ($request){
            $tecnico=Tecnico::leftjoin('technician_agenda', 'technician_agenda.technicianId', '=', 'technicians.id')
            ->leftjoin('detail_technical_agenda', 'detail_technical_agenda.agendaTechnicalId', '=', 'technician_agenda.id')
            ->where('detail_technical_agenda.agendaTechnicalId', $request->agendaTechnicalId)
            ->select('technicians.*')->first();
            //dd($tecnico);
            $client=DB::table('external_clients')
            ->leftJoin('associationTechnClient', function($join) use ($tecnico) {
                $join->on('associationTechnClient.technicalId', '=', DB::raw($tecnico->id));
            })
            ->where('external_clients.id', $request->clientId)
            ->select('external_clients.*')
            ->first();
            //dd($request);
            return [
                'agenda' => $request ,
                'technician' => $tecnico ,
                'client'=> $client
            ];
        });
        //dd($agenda);
        return [
            'message' => 'Listado de agenda',
            'content' => $agenda
        ];
    }

    private function dateHelper($dateFilter,$query,$nameAttribute){
        $now = Carbon::now();
        if($dateFilter ==='mas reciente'){
            return $query->where('serviceDate','<=',$now)->orderByDesc('serviceDate');
        }

        return $query->whereDate('serviceDate',$dateFilter);
    }
}
