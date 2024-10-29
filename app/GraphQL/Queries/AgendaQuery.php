<?php declare(strict_types=1);

namespace App\GraphQL\Queries;
use App\Models\Agenda_Tecnico;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Cita;
use App\Models\Servicio;
use Carbon\Carbon;
use Nuwave\Lighthouse\Federation\Resolvers\Service;

class AgendaQuery{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    /*public function agendaAllow($root , array $args){
        $agendaData = $args['requestData'];
        $agendaId = $agendaData['id_technician'];
        $agenda = Agenda_Tecnico::where('technicalId',$agendaId)
        ->leftjoin('detail_technical_agenda','technician_agenda.id','=','detail_technical_agenda.agendaTechnicalId')
        ->leftjoin('citation','detail_technical_agenda.citationId','=','citation.id')
        ->get();

        // Aquí puedes mapear los resultados para ajustarlos al tipo que definiste en GraphQL
        $agendaFormatted = $agenda->map(function($item) {
            return [
                'id' => $item->id,
                'technicalId' => $item->technicalId,
                'detailTechnicalAgenda' => [
                    [
                        'id' => $item->detail_technical_agenda_id,
                        'agendaTechnicalId' => $item->agendaTechnicalId,
                        'citationId' => $item->citationId,
                        'citation' => [
                            'id' => $item->citation_id,
                            'clientId' => $item->clientId,
                            'serviceId' => $item->serviceId,
                            'description' => $item->description,
                            'date' => $item->date
                        ]
                    ]
                ]
            ];
        });

        return [
            'message' => 'Listado de la agenda',
            'agenda' => $agendaFormatted
        ];
    }*/
    public function getAllowAgenda($root,array $args){
        $tcita=2;
        $tService=1;
        $agendaData = $args['requestAgenda'];
        $tecnicoid = $agendaData['id_technician'];
        $agenda = Agenda_Tecnico::where('technicianId',$tecnicoid)
        ->first();
        $detailAgenda = Detalle_Agenda_Tecnico::where('agendaTechnicalId',$agenda->id)->get();
        $citations =[];
        $services = [];
        foreach($detailAgenda as $detail){
            if(!is_null($detail->citationId)){
                $citation = Cita::where('id',$detail->citationId)->first();
                if($citation){
                    $citations[]=$citation;
                }
            }
            if(!is_null($detail->serviceId)){
                $service = Servicio::where('id',$detail->serviceId)->first();
                if($service){
                    $services[]=$service;
                }
            }
        }
        //dd($citations);
        return [
            'message' => 'Agenda del tecnico.',
            'agenda' => $agenda,
            'detailAgenda' => $detailAgenda,
            'citation' => $citations,
            'service' => $services
        ];
    }

    public function getAgendaByDate($root , array $args)
    {
        $agendaData = $args['requestAgenda'];
        $tecnicoid = $agendaData['id_technician'];
        $inputDate = isset($agendaData['date']) ? Carbon::parse($agendaData['date']) : null;

        $agenda = Agenda_Tecnico::where('technicianId', $tecnicoid)->first();

        // Obtener servicios en la fecha exacta
        $exactDateServices = Detalle_Agenda_Tecnico::where('agendaTechnicalId', $agenda->id)
            ->whereHas('service', function($query) use ($inputDate) {
                $query->whereDate('programDate', $inputDate);
            })
            ->with('service')  // Cargar los datos de Service
            ->get();
            //->pluck('service'); // Extraer solo los servicios para simplificar el resultado

        // Si no hay servicios en la fecha exacta, buscar los más cercanos
        if ($exactDateServices->isEmpty()) {
            $closestServices = Detalle_Agenda_Tecnico::where('agendaTechnicalId', $agenda->id)
                ->whereHas('service', function($query) use ($inputDate) {
                    $query->where('programDate', '>', $inputDate);
                })
                ->with(['service' => function($query) {
                    $query->orderBy('programDate', 'asc');
                }])
                ->limit(5)
                ->get();
                //->pluck('service'); // Extraer solo los servicios para simplificar el resultado
               //dd($closestServices);
            return [
                'message' => 'Servicios más cercanos a la fecha proporcionada.',
                'services' => $closestServices
            ];
        }

        return [
            'message' => 'Servicios en la fecha especificada.',
            'services' => $exactDateServices
        ];
    }

    public function getContentServiceId($root , array $args){
        $serviceDate = $args['requestAgenda'];
        $serviceId = $serviceDate['id'];
        $service = Servicio::find($serviceId);
        return [
            'message' => 'contenido de la agenda.',
            'service' => $service
        ];
    }
    public function getAgendaByClient($root , array $args){
        $agendaData = $args['requestAgenda'];
        $tecnicoid = $agendaData['id_technician'];
        $clientId = $agendaData['id_client'];
        $agenda = Agenda_Tecnico::where('technicianId',$tecnicoid)
        ->first();
        $detailAgenda = Detalle_Agenda_Tecnico::where('agendaTechnicalId',$agenda->id)
        ->where('clientId',$clientId)
        ->get();
        $citations =[];
        $services = [];
        foreach($detailAgenda as $detail){
            if(!is_null($detail->citationId)){
                $citation = Cita::where('id',$detail->citationId)->first();
                if($citation){
                    $citations[]=$citation;
                }
            }
            if(!is_null($detail->serviceId)){
                $service = Servicio::where('id',$detail->serviceId)->first();
                if($service){
                    $services[]=$service;
                }
            }
        }

        return [
            'message' => 'Agenda del tecnico.',
            'agenda' => $agenda,
            'detailAgenda' => $detailAgenda,
            'service' => $services,
            'citation' => $citations
        ];
    }
}
