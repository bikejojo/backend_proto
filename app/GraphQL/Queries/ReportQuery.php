<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Tecnico;
use App\Models\Calificacion;
use App\Models\Suscripcion;
use Illuminate\Support\Facades\DB;
use App\Models\Technician_subcripcion;

class ReportQuery{
    public function reportSuscription($root,array $args){
        $input = $args['Input'];
        $startDate = $input['startDate'] ?? null;
        $finishDate = $input['finishDate'] ?? null;
        $type = $input['type'] ?? null;
        $status = isset($input['status']) ? $input['status'] : false;
        //dd($status);
        if(!$status){
            $content = Technician_subcripcion::leftJoin('subcriptions','technician_subcription.subcriptionsId','=','subcriptions.id')
                                        ->select(
                                            'subcriptions.name As name_sub',
                                            'subcriptions.status As state_sub',
                                            DB::raw('COUNT(technician_subcription."technicianId") as technician_count '),
                                        )
                                        ->groupBy('subcriptions.name','subcriptions.status')
                                        ->get();
            return [
                'message' => 'Resulta de reporte True',
                'content' => $content->map(function ($item) {
                    return [
                        'name_subs' => $item->name_sub,
                        'state_sub' => $item->state_sub,
                        'count_sub' => $item->technician_count
                    ];
                })->toArray()
            ];
        }else{
            $content = Technician_subcripcion::leftJoin('subcriptions','technician_subcription.subcriptionsId','=','subcriptions.id')
                                        ->leftJoin('technicians','technician_subcription.technicianId','=','technicians.id')
                                        ->select(
                                            'subcriptions.name As name_sub',
                                            DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) AS "fullName_technician"'),
                                            'technician_subcription.status As state_TechSub',
                                            'technician_subcription.starDateSubcription As startSub',
                                            'technician_subcription.endDateSubcription As endSub'
                                        )
                                        ->get();
        }
            $contentGroup = $content->groupBy('name_sub');        //dd($content);
        return [
            'message' => 'Resulta de reporte True',
            'technicianData' => $contentGroup->map(function ($item , $nameSub) {
                return [
                    'name_sub' => $nameSub,
                    'type_report' => $item->map(function ($item) {
                        return [
                            'fullName_technician' => $item->fullName_technician,
                            'state_TechSub' => $item->state_TechSub,
                            'startSub' => $item->startSub,
                            'endSub' => $item->endSub,
                        ];
                    })
                ];
            })->toArray()
        ];
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
