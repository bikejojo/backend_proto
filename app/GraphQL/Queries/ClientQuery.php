<?php

namespace App\GraphQL\Queries;

use App\GraphQL\Mutations\ServicioMutations;
use App\Models\Agenda_Tecnico;
use App\Models\Cliente_Interno;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Servicio;
use App\Models\Solicitud;
use App\Models\Historial_Servicios;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use function PHPUnit\Framework\isEmpty;

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

    public function quantifyclient_externo($root, array $args){
        $clientData = $args['requestClient'];
        $startDate = $clientData['startDate'];
        $finishDate_ = Carbon::createFromFormat('Y-m-d', $clientData['finishDate'])->addDay()->format('Y-m-d');
        $finishDate = $clientData['finishDate'];
        //dd($startDate);
        $technicalId = $clientData['technicianId'];
        $servicesExt = DB::table('services')
        ->where('services.typeClient', ServicioMutations::clientExternal)
        ->where('associationTechnClient.status',1)
        ->where('services.status', 1)
        ->where('services.technicalId', $technicalId)
        ->whereBetween(DB::raw('DATE(services."updatedDateTime")'), [$startDate, $finishDate])
        ->where('associationTechnClient.technicalId', $technicalId)
        ->select(
            'associationTechnClient.full_name as fullName',
            //DB::raw('DATE(services."updatedDateTime") as date'), // Extraer solo la fecha
            DB::raw('COUNT(services."id") as servicecount'), // Contar servicios por cliente y día
            DB::raw("'Cliente Externo' as clienttype")
        )
        // Filtro solo por fechas
        ->leftJoin('associationTechnClient', 'services.clientId', '=', 'associationTechnClient.clientId')
        ->groupBy('associationTechnClient.full_name')
        ->orderBy('servicecount', 'desc') // Ordenar por fecha
        ->get();
            //dd($servicesExt);
            $result = $servicesExt->map(function ($service) {
                return [
                    'fullName' => $service->fullName,
                    'servicecount' => $service->servicecount,
                    'clienttype' => $service->clienttype,
                ];
            });

            // Retornar el resultado como un array
            return $result->toArray();
    }

    public function quantifyclient_interno($root, array $args) {
        $clientData = $args['requestClient'];
        $startDate = $clientData['startDate'];
        $finishDate_ = Carbon::createFromFormat('Y-m-d', $clientData['finishDate'])->addDay()->format('Y-m-d');
        $finishDate = $clientData['finishDate'];
        //dd($startDate);
        $technicalId = $clientData['technicianId'];

        $servicesInt = DB::table('services')
            ->where('services.status', 1) // Filtrar solo servicios activos
            ->where('typeClient', ServicioMutations::clientInternal) // Filtrar por tipo de cliente interno
            ->where('technicalId', $technicalId) // Filtrar por técnico específico
            ->leftJoin('internal_clients', 'services.clientId', '=', 'internal_clients.id') // Unión con clientes internos
            ->select(
                DB::raw('CONCAT(COALESCE(internal_clients."firstName", \'\'), \' \', COALESCE(internal_clients."lastName", \'\')) as "fullName"'), // Nombre completo
                DB::raw('COUNT(services."clientId") as servicecount'), // Contar servicios por cliente
                DB::raw("'Cliente Interno' as clienttype") // Tipo de cliente
            )
            ->whereBetween(DB::raw('DATE(services."updatedDateTime")'), [$startDate, $finishDate]) // Filtrar por rango de fechas
            ->groupBy(DB::raw('CONCAT(COALESCE(internal_clients."firstName", \'\'), \' \', COALESCE(internal_clients."lastName", \'\'))')) // Agrupar solo por cliente
            ->orderBy(DB::raw('COUNT(services."clientId")'), 'desc') // Ordenar por cantidad de servicios
            ->get();
            //dd($servicesInt);
            //dd($servicesInt->toSql(), $servicesInt->getBindings());

            $result = $servicesInt->map(function ($service) {
                return [
                    'fullName' => $service->fullName ?? 'Sin nombre', // Usa "fullname" en minúsculas, como aparece en el dd.
                    'servicecount' => $service->servicecount,
                    'clienttype' => $service->clienttype,
                ];
            });
            return $result->toArray();
    }

    /*public function quantifyclient_interno($root, array $args) {
        $clientData = $args['requestClient'];
        $startDate = $clientData['startDate'];
        $finishDate_ = Carbon::createFromFormat('Y-m-d', $clientData['finishDate'])->addDay()->format('Y-m-d');
        $finishDate = $clientData['finishDate'];
        //dd($startDate);
        $technicalId = $clientData['technicianId'];
        $servicesExt = DB::table('services')
            ->where('services.typeClient', ServicioMutations::clientExternal)
            ->where('associationTechnClient.status',1)
            ->where('services.status', 1)
            ->where('services.technicalId', $technicalId)
            ->whereBetween(DB::raw('DATE(services."updatedDateTime")'), [$startDate, $finishDate_])
            ->where('associationTechnClient.technicalId', $technicalId)
            ->select(
                'associationTechnClient.full_name as fullName',
                //DB::raw('DATE(services."updatedDateTime") as date'), // Extraer solo la fecha
                DB::raw('COUNT(services."id") as servicecount'), // Contar servicios por cliente y día
                DB::raw("'Cliente Externo' as clienttype")
            )
            // Filtro solo por fechas
            ->leftJoin('associationTechnClient', 'services.clientId', '=', 'associationTechnClient.clientId')
            ->groupBy('associationTechnClient.full_name')
            ->orderBy('servicecount', 'desc') // Ordenar por fecha
            ->get();
            //dd($servicesExt);
        //dd($servicesExt->toSql(), $servicesExt->getBindings());

        $servicesInt = DB::table('services')
            ->where('services.status', 1) // Filtrar solo servicios activos
            ->where('typeClient', ServicioMutations::clientInternal) // Filtrar por tipo de cliente interno
            ->where('technicalId', $technicalId) // Filtrar por técnico específico
            ->leftJoin('internal_clients', 'services.clientId', '=', 'internal_clients.id') // Unión con clientes internos
            ->select(
                DB::raw('CONCAT(COALESCE(internal_clients."firstName", \'\'), \' \', COALESCE(internal_clients."lastName", \'\')) as "fullName"'), // Nombre completo
                DB::raw('COUNT(services."clientId") as servicecount'), // Contar servicios por cliente
                DB::raw("'Cliente Interno' as clienttype") // Tipo de cliente
            )
            ->whereBetween(DB::raw('DATE(services."updatedDateTime")'), [$startDate, $finishDate_]) // Filtrar por rango de fechas
            ->groupBy(DB::raw('CONCAT(COALESCE(internal_clients."firstName", \'\'), \' \', COALESCE(internal_clients."lastName", \'\'))')) // Agrupar solo por cliente
            ->orderBy(DB::raw('COUNT(services."clientId")'), 'desc') // Ordenar por cantidad de servicios
            ->get();
            //dd($servicesInt);
            //dd($servicesInt->toSql(), $servicesInt->getBindings());

            Log::info('Respuesta final: ', [
                'servicesExternal' => $servicesExt,
                'servicesInternal' => $servicesInt,
            ]);
            return [
                'servicesExternal' => $servicesExt->map(function ($service) {
                    return [
                        'fullName' => $service->fullName,
                        //'date' => $service->date,
                        'servicecount' => $service->servicecount,
                        'clienttype' => $service->clienttype,
                    ];
                })->toArray(),
                'servicesInternal' => $servicesInt->map(function ($service) {
                    return [
                        'fullName' => $service->fullName ?? 'Sin nombre', // Usa "fullname" en minúsculas, como aparece en el dd.
                        //'date' => $service->date,
                        'servicecount' => $service->servicecount,
                        'clienttype' => $service->clienttype,
                    ];
                })->toArray(),
            ];
    }*/


    public function quantityClient($root, array $args){
        $technicialId = $args['id_technician']['id'];
        $agenda = Agenda_Tecnico::where('technicianId',$technicialId)->first();

        $detailAgenda = Detalle_Agenda_Tecnico::where('agendaTechnicalId',$agenda->id)
        ->select('clientId', 'typeClient')
        ->get();
        $filteredExternalClients =  Asociacion_Cliente_Tecnico::where('technicalId', $technicialId)
                ->where('status', 1);
        $clienteExternoCount = $filteredExternalClients->count();
        //dd($clienteExternoCount);
        $clienteInternoCount = $detailAgenda->where('typeClient', 1)
        ->unique('clientId')
        ->count();
        //dd($clienteInternoCount);
        $clientSum = $clienteExternoCount + $clienteInternoCount;

        return [
            'message' => 'Total de clientes de tecnico',
            'quantity' => $clientSum
        ];
    }

    public function quantityCities($root, array $args){
        $technicialId = $args['id_technician'];
        $agenda = Agenda_Tecnico::where('technicianId',$technicialId)->first();

        $detailAgenda = Detalle_Agenda_Tecnico::join('services','detail_technical_agenda.serviceId','=','services.id')
        ->where('detail_technical_agenda.agendaTechnicalId',$agenda->id)
        ->where('services.status',1)
        ->whereNotNull('detail_technical_agenda.serviceDate')
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

    public function clientInternalAcept($root,array $args){
        try{
            $clietn_id  = $args['id_client'];
            $request = Solicitud::join('state_types','requests.stateId','=','state_types.id')
            ->join('technicians','requests.technicianId','=','technicians.id')
            ->where('clientId',$clietn_id)
            ->where('stateId',2)
            ->select(
                'requests.id As id_request',
                'requests.registrationDateTime As date',
                'requests.titleRequests As title',
                'requests.requestDescription As description',
                'state_types.description AS state_name',
                DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) As full_name'))
            ->get();
            //dd($request);
            $content = $request->map(function ($response) {
                return [
                    'id_request' => $response->id_request,
                    'date' => $response->date,
                    'title' => $response->title,
                    'description' => $response->description,
                    'state_name' => $response->state_name, // Renombrado para evitar ambigüedades
                    'full_name' => $response->full_name
                ];
            });

            return [
                'message' => 'Solicitudes obtenidas correctamente.',
                'contentClient' => $content
            ];

        } catch (\Exception $e) {
            return [
                'message' => 'Fallas en la base de datos :'.$e->getMessage()
            ];
        }
    }

    public function getListClientsInterns($root,$args){
        try{
            $client=Cliente_Interno::join('cities','internal_clients.cityId','=','cities.id')
            ->select(
                    DB::raw('CONCAT(COALESCE(internal_clients."firstName", \'\'), \' \', COALESCE(internal_clients."lastName", \'\')) As full_name')
                    ,'internal_clients.*',
                    'cities.name as name_city'
                    )
            ->get();

            $content = $client->map(function($clients){
                return [
                    'id_clientInternal' => $clients->id,
                    'full_name' => $clients->full_name,
                    'phoneNumber' => $clients->phoneNumber,
                    'email' => $clients->email,
                    'loginMethod'=>$clients->loginMethod,
                    'photo'=>$clients->photo,
                    'city_name'=>$clients->name_city
                ];
            });

            return [
                'message' => 'Listado exitoso de clientes',
                'result' => true,
                'clients_content' => $content
            ];
        } catch(\Exception $e){
            return [
                'message' => 'Fallas al momento del consumo: ' . $e->getMessage(),
                'result' => false,
                'clients_content' => []
            ];
        }
    }

    public function searchClientInterno($root,$args){
        try{
            $searchData = $args['requestSearchData'];
            $parameterSearch=$searchData['parameterSearch'];

            $query=Cliente_Interno::join('cities','internal_clients.cityId','=','cities.id')
                    ->select(
                        DB::raw('CONCAT(COALESCE(internal_clients."firstName", \'\'), \' \', COALESCE(internal_clients."lastName", \'\')) as "full_name"') // Nombre completo
                        ,'internal_clients.*',
                        'internal_clients.id As id_clientInternal',
                        'cities.name as city_name'
                    );

            if (!empty($parameterSearch)) {
                $query->where(function ($q) use ($parameterSearch) {
                    $q->where('cities.name', 'ILIKE', '%' . $parameterSearch . '%')
                        ->orWhere('internal_clients.phoneNumber', 'ILIKE', '%' . $parameterSearch . '%')
                        ->orWhere('internal_clients.email', 'ILIKE', '%' . $parameterSearch . '%')
                        ->orWhereRaw("CONCAT(internal_clients.\"firstName\", ' ', internal_clients.\"lastName\") ILIKE ?", ['%' . $parameterSearch . '%']);
                });
            }

            $client = $query->get();

            if ($client->isEmpty()) {
                return [
                    'message' => 'No se encontraron técnicos con los filtros aplicados',
                    'result' => false,
                    'clients_content' => []
                ];
            }

            return[
                'message'=>'Clientes encontrados con los filtros aplicados',
                'result'=> true,
                'clients_content'=>$client
            ];
        } catch(\Exception $e ){
            return [
                'message' => 'Fallas al momento del consumo: '. $e->getMessage(),
                'result' => false,
                'clients_content' => null
            ];
        }
    }

    public function listRequestByClient($root,array $args){
        try{
            //$now=Carbon::now()->format('Y-m-d');
            $id_client = $args['id_client'];
            $stateId=$args['id_state'] ?? "";

            $latestStateSubquery = DB::table('state_reference as sr')
                ->select('sr.requestId', DB::raw('MAX(sr."dateCreate") as latest_date'))
                ->groupBy('sr.requestId');

            $request = Solicitud::where('requests.clientId',$id_client)
                                ->leftJoin('technicians','requests.technicianId','=','technicians.id')
                                ->leftJoinSub($latestStateSubquery, 'latest_state', function ($join) {
                                    $join->on('requests.id', '=', 'latest_state.requestId');
                                })
                                ->leftJoin('state_reference', function ($join) {
                                    $join->on('state_reference.requestId', '=', 'latest_state.requestId')
                                        ->on('state_reference.dateCreate', '=', 'latest_state.latest_date');
                                })
                                ->select([
                                        'requests.id As id_requests',
                                        'requests.titleRequests',
                                        'requests.requestDescription',
                                        'requests.serviceLocation',
                                        'requests.latitude',
                                        'requests.longitude',
                                        'state_reference.observations',
                                        'technicians.photo',
                                        'requests.reference_phone',
                                        'requests.registrationDateTime',
                                        DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) As full_name')
                                    ]);
            if (!empty($stateId)) {
                $request->where('requests.stateId', $stateId)
                        ->where('state_reference.stateId',$stateId);
            }

            $requests= $request->distinct()->get();

            $content = $requests->map( function($req) use ($stateId) {
                return [//
                    'id_requests'=>$req->id_requests,
                    'titleRequests'=>$req->titleRequests,
                    'requestDescription'=>$req->requestDescription,
                    'serviceLocation'=>$req->serviceLocation,
                    'latitude'=>$req->latitude,
                    'longitude'=>$req->longitude,
                    'full_name'=>$req->full_name,
                    'photo'=>$req->photo,
                    'reference_phone'=>$req->reference_phone,
                    'state'=>$req->state,
                    'date' => $req->registrationDateTime,
                    'observations' => $req->observations,
                ];
            });

            return [
                'message' => 'Listado de las solicitudes.',
                'count' => count($requests),
                'requests' => $content
            ];

        } catch(\Exception $e) {
            return [
                'message' => 'Las fallas son las siguientes: ' . $e->getMessage(),
                'count' => 0 ,
                'requests' => []
            ];
        }
    }

    public function dataClient($root,array $args){
        try{
            $clientId = $args['id'];
            $clientData = Cliente_Interno::where('id',$clientId)
                        ->select(
                            'internal_clients.id',
                            'internal_clients.firstName',
                            'internal_clients.lastName',
                            'internal_clients.phoneNumber',
                            'internal_clients.email'
                        )
                        ->first();
            return [
                'message' => 'Envio de datos exitoso',
                'client_int' => $clientData
            ];
        }catch(\Exception $e){
            return [
                'message' => 'Se presento las siguientes fallas: ' . $e->getMessage()
            ];
        }
    }

    public function clientCont(){
        try{
            $content = [
                'clientAll' => Cliente_Interno::count(),
                'clientActive' => Cliente_Interno::where('status',1)->count(),
                'clientLow' => Cliente_Interno::where('status',0)->count(),
            ];
            //dd($content);
            return [
                'message' => 'conteo exitoso!',
                'cont' => $content
            ];
        }catch(\Exception $e){
            return [
               'message' => 'Se presento las siguientes fallas: ' . $e->getMessage()
            ];
        }
    }
}
