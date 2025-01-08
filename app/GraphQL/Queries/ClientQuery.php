<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\GraphQL\Mutations\ServicioMutations;
use App\Models\Agenda_Tecnico;
use App\Models\Cliente_Externo;
use App\Models\Cliente_Interno;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Servicio;
use App\Models\Solicitud;
use App\Models\Historial_Servicios;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientQuery{

    public function searchExternalByName($root, array $args)
    {
        $clientData = $args['requestClient'];
        $tecnicoId = $clientData['technicalId'];

        // Verificar si existe el parámetro de búsqueda
        if (!empty($clientData['searchParameter'])) {
            $clientNamePhone = strtolower($clientData['searchParameter']);

            // Realizar la búsqueda en la tabla de asociación
            $clientExterno = Asociacion_Cliente_Tecnico::where('technicalId', $tecnicoId)
                ->where('status', 1)  // Filtrar por técnico y estado activo
                ->where(function($query) use ($clientNamePhone) {
                    $query->where(DB::raw('LOWER(full_name)'), 'LIKE', "%{$clientNamePhone}%")
                          ->orWhere(DB::raw('LOWER(phone_number)'), 'LIKE', "%{$clientNamePhone}%");
                })
                ->orderBy('created_at', 'desc')
                ->get(['associationTechnClient.full_name', 'associationTechnClient.phone_number','associationTechnClient.status','associationTechnClient.clientId']);  // Obtener solo nombre y teléfono
        } else {
            // Si no hay parámetro de búsqueda, solo aplicar el filtro del técnico y estado
            $clientExterno = Asociacion_Cliente_Tecnico::where('technicalId', $tecnicoId)
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->get(['associationTechnClient.full_name', 'associationTechnClient.phone_number','associationTechnClient.status','associationTechnClient.clientId']);  // Obtener solo nombre y teléfono
        }

        // Verificar si no se encontraron resultados
        if ($clientExterno->isEmpty()) {
            return [
                'message' => 'No se encontraron resultados',
                'customer_external' => null
            ];
        }


        // Retornar los resultados encontrados
        return [
            'message' => 'Resultados encontrados',
            'customer_external' => $clientExterno->map(function ($client) {
                return [
                    'fullName' => $client->full_name,
                    'phoneNumber' => $client->phone_number,
                    'status' => $client->status,
                    'id' => $client->clientId
                ];
            })
        ];
    }


    public function searchInternalByName($root, array $args){
        $clientData = $args['requestClient'];
        $tecnicoId = $clientData['technicalId'];

        // Verificar si existe el parámetro de búsqueda
        if (!empty($clientData['searchParameter'])) {
            $clientNamePhone = strtolower($clientData['searchParameter']);

            $clientInterno = Cliente_Interno::leftjoin('list_internal_clients', 'internal_clients.id', '=', 'list_internal_clients.clientId')
                ->where('list_internal_clients.technicianId', $tecnicoId)
                ->where(function ($query) use ($clientNamePhone) {
                    $query->where(DB::raw('LOWER("internal_clients"."firstName")'), 'LIKE', "%{$clientNamePhone}%")
                        ->orWhere(DB::raw('LOWER("internal_clients"."lastName")'), 'LIKE', "%{$clientNamePhone}%")
                        ->orWhere(DB::raw('LOWER("internal_clients"."phoneNumber")'), 'LIKE', "%{$clientNamePhone}%");
                })
                ->select('internal_clients.*', 'internal_clients.created_at')
                ->distinct()
                ->orderBy('internal_clients.created_at', 'desc')
                ->get();
        } else {
            // Si no hay parámetro de búsqueda, solo aplicar el filtro del técnico
            $clientInterno = Cliente_Interno::leftjoin('list_internal_clients', 'internal_clients.id', '=', 'list_internal_clients.clientId')
            ->where('list_internal_clients.technicianId', $tecnicoId)
            ->select('internal_clients.*', 'internal_clients.created_at') // Agregar columnas necesarias
            ->distinct()
            ->orderBy('internal_clients.created_at', 'desc') // Ordenar por fecha de creación
            ->get();
        }

        // Verificar si no se encontraron resultados
        if ($clientInterno->isEmpty()) {
            return [
                'message' => 'No se encontraron resultados',
                'client_i' => null
            ];
        }

        // Retornar los resultados encontrados
        return [
            'message' => 'Resultados encontrados',
            'client_i' => $clientInterno
        ];
    }

    public function technicianByclient($root,array $args){
        $technicianId = $args['id_technician'];

        $listado=Asociacion_Cliente_Tecnico::where('technicalId',$technicianId)
        ->where('associationTechnClient.status',1)
        ->leftjoin('external_clients','external_clients.id','=','clientId')
        ->orderBy('external_clients.created_at', 'desc')
        ->select('associationTechnClient.full_name','associationTechnClient.phone_number','external_clients.id','associationTechnClient.status')
        ->get();
        //dd($listado);
        if($listado->isEmpty()){
            return[
                'message' => 'El tecnico no tiene una lista de clientes propios',
                'customer_external' => null
            ];
        }

        return[
            'message' => 'Clientes propios de los tecnicos',
            'customer_external' => $listado->map(function ($client) {
                return [
                    'id' => $client->id,
                    'fullName' => $client->full_name,
                    'phoneNumber' => $client->phone_number,
                    'status' => $client->status
                ];
            })
        ];
    }

    public function quantifyclient($root , array $args){
        $clientData = $args['requestClient'];
        $startDate = $clientData['startDate'];
        $finishDate = $clientData['finishDate'];

        $servicesExt = DB::table('services')
            ->where('typeClient',ServicioMutations::clientExternal)
            ->leftjoin('external_clients', 'services.clientId', '=', 'external_clients.id')
            ->select(
                'external_clients.fullName',
                DB::raw('DATE("services"."programDate")'),
                DB::raw('COUNT(services.id) as serviceCount'),
                DB::raw("'Cliente Externo' as clientType")
            )
            ->whereBetween('services.programDate', [$startDate, $finishDate])
            ->groupBy('external_clients.fullName',"programDate")
            ->orderBy("programDate")
            ->get();

        $servicesInt = DB::table('services')
        ->where('typeClient','1')
        ->leftjoin('internal_clients', 'services.clientId', '=', 'internal_clients.id')
        ->select(
            DB::raw('CONCAT(internal_clients."firstName", \' \', internal_clients."lastName") as "fullName"'),
            DB::raw('DATE("services"."programDate")'),
            DB::raw('COUNT(services.id) as serviceCount'),
            DB::raw("'Cliente Interno' as clienttype")
        )
        ->whereBetween('services.programDate', [$startDate, $finishDate])
        ->groupBy("fullName","programDate")
        ->orderBy("programDate")
        ->get();

        return [
            'servicesExternal'=> $servicesExt,
            'servicesInternal' => $servicesInt
        ];
    }

    public function quantityClient($root, array $args){
        $technicialId = $args['id_technician']['id'];
        $agenda = Agenda_Tecnico::where('technicianId',$technicialId)->first();

        $detailAgenda = Detalle_Agenda_Tecnico::where('agendaTechnicalId',$agenda->id)
        ->select('clientId', 'typeClient')
        ->get();
        $clienteExternoCount = $detailAgenda->where('typeClient', 2)
        ->count();
        $clienteInternoCount = $detailAgenda->where('typeClient', 1)
        ->count();
        $clientSum = $clienteExternoCount + $clienteInternoCount;

        return [
            'message' => 'Total de clientes de tecnico',
            'quantity' => $clientSum
        ];
    }

    public function quantityCities($root, array $args){
        $technicialId = $args['id_technician'];
        $agenda = Agenda_Tecnico::where('technicianId',$technicialId)->first();

        $detailAgenda = Detalle_Agenda_Tecnico::where('agendaTechnicalId',$agenda->id)
        ->whereNotNull('serviceDate')
        ->count();
        return [
            'message' => 'Total de citas programas de tecnico',
            'quantity' => $detailAgenda
        ];
    }

    public function list_requests_services($root,array $args){
        $clientId = $args['clientRequest']['id_client'];

        // Obtener el historial de servicios y solicitudes para el cliente
        $historial = Historial_Servicios::where('clientId', $clientId)
            ->with(['client', 'technician'])
            ->orderBy('outsetDate', 'desc')
            ->get();

        // Si no hay historial, devolver un mensaje apropiado
        if ($historial->isEmpty()) {
            return [
                'message' => 'No se encontraron registros en el historial.',
                'client' => null,
                'historial' => null,
            ];
        }

        // Procesar el historial
        $history = $historial->map(function ($record) {
            // Determinar si es una solicitud o un servicio
            $type = $record->descriptionJob == 1 ? 'Solicitud' : 'Servicio';

            // Obtener detalle de la solicitud o servicio
            $detail = null;
            if ($type === 'Solicitud') {
                $solicitud = Solicitud::find($record->jobId);
                if ($solicitud) {
                    $detail = [
                        'title' => $solicitud->titleRequests,
                        'description' => $solicitud->requestDescription,
                        'dateCreate' => $solicitud->registrationDateTime ? Carbon::parse($solicitud->registrationDateTime)->toDateString() : null,
                    ];
                }
            } else {
                $servicio = Servicio::find($record->jobId);
                if ($servicio) {
                    $detail = [
                        'title' => $servicio->titleService,
                        'description' => $servicio->serviceDescription,
                        'dateCreate' => $servicio->createdDateTime ? Carbon::parse($servicio->createdDateTime)->toDateString() : null,
                        'dateFinish' => $servicio->finishDateTime_client ? Carbon::parse($servicio->finishDateTime_client)->toDateString() : null,
                    ];
                }
            }

            return [
                'type' => $type,
                'technician' => $record->technician,
                'detail' => $detail,
            ];
        });

        // Obtener información del cliente
        $client = Cliente_Interno::find($clientId);

        return [
            'message' => 'Historial obtenido correctamente.',
            'client' => $client,
            'historial' => $history,
        ];
    }

}
