<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Tecnico;
use App\Models\Calificacion;
use App\Models\Suscripcion;
use Illuminate\Support\Facades\DB;
use App\Models\Technician_subcripcion;

class ReportQuery{
    public function reportSuscription($root,array $args){
        $startDate = $args['input']['startDate'] ?? null;
        $finishDate = $args['input']['finishDate'] ?? null;
        $type = $args['input']['type'] ?? null;
        $satus = $args['input']['status'] ?? null;

        $content = Technician_subcripcion::join('subcriptions','technician_subcription.subcriptionsId','=','subcriptions.id')
                                        ->join('technicians','technician_subcription.technicianId','=','technicians.id')
                                        //->join('cities','technicians.cityId','=','cities.id')
                                        ->select(
                                            'subcriptions.id As subcriptionsId',
                                            'subcriptions.name As nombreSusc',
                                            'subcriptions.status As stadoSuscrip',
                                            DB::raw('COUNT(technician_subcription."technicianId") As cont_Tech')
                                        )
                                        ->groupBy(
                                            'subcriptions.id',
                                            'subcriptions.name',
                                            'subcriptions.status',
                                        )
                                        ->get();
        dd($content);
    }

    public function reportTechnician($root,array $args){
        $startDate = $args['input']['startDate'] ?? null;
        $finishDate = $args['input']['finishDate'] ?? null;
        $type = $args['input']['type'] ?? null;
        $satus = $args['input']['status'] ?? null;
    }

    public function reportClient($root,array $args){
        $startDate = $args['input']['startDate'] ?? null;
        $finishDate = $args['input']['finishDate'] ?? null;
        $type = $args['input']['type'] ?? null;
        $satus = $args['input']['status'] ?? null;
    }
}
