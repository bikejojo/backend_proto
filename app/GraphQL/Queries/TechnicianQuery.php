<?php

namespace App\GraphQL\Queries;

use App\Models\Ciudad;
use App\Models\Tecnico;
use App\Models\User;
use App\Models\Technician_subcripcion;
use App\Models\Suscripcion;
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

    public function get_technician($root,array $args){
        try {
            $technician = Tecnico::join('users','technicians.userId','=','users.id')
                            ->join('cities','technicians.cityId','=','cities.id')
                            ->join('technician_subcription','technicians.id','=','technician_subcription.technicianId')
                            ->join('subcriptions','technician_subcription.subcriptionsId','=','subcriptions.id')
                            ->select(
                                'users.ci As ci_tech','technicians.cityId As city_id',DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) As full_name'),'technicians.phoneNumber',
                                'technicians.id As id_technician','technicians.phoneNumber As phone','technicians.email As email_tech','technicians.frontIdCard As frontCard','technicians.backIdCard As backCard','technicians.photo As photoCard',
                                'cities.name As name_city','subcriptions.codeSubcription As code_sub','technician_subcription.id As id_sub_tech',
                                'technician_subcription.status As status_sub','technician_subcription.starDateSubcription As startDate','technician_subcription.endDateSubcription As endDate'
                            )
                            ->orderBy('id_technician','ASC')
                            ->get();
            if($technician->isEmpty()){
                return [
                    'message' =>'No existen tecnicos',
                    'status' => 2,
                    'technicians'  => []
                ];
            }
            //dd($technician);
            $content = $technician->map(function ($technician) {
                return [
                    'id_technician' => $technician->id_technician,
                    'full_name'=>$technician->full_name,
                    'frontCard'=>$technician->frontCard,
                    'backCard'=>$technician->backCard,
                    'phoneNumber' => $technician->phone,
                    'photoCard'=>$technician->photoCard,
                    'email'=>$technician->email_tech,
                    'ci'=>$technician->ci_tech,
                    'id_city'=>$technician->city_id,
                    'name_city'=>$technician->name_city,
                    'id_subcription_tech'=>$technician->id_sub_tech,
                    'status_sub'=>$technician->status_sub,
                    'startDate'=>$technician->startDate,
                    'endDate'=>$technician->endDate,
                    'code_sub'=>$technician->code_sub,
                ];
            });
            return [
                'message' =>'Listado de tecnicos',
                'status' => 1,
                'technicians'  => $content
            ];
        } catch (\Exception $e) {
            return [
                'message' =>'Problemas en la conexion del servidor ' . $e->getMessage(),
                'status' => 3,
                'technicians'  => null
            ];
        }
    }
}
