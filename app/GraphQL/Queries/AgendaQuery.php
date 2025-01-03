<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\GraphQL\Mutations\ServicioMutations;

use App\Helpers\StatusHelper;
use App\Models\Agenda_Tecnico;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Tecnico;
use App\Services\ValidationModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AgendaQuery{

    const servicioInternal = ServicioMutations::clientInternal;
    const servicioExternal = ServicioMutations::clientExternal;

    public function listAgendaInternalClient($root, array $args) {
        $agendaData = $args['requestAgenda'];
        $technicianId = $agendaData['technicianId'];
        $dateFilter = $agendaData['entryDate'] ?? StatusHelper::ORDER_NAME_RECENT;
        $tecnico = ValidationModels::validationTechnician($technicianId);

        $agenda = Agenda_Tecnico::where('technicianId', $tecnico->id)->first();
        if (!$agenda) {
            return [
                'message' => 'El tecnico no tiene una agenda.'
            ];
        }
        try{ //terminar despues de reunion
            $query = Detalle_Agenda_Tecnico::join('services','services.id','=','detail_technical_agenda.serviceId')
                ->where('agendaTechnicalId', $agenda->id)
                ->where('detail_technical_agenda.typeClient', self::servicioInternal) // Cliente interno
                ->where('services.status',1);
            if ($dateFilter) {
                $query = $this->dateHelper($dateFilter, $query, 'detail_technical_agenda.serviceDate');
            }
            $serviceDetails = $query->get();
            if ($serviceDetails->isEmpty()) {
                return [
                    'message' => 'No hay servicios en la agenda',
                    'content' => null
                ];
            }

            $agendaContent = $serviceDetails->map(function ($detail) use ($tecnico) {
                // Obtener cliente interno asociado
                $client = DB::table('internal_clients')
                    ->where('id', $detail->clientId)
                    ->select('internal_clients.*')
                    ->first();

                return [
                    'agenda' => $detail,
                    'technician' => $tecnico,
                    'client' => $client,
                    'service' => [
                        'title' => $detail->service_title,
                        'description' => $detail->service_description,
                    ],
                ];
            });

            return [
                'message' => 'Listado de agenda de cliente interno',
                'content' => $agendaContent
            ];
        } catch(\Exception $e){
            return [
                'message' => 'Fallas en la funcion. ' . $e->getMessage()
            ];
        }
    }
    public function listAgendaExternalClient($root , array $args){
        try{
            $agendaData = $args['requestAgenda'];
            $technicianId = $agendaData['technicianId'];
            $dateFilter = $agendaData['entryDate'] ?? StatusHelper::ORDER_NAME_RECENT;
            $tecnico = ValidationModels::validationTechnician($technicianId);

            $agenda = ValidationModels::validationAgenda($tecnico->id);
            $query = Detalle_Agenda_Tecnico::join('services','services.id','=','detail_technical_agenda.serviceId')
            ->where('detail_technical_agenda.agendaTechnicalId',$agenda->id)
            ->where('detail_technical_agenda.typeClient',self::servicioExternal)
            ->where('services.status',1);

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
                ->leftjoin('services','services.id','=','detail_technical_agenda.serviceId')
                ->where('services.status',1)
                ->where('detail_technical_agenda.agendaTechnicalId', $request->agendaTechnicalId)
                //->select('technicians.*')->first();
                ->select('technicians.*','services.latitude','services.longitude')->first();
                $client=DB::table('external_clients')
                ->leftJoin('associationTechnClient', function($join) use ($tecnico) {
                    $join->on('associationTechnClient.technicalId', '=', DB::raw($tecnico->id));
                })
                ->where('external_clients.id', $request->clientId)
                ->select('external_clients.*')
                ->first();
                return [
                    'agenda' => $request ,
                    'technician' => $tecnico ,
                    'client'=> $client
                ];
            });

            return [
                'message' => 'Listado de agenda de clientes Externos',
                'content' => $agenda
            ];
        } catch(\Exception $e){
            return [
                'message' => 'Fallas en las solicitudes ' . $e->getMessage()
            ];
        }
    }

    private function dateHelper($dateFilter,$query,$nameAttribute){
        $now = Carbon::now()->toDateTimeString();

        if($dateFilter ==='mas reciente'){
            return ($query->where('serviceDate','<=',$now)->orderBy('serviceDate','desc'));
        }

        return ($query->whereDate('serviceDate',$dateFilter));
    }
}
