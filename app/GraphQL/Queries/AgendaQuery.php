<?php declare(strict_types=1);

namespace App\GraphQL\Queries;
use App\Models\Agenda_Tecnico;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Cita;

class AgendaQuery{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function agendaAllow($root , array $args){
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
    }
}
