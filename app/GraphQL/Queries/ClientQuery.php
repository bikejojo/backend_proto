<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Agenda_Tecnico;
use App\Models\Cliente_Externo;
use App\Models\Cliente_Interno;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Servicio;
use App\Models\Solicitud;
use App\Models\Historial_Servicios;
use App\Models\Tecnico;
use App\Models\Lists_Internal_Client;
use Illuminate\Support\Facades\DB;

class ClientQuery{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function searchExternalByName($root, array $args)
    {
        $clientData = $args['requestClient'];
        $tecnicoId = $clientData['technicalId'];

        // Verificar si existe el parámetro de búsqueda
        if (!empty($clientData['searchParameter'])) {
            $clientNamePhone = strtolower($clientData['searchParameter']);

            // Aplicar el filtro por nombre o teléfono y también filtrar por estado
            $clientExterno = Cliente_Externo::whereHas('associantions', function ($query) use ($tecnicoId) {
                $query->where('technicalId', $tecnicoId);  // Filtrar por ID del técnico
            })
            ->where(function($query) use ($clientNamePhone) {
                $query->where(DB::raw('LOWER(external_clients."fullName")'), 'LIKE', "%{$clientNamePhone}%")
                      ->orWhere(DB::raw('LOWER(external_clients."phoneNumber")'), 'LIKE', "%{$clientNamePhone}%");
            })
            ->where('external_clients.status', 1)
            ->orderBy('external_clients.created_at', 'desc')  // Filtrar solo por clientes con estado 1
            ->get();
        } else {
            // Si no hay parámetro de búsqueda, solo aplicar el filtro del técnico y estado
            $clientExterno = Cliente_Externo::whereHas('associantions', function ($query) use ($tecnicoId) {
                $query->where('technicalId', $tecnicoId);  // Filtrar por ID del técnico
            })
            ->where('external_clients.status', 1)
            ->orderBy('external_clients.created_at', 'desc')  // Filtrar solo por clientes con estado 1
            ->get();
        }

        // Verificar si no se encontraron resultados
        if ($clientExterno->isEmpty()) {
            return [
                'message' => 'No se encontraron resultados',
                'customer_internal' => null
            ];
        }

        // Retornar los resultados encontrados
        return [
            'message' => 'Resultados encontrados',
            'customer_external' => $clientExterno
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
        ->where('status',1)
        ->leftjoin('external_clients','external_clients.id','=','clientId')
        ->orderBy('external_clients.created_at', 'desc')
        ->get();
        if($listado->isEmpty()){
            return[
                'message' => 'El tecnico no tiene una lista de clientes propios',
                'customer_external' => null
            ];
        }

        return[
            'message' => 'Clientes propios de los tecnicos',
            'customer_external' => $listado
        ];
    }

    public function quantifyclient($root , array $args){
        $clientData = $args['requestClient'];
        $startDate = $clientData['startDate'];
        $finishDate = $clientData['finishDate'];

        $servicesExt = DB::table('services')
            ->where('typeClient','2')
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
        //dd($servicesInt);
        return [
            'servicesExternal'=> $servicesExt,
            'servicesInternal' => $servicesInt
        ];
    }

    public function quantityClient($root, array $args){
        $technicialId = $args['id_technician']['id'];
        $agenda = Agenda_Tecnico::where('technicianId',$technicialId)->first();
        //dd($agenda);
        $detailAgenda = Detalle_Agenda_Tecnico::where('agendaTechnicalId',$agenda->id)
        ->select('clientId', 'typeClient')
        //->distinct()
        ->get();
        $clienteExternoCount = $detailAgenda->where('typeClient', 2)
        ->count();
        $clienteInternoCount = $detailAgenda->where('typeClient', 1)
        ->count();
        $clientSum = $clienteExternoCount + $clienteInternoCount;
        //dd($clientSum);
        return [
            'message' => 'Total de clientes de tecnico',
            'quantity' => $clientSum
        ];
    }

    public function quantityCities($root, array $args){
        $technicialId = $args['id_technician'];
        $agenda = Agenda_Tecnico::where('technicianId',$technicialId)->first();
        //dd($agenda);
        $detailAgenda = Detalle_Agenda_Tecnico::where('agendaTechnicalId',$agenda->id)
        ->whereNotNull('serviceDate')
        ->count();
        return [
            'message' => 'Total de citas programas de tecnico',
            'quantity' => $detailAgenda
        ];
    }

    public function list_requests_services($root,array $args){
        $list = $args['clientRequest'];
        $clientId=$list['id_client'];
        // Obtener las solicitudes relacionadas
        $history_solic = DB::table('service_client_history')
            ->join('requests', 'service_client_history.jobId', '=', 'requests.id')
            ->where('service_client_history.clientId', $clientId)
            ->where('service_client_history.descriptionJob', 1)
            ->get();

        // Obtener los servicios relacionados
        $history_serv = DB::table('service_client_history')
            ->join('services', 'service_client_history.jobId', '=', 'services.id')
            ->where('service_client_history.clientId', $clientId)
            ->where('service_client_history.descriptionJob', 2)
            ->get();
        $technician = DB::table('technicians')
        ->join('service_client_history','service_client_history.technicianId','=','technicians.id')
        ->select('technicians.*')
        ->distinct()
        ->get();
        $client = Cliente_Interno::where('id',$clientId)->first();
        dd($technician);
    }
}
