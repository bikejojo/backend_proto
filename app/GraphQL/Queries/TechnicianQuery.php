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
                                'users.ci','cities.name',DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) As full_name'),'technicians.phoneNumber',
                                'technicians.email','technicians.frontIdCard','technicians.backIdCard','technicians.photo',
                                'cities.name','subcriptions.name','subcriptions.codeSubcription',
                                'technician_subcription.status','technician_subcription.starDateSubcription','technician_subcription.endDateSubcription'
                            )
                            ->get();
        } catch (\Exception $e) {
            return [
                'message' =>'No existen tecnicos en la ' . $e->getMessage(),
                'status' => 3,
                'technicians'  => []
            ];
        }
    }
}
