<?php

namespace App\GraphQL\Queries;

use App\Models\Technician_subcripcion;
use App\Models\Tecnico;

use Illuminate\Support\Facades\DB;

class TechnicianQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function verifactionCityTechnician($root , array $args){
        try {
            DB::beginTransaction();
            $id_ciudad = $args['id_city'];
            $verfication = Tecnico::where('cityId',$id_ciudad)->get();
            //dd($verfication);
            if(!$verfication->isEmpty()){
                return[
                    'message' =>'Existen tecnicos en la ciudad',
                    'value' => 1
                ];
            }else{
                return[
                    'message' =>'No existen tecnicos en la ciudad',
                    'value' => 2
                ];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'message' => 'Fallas internas en la base de datos' . $e->getMessage(),
                'value' => 3
            ];
        }

    }

    public function get_technician($root, array $args)
    {
        try {
            // Subconsulta para obtener la última suscripción de cada técnico
            $latestSubscription = DB::table('technician_subcription as ts1')
                ->select('ts1.technicianId', DB::raw('MAX(ts1."endDateSubcription") as last_end_date'))
                ->groupBy('ts1.technicianId');

            $technician = Tecnico::join('users', 'technicians.userId', '=', 'users.id')
                ->join('cities', 'technicians.cityId', '=', 'cities.id')
                ->joinSub($latestSubscription, 'latest_sub', function ($join) {
                    $join->on('technicians.id', '=', 'latest_sub.technicianId');
                })
                ->join('technician_subcription', function ($join) {
                    $join->on('technicians.id', '=', 'technician_subcription.technicianId')
                        ->on('technician_subcription.endDateSubcription', '=', 'latest_sub.last_end_date');
                })
                ->join('subcriptions', 'technician_subcription.subcriptionsId', '=', 'subcriptions.id')
                ->select(
                    'users.ci AS ci_tech',
                    'technicians.cityId AS city_id',
                    DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) AS full_name'),
                    'technicians.phoneNumber',
                    'technicians.id AS id_technician',
                    'technicians.phoneNumber AS phone',
                    'technicians.email AS email_tech',
                    'technicians.frontIdCard AS frontCard',
                    'technicians.backIdCard AS backCard',
                    'technicians.photo AS photoCard',
                    'cities.name AS name_city',
                    'subcriptions.codeSubcription AS code_sub',
                    'technician_subcription.id AS id_sub_tech',
                    'technician_subcription.status AS status_sub',
                    'technician_subcription.starDateSubcription AS startDate',
                    'technician_subcription.endDateSubcription AS endDate'
                )
                ->orderBy('id_technician', 'ASC')
                ->get();

            if ($technician->isEmpty()) {
                return [
                    'message' => 'No existen técnicos',
                    'status' => 2,
                    'technicians' => []
                ];
            }

            $content = $technician->map(function ($technician) {
                return [
                    'id_technician' => $technician->id_technician,
                    'full_name' => $technician->full_name,
                    'frontCard' => $technician->frontCard,
                    'backCard' => $technician->backCard,
                    'phoneNumber' => $technician->phone,
                    'photoCard' => $technician->photoCard,
                    'email' => $technician->email_tech,
                    'ci' => $technician->ci_tech,
                    'id_city' => $technician->city_id,
                    'name_city' => $technician->name_city,
                    'id_subcription_tech' => $technician->id_sub_tech,
                    'status_sub' => $technician->status_sub,
                    'startDate' => $technician->startDate,
                    'endDate' => $technician->endDate,
                    'code_sub' => $technician->code_sub,
                ];
            });

            return [
                'message' => 'Listado de técnicos',
                'status' => 1,
                'technicians' => $content
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'Problemas en la conexión del servidor ' . $e->getMessage(),
                'status' => 3,
                'technicians' => null
            ];
        }
    }

    public function filterListTechnician($root,array $args){
            try {
                $searchData = $args['requestFilterTechnician'];
                $searchParameter = $searchData['searchParameter'] ?? null;
                //$ci = $searchData['ci'];
                //$phoneNumber = $searchData['phoneNumber'];
                $code_sub = $searchData['code_sub'];
            $latestSubscription = DB::table('technician_subcription as ts1')
            ->select('ts1.technicianId', DB::raw('MAX(ts1."endDateSubcription") as last_end_date'))
            ->groupBy('ts1.technicianId');
            $query = Tecnico::join('users', 'technicians.userId', '=', 'users.id')
            ->join('cities', 'technicians.cityId', '=', 'cities.id')
            ->joinSub($latestSubscription, 'latest_sub', function ($join) {
                $join->on('technicians.id', '=', 'latest_sub.technicianId');
            })
            ->join('technician_subcription', function ($join) {
                $join->on('technicians.id', '=', 'technician_subcription.technicianId')
                     ->on('technician_subcription.endDateSubcription', '=', 'latest_sub.last_end_date');
            })
            ->join('subcriptions', 'technician_subcription.subcriptionsId', '=', 'subcriptions.id')
            //->where('technician_subcription.status', 1)
            ->select(
                'users.ci AS ci_tech',
                'technicians.cityId AS city_id',
                DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) AS full_name'),
                'technicians.phoneNumber AS phone',
                'technicians.id AS id_technician',
                'technicians.email AS email_tech',
                'cities.name AS name_city',
                'technician_subcription.status AS status_sub',
                'subcriptions.codeSubcription AS code_sub',
                'technician_subcription.starDateSubcription AS startDate',
                'technician_subcription.endDateSubcription AS endDate'
            );

        // Aplicar filtros solo si existen valores


        if (!empty($code_sub)) {
            $query->where('subcriptions.codeSubcription', 'ILIKE','%'. $code_sub.'%');
        }
        if (!empty($searchParameter)) {
            $query->where(function ($q) use ($searchParameter) {
                $q->where('users.ci', 'LIKE', '%' . $searchParameter . '%')
                    ->orWhereRaw("CONCAT(technicians.\"firstName\", ' ', technicians.\"lastName\") ILIKE ?", ['%' . $searchParameter . '%'])
                    ->orWhere('technicians.phoneNumber', 'LIKE', '%' . $searchParameter . '%')
                    ->orWhere('users.ci', 'LIKE', '%' . $searchParameter . '%');
            });
        }

        // Obtener resultados
        $technicians = $query->orderBy('id_technician', 'ASC')->get();

        if ($technicians->isEmpty()) {
            return [
                'message' => 'No se encontraron técnicos con los filtros aplicados',
                'status' => 2,
                'technicians' => []
            ];
        }

        // Formatear la respuesta
        $content = $technicians->map(function ($technician) {
            return [
                'id_technician' => $technician->id_technician,
                'full_name' => $technician->full_name,
                'phoneNumber' => $technician->phone,
                'email' => $technician->email_tech,
                'ci' => $technician->ci_tech,
                'id_city' => $technician->city_id,
                'name_city' => $technician->name_city,
                'status_sub' => $technician->status_sub,
                'startDate' => $technician->startDate,
                'endDate' => $technician->endDate,
                'code_sub' => $technician->code_sub,
            ];
        });

        return [
            'message' => 'Resultados de búsqueda',
            'status' => 1,
            'technicians' => $content
        ];
        } catch (\Exception $e) {
        return [
            'message' => 'Error en la consulta: ' . $e->getMessage(),
            'status' => 3,
            'technicians' => null
        ];
        }
    }

    public function recordSubcriptionTechnician($root,array $args){
        try{
            $technicianId=$args['id_technician'];
            $suscriptionTech=Technician_subcripcion::join('subcriptions','technician_subcription.subcriptionsId','=','subcriptions.id')
                                                    ->where('technicianId',$technicianId)
                                                    ->select(
                                                        'technician_subcription.id As id_subcription',
                                                        'subcriptions.price As price_subcription',
                                                        'technician_subcription.starDateSubcription As startSubcription',
                                                        'technician_subcription.endDateSubcription As endSubcription',
                                                        'technician_subcription.status As statusSubcription',
                                                        'subcriptions.description As description_subcription',
                                                    )
                                                    ->orderBy('technician_subcription.starDateSubcription', 'DESC')
                                                    ->get();
            if($suscriptionTech->isEmpty()){
                return [
                    'message' => 'No existen suscripciones.',
                    'suscriptionTech' => []
                ];
            }
            return [
                'message' => 'Historial de suscripciones.',
                'suscriptionTech' => $suscriptionTech
            ];
        } catch(\Exception $e){
            return [
                'message' => 'Se presento la siguiente falla: '. $e->getMessage(),
                'suscriptionTech' => null
            ];
        }
    }
}
