<?php

namespace App\GraphQL\Queries;

use App\Models\Ciudad;
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
}
